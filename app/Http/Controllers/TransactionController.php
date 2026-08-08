<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Page de paiement (simulé) pour une réservation en attente.
     */
public function checkout(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        if ($reservation->transaction && $reservation->transaction->status === 'reussie') {
            return redirect()->route('reservations.show', $reservation)
                ->with('status', 'Cette réservation est déjà payée.');
        }

        // Vérifier la disponibilité de la chambre avant d'afficher le paiement.
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

        $methods = [
            'mtn_momo' => 'MTN Mobile Money',
            'moov_money' => 'Moov Money',
            'card' => 'Carte bancaire',
        ];

        return view('reservations.checkout', compact('reservation', 'methods', 'availableUnits'));
    }

    /**
     * Traite le paiement simulé : marque la transaction réussie et confirme la réservation.
     */
public function process(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        // Re-vérifier la disponibilité juste avant de confirmer (anti sur-réservation).
        if ($reservation->room && $reservation->room->isUnavailableFor(
            $reservation->check_in->toDateString(),
            $reservation->check_out->toDateString(),
            $reservation->id
        )) {
            return $this->cancelUnavailable($reservation);
        }

        $transaction = $reservation->transaction;

        // Simuler un paiement réussi (aucun prestataire réel connecté)
        $transaction->update([
            'status' => 'reussie',
            'reference' => $transaction->reference ?? 'TXN-'.strtoupper(\Illuminate\Support\Str::random(10)),
        ]);

        $reservation->update(['status' => 'confirmee']);

        return redirect()->route('reservations.show', $reservation)
            ->with('status', 'Paiement réussi ! Votre réservation est confirmée.');
    }

    /**
     * Annule une réservation lorsque la chambre n'est plus disponible sur la période.
     */
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
