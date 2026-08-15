<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
                'amount'       => (int) round($reservation->amount), // XOF = pas de décimales
                'currency'     => ['iso' => 'XOF'],
                'callback_url' => route('reservations.fedapay.callback', $reservation),
                'customer'     => [
                    'firstname' => $user->name ?? 'Client',
                    'lastname'  => '—',
                    'email'     => $user->email,
                ],
            ]);

            $token = $fedapayTransaction->generateToken();

            $reservation->transaction()->update([
                'method'                  => 'fedapay',
                'provider_transaction_id' => $fedapayTransaction->id,
                'status'                  => 'en_attente',
            ]);

            $reservation->update(['payment_method' => 'fedapay']);

            return redirect()->away($token->url);
        } catch (\Throwable $e) {
            Log::error('Erreur création transaction FedaPay', ['error' => $e->getMessage()]);

            return back()->withErrors([
                'fedapay' => "Impossible de contacter FedaPay pour le moment. Réessayez dans un instant.",
            ]);
        }
    }

    /**
     * Retour du client après paiement FedaPay (callback_url).
     */
    public function fedapayCallback(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $id = $request->query('id') ?? $reservation->transaction?->provider_transaction_id;

        if (! $id) {
            return redirect()->route('reservations.show', $reservation)
                ->with('status', 'Statut de paiement inconnu, veuillez vérifier votre réservation.');
        }

        try {
            $fedapayTransaction = FedaPayTransaction::retrieve($id);
            $this->applyFedaPayStatus($reservation, $fedapayTransaction->status);
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

        $transaction = \App\Models\Transaction::where('provider_transaction_id', $providerTransactionId)->first();

        if (! $transaction) {
            return response('OK', 200);
        }

        $reservation = $transaction->reservation;

        match ($event->name) {
            'transaction.approved' => $this->applyFedaPayStatus($reservation, 'approved'),
            'transaction.canceled', 'transaction.declined' => $this->applyFedaPayStatus($reservation, 'canceled'),
            default => null,
        };

        return response('OK', 200);
    }

    private function applyFedaPayStatus(Reservation $reservation, string $status): void
    {
        $transaction = $reservation->transaction;
        if (! $transaction || $transaction->status === 'reussie') {
            return;
        }

        if ($status === 'approved') {
            $transaction->update(['status' => 'reussie']);
            $reservation->update(['status' => 'confirmee']);
        } elseif (in_array($status, ['canceled', 'declined'])) {
            $transaction->update(['status' => 'echouee']);
            $reservation->update(['status' => 'annulee']);
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