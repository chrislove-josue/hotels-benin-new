<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaPayTransaction;
use FedaPay\Webhook as FedaPayWebhook;

class TransactionController extends Controller
{
    public function __construct()
    {
        FedaPay::setApiKey(config('services.fedapay.secret_key'));
        FedaPay::setEnvironment(config('services.fedapay.environment'));
    }

    /**
     * Page de paiement pour une réservation en attente.
     */
    public function checkout(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if ($reservation->transaction && $reservation->transaction->status === 'reussie') {
            return redirect()->route('reservations.show', $reservation)
                ->with('status', 'Cette réservation est déjà payée.');
        }

        if ($reservation->room && $reservation->room->isUnavailableFor(
            $reservation->check_in->toDateString(),
            $reservation->check_out->toDateString(),
            $reservation->id
        )) {
            return $this->cancelUnavailable($reservation);
        }

        $reservation->load(['establishment', 'room', 'transaction']);

        $availableUnits = $reservation->room
            ? $reservation->room->availableUnitsFor(
                $reservation->check_in->toDateString(),
                $reservation->check_out->toDateString(),
                $reservation->id
            )
            : 0;

        return view('reservations.checkout', compact('reservation', 'availableUnits'));
    }

    /**
     * Traite le paiement : redirige systématiquement vers FedaPay.
     */
    public function process(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if (! in_array($reservation->status, ['en_attente'], true)) {
            return redirect()->route('reservations.show', $reservation)
                ->with('status', 'Cette réservation ne peut plus être payée.');
        }

        if ($reservation->room && $reservation->room->isUnavailableFor(
            $reservation->check_in->toDateString(),
            $reservation->check_out->toDateString(),
            $reservation->id
        )) {
            return $this->cancelUnavailable($reservation);
        }

        return $this->payWithFedaPay($reservation);
    }

    /**
     * Crée une transaction FedaPay et redirige le client vers la page de paiement hébergée.
     */
    private function payWithFedaPay(Reservation $reservation)
    {
        $user = Auth::user();

        try {
            $fedapayTransaction = FedaPayTransaction::create([
                'description'  => "Réservation {$reservation->reference} — {$reservation->establishment->name}",
                'amount'       => (int) round((float) $reservation->amount), // XOF = pas de décimales
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('reservations.fedapay.callback', $reservation),
                'customer'     => [
                    'firstname' => $user->name ?? 'Client',
                    'lastname'  => '—',
                    'email'     => $user->email,
                ],
            ]);

            $token = $fedapayTransaction->generateToken();

            Transaction::whereKey($reservation->transaction?->getKey())->update([
                'method'                  => 'fedapay',
                'provider_transaction_id' => $fedapayTransaction->id,
                'status'                  => 'en_attente',
            ]);

            $reservation->update(['payment_method' => 'fedapay']);

            return redirect()->away($token->url);
        } catch (\Throwable $e) {
            Log::error('Erreur création transaction FedaPay', [
                'reservation' => $reservation->reference,
                'error'       => $e->getMessage(),
                'http'        => method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : null,
                'body'        => method_exists($e, 'getJsonBody') ? $e->getJsonBody() : null,
            ]);

            return back()->withErrors([
                'fedapay' => $this->humanFedaPayError($e),
            ]);
        }
    }

    /**
     * Traduit une erreur du SDK/API FedaPay en message compréhensible.
     */
    private function humanFedaPayError(\Throwable $e): string
    {
        $fallback = "Impossible de contacter FedaPay pour le moment. Réessayez dans un instant.";

        $body = method_exists($e, 'getJsonBody') ? $e->getJsonBody() : null;
        $errors = is_array($body) ? ($body['errors'] ?? null) : null;

        if (is_array($errors) && $errors !== []) {
            $details = collect($errors)
                ->map(fn ($v) => is_array($v) ? implode(' ', $v) : (string) $v)
                ->flatten()
                ->filter()
                ->implode(' ');

            if ($details !== '') {
                return "Le paiement n'a pas pu être initialisé : {$details}";
            }
        }

        return $fallback;
    }

    /**
     * Retour du client après paiement FedaPay (callback_url).
     *
     * Sécurité : seule la transaction liée à CETTE réservation est vérifiée.
     * Le paramètre ?id= de l'URL n'est jamais utilisé pour pointer une autre
     * transaction du compte marchand.
     */
    public function fedapayCallback(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $ownTransactionId = $reservation->transaction?->provider_transaction_id;

        if (! $ownTransactionId) {
            return redirect()->route('reservations.show', $reservation)
                ->with('status', 'Statut de paiement inconnu, veuillez vérifier votre réservation.');
        }

        try {
            $fedapayTransaction = FedaPayTransaction::retrieve($ownTransactionId);
            $this->applyFedaPayStatus($reservation, $fedapayTransaction->status ?? '', $fedapayTransaction);
        } catch (\Throwable $e) {
            Log::error('Erreur vérification transaction FedaPay', ['error' => $e->getMessage()]);
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('status', 'Merci de vérifier ci-dessous le statut de votre paiement.');
    }

    /**
     * Webhook FedaPay : source de vérité pour la confirmation (asynchrone, signée).
     */
    public function fedapayWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('X-FEDAPAY-SIGNATURE');
        $secret = config('services.fedapay.webhook_secret');

        try {
            $event = FedaPayWebhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Payload invalide', 400);
        } catch (\FedaPay\Error\SignatureVerification $e) {
            return response('Signature invalide', 400);
        }

        $entity = $event->entity ?? $event->object ?? null;
        $providerTransactionId = $entity->id ?? null;

        if (! $providerTransactionId) {
            return response('OK', 200);
        }

        $transaction = Transaction::where('provider_transaction_id', $providerTransactionId)->first();

        if (! $transaction || ! $transaction->reservation) {
            return response('OK', 200);
        }

        match ($event->name) {
            'transaction.approved' => $this->applyFedaPayStatus($transaction->reservation, 'approved', $entity),
            'transaction.canceled', 'transaction.declined' => $this->applyFedaPayStatus($transaction->reservation, 'canceled'),
            default => null,
        };

        return response('OK', 200);
    }

    /**
     * Applique le statut FedaPay à la réservation et à sa transaction locale.
     *
     * - approved : vérifie le montant attendu avant de confirmer, puis envoie
     *   l'e-mail de confirmation au voyageur.
     * - canceled / declined : la réservation reste « en_attente » afin que le
     *   voyageur puisse relancer un paiement depuis sa réservation.
     */
    private function applyFedaPayStatus(Reservation $reservation, string $status, $fedapayTransaction = null): void
    {
        $transaction = $reservation->transaction;
        if (! $transaction || $transaction->status === 'reussie') {
            return;
        }

        if ($status === 'approved') {
            $expectedAmount = (int) round((float) $reservation->amount);
            $paidAmount = isset($fedapayTransaction->amount) ? (int) $fedapayTransaction->amount : $expectedAmount;

            if ($paidAmount < $expectedAmount) {
                Log::warning('Montant FedaPay inférieur au montant attendu', [
                    'reservation' => $reservation->reference,
                    'attendu'     => $expectedAmount,
                    'payé'        => $paidAmount,
                ]);
                $transaction->update(['status' => 'echouee']);
                return;
            }

            $transaction->update(['status' => 'reussie']);
            $reservation->update(['status' => 'confirmee']);
            $this->sendConfirmationEmail($reservation);
        } elseif (in_array($status, ['canceled', 'declined'], true)) {
            $transaction->update(['status' => 'echouee']);
            // La réservation reste en_attente : paiement relançable depuis la fiche.
        }
    }

    /**
     * E-mail de confirmation envoyé au voyageur après paiement réussi.
     * L'échec d'envoi ne doit jamais casser le flux de paiement.
     */
    private function sendConfirmationEmail(Reservation $reservation): void
    {
        try {
            $reservation->loadMissing(['establishment', 'room', 'user']);

            Mail::raw(
                "Bonjour {$reservation->user->name},\n\n"
                . "Votre paiement a été reçu et votre réservation est confirmée.\n\n"
                . "Référence : {$reservation->reference}\n"
                . "Établissement : {$reservation->establishment->name}\n"
                . "Chambre : {$reservation->room?->name}\n"
                . "Séjour : {$reservation->check_in->format('d/m/Y')} → {$reservation->check_out->format('d/m/Y')} "
                . "({$reservation->nights()} nuit(s))\n"
                . "Voyageurs : {$reservation->guests}\n"
                . "Montant payé : " . number_format((float) $reservation->amount, 0, ',', ' ') . " FCFA\n\n"
                . "Merci de votre confiance,\nL'équipe Hôtels Bénin",
                function ($message) use ($reservation) {
                    $message->to($reservation->user->email)
                        ->subject("Confirmation de réservation {$reservation->reference} — Hôtels Bénin");
                }
            );
        } catch (\Throwable $e) {
            Log::error('Envoi e-mail de confirmation échoué', [
                'reservation' => $reservation->reference,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    private function cancelUnavailable(Reservation $reservation)
    {
        $reservation->update(['status' => 'annulee']);

        if ($reservation->transaction && $reservation->transaction->status !== 'reussie') {
            $reservation->transaction->update(['status' => 'echouee']);
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('status', 'Désolé, la chambre n\'est plus disponible pour ces dates. Votre réservation a été annulée.');
    }
}
