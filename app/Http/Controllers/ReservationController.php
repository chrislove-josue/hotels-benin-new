<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Crée une réservation pour un voyageur connecté, puis l'envoie directement
     * sur la page de paiement (le choix du moyen de paiement se fait au checkout).
     *
     * La création se fait dans une transaction SQL avec verrou sur la chambre :
     * deux clients simultanés ne peuvent pas réserver la dernière unité.
     */
    public function store(Request $request, string $slug)
    {
        $establishment = Establishment::where('slug', $slug)->where('is_available', true)->firstOrFail();

        $data = $request->validate([
            'room_id'   => ['required', 'exists:rooms,id'],
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        // Vérifie l'existence de la chambre avant le verrou (404 propre).
        Room::where('id', $data['room_id'])
            ->where('establishment_id', $establishment->id)
            ->where('is_available', true)
            ->firstOrFail();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($establishment, $data) {
            $room = Room::where('id', $data['room_id'])
                ->where('establishment_id', $establishment->id)
                ->where('is_available', true)
                ->lockForUpdate()
                ->firstOrFail();

            if ($data['guests'] > $room->capacity) {
                return back()
                    ->withErrors(['guests' => "Cette chambre accepte au maximum {$room->capacity} voyageur(s)."])
                    ->withInput();
            }

            // Vérification de disponibilité sous verrou : bloquer si la chambre est complète.
            if ($room->isUnavailableFor($data['check_in'], $data['check_out'])) {
                return back()
                    ->withErrors(['room_id' => "La chambre « {$room->name} » est complète pour ces dates. Choisissez une autre période ou une autre chambre."])
                    ->withInput();
            }

            $nights = Reservation::nightsBetween($data['check_in'], $data['check_out']);
            $amount = $nights * (float) $room->price_per_night;
            $commission = round($amount * 0.10, 2); // 10% commission plateforme

            $reservation = Reservation::create([
                'user_id'          => Auth::id(),
                'establishment_id' => $establishment->id,
                'room_id'          => $room->id,
                'reference'        => Reservation::generateReference(),
                'check_in'         => $data['check_in'],
                'check_out'        => $data['check_out'],
                'guests'           => $data['guests'],
                'amount'           => $amount,
                'commission'       => $commission,
                'payment_method'   => null,
                'status'           => 'en_attente',
                // Sans paiement sous 24h, la réservation expire et libère la chambre.
                'expires_at'       => now()->addDay(),
            ]);

            // Transaction en attente : le moyen de paiement sera renseigné au checkout.
            Transaction::create([
                'reservation_id' => $reservation->id,
                'amount'         => $amount,
                'commission'     => $commission,
                'method'         => null,
                'reference'      => 'TXN-'.strtoupper(substr($reservation->reference, 3)).'-'.strtoupper(\Illuminate\Support\Str::random(4)),
                'status'         => 'en_attente',
            ]);

            return redirect()->route('reservations.checkout', $reservation)
                ->with('status', 'Votre réservation a bien été enregistrée. Vous avez 24h pour finaliser le paiement.');
        });
    }

    /**
     * Page de confirmation après création de la réservation (accessible directement si besoin).
     */
    public function confirm(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $reservation->load(['establishment', 'room', 'transaction']);

        return view('reservations.confirm', compact('reservation'));
    }

    /**
     * Espace voyageur : liste de ses réservations.
     */
    public function myReservations()
    {
        $reservations = Reservation::with(['establishment', 'room', 'transaction'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Tableau de bord du voyageur : statistiques et activités récentes.
     */
    public function dashboard()
    {
        Reservation::expireOverdue();
        $reservations = Reservation::with(['establishment', 'room', 'transaction'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $totalReservations = $reservations->count();
        $upcomingReservations = $reservations->whereIn('status', ['en_attente', 'confirmee'])->count();
        $totalSpent = $reservations->where('status', 'confirmee')->sum('amount');
        $pendingPayments = $reservations->where('status', 'en_attente')->count();

        $upcoming = $reservations
            ->where('status', '!=', 'annulee')
            ->where('check_out', '>=', now()->toDateString())
            ->sortBy('check_in')
            ->take(5);

        $latest = $reservations->take(6);

        return view('reservations.dashboard', compact(
            'reservations',
            'totalReservations',
            'upcomingReservations',
            'totalSpent',
            'pendingPayments',
            'upcoming',
            'latest'
        ));
    }

    /**
     * Détail d'une réservation du voyageur connecté.
     */
    public function show(Reservation $reservation)
    {
        abort_unless($reservation->user_id === Auth::id(), 403);

        $reservation->load(['establishment', 'room', 'transaction']);

        return view('reservations.show', compact('reservation'));
    }
}