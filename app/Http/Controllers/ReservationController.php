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
     * Crée une réservation pour un voyageur connecté.
     */
    public function store(Request $request, string $slug)
    {
        $establishment = Establishment::where('slug', $slug)->where('is_available', true)->firstOrFail();

        $data = $request->validate([
            'room_id'   => ['required', 'exists:rooms,id'],
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1', 'max:20'],
            'payment_method' => ['required', 'in:mtn_momo,moov_money,card'],
        ]);

$room = Room::where('id', $data['room_id'])
            ->where('establishment_id', $establishment->id)
            ->where('is_available', true)
            ->firstOrFail();

        // Vérification de disponibilité : bloquer si la chambre est complète sur la période.
        if ($room->isUnavailableFor($data['check_in'], $data['check_out'])) {
            return back()
                ->withErrors(['room_id' => "La chambre « {$room->name} » est complète pour ces dates. Choisissez une autre période ou une autre chambre."])
                ->withInput();
        }

        $nights = Reservation::nightsBetween($data['check_in'], $data['check_out']);
        $amount = $nights * (float) $room->price_per_night;
        $commission = round($amount * 0.10, 2); // 10% commission plateforme

        $reservation = Reservation::create([
            'user_id'         => Auth::id(),
            'establishment_id'=> $establishment->id,
            'room_id'         => $room->id,
            'reference'       => Reservation::generateReference(),
            'check_in'        => $data['check_in'],
            'check_out'       => $data['check_out'],
            'guests'          => $data['guests'],
            'amount'          => $amount,
            'commission'      => $commission,
            'payment_method'  => $data['payment_method'],
            'status'          => 'en_attente',
        ]);

        // Créer la transaction associée (paiement en attente)
        Transaction::create([
            'reservation_id' => $reservation->id,
            'amount'         => $amount,
            'commission'     => $commission,
            'method'         => $data['payment_method'],
            'reference'      => 'TXN-'.strtoupper(substr($reservation->reference, 3)).'-'.strtoupper(\Illuminate\Support\Str::random(4)),
            'status'         => 'en_attente',
        ]);

        return redirect()->route('reservations.confirm', $reservation)
            ->with('status', 'Votre réservation a bien été enregistrée.');
    }

    /**
     * Page de confirmation après création de la réservation.
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
