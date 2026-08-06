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

        $reservation->load(['establishment', 'room', 'transaction']);

        $methods = [
            'mtn_momo' => 'MTN Mobile Money',
            'moov_money' => 'Moov Money',
            'card' => 'Carte bancaire',
        ];

        return view('reservations.checkout', compact('reservation', 'methods'));
    }

    /**
     * Traite le paiement simulé : marque la transaction réussie et confirme la réservation.
     */
    public function process(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

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
}
