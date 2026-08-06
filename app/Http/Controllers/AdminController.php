<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'establishments' => Establishment::count(),
            'users' => User::count(),
            'reservations' => Reservation::count(),
            'revenue' => Transaction::where('status', 'reussie')->sum('amount'),
            'commission' => Transaction::where('status', 'reussie')->sum('commission'),
            'pending_reservations' => Reservation::where('status', 'en_attente')->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $latestReservations = Reservation::with(['user', 'establishment'])
            ->latest()
            ->take(8)
            ->get();

        $latestTransactions = Transaction::with('reservation.establishment')
            ->latest()
            ->take(8)
            ->get();

        $pendingEstablishments = Establishment::where('is_available', false)
            ->with('type')
            ->latest()
            ->take(5)
            ->get();

$driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at) as month"
            : "DATE_FORMAT(created_at, '%Y-%m') as month";

        $revenueByMonth = Transaction::selectRaw("$monthExpr, SUM(amount) as total")
            ->where('status', 'reussie')
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(6)
            ->get();

        return view('backoffice.admin.dashboard', compact(
            'stats',
            'latestReservations',
            'latestTransactions',
            'pendingEstablishments',
            'revenueByMonth'
        ));
    }

    public function establishments()
    {
        $establishments = Establishment::with(['locality', 'type', 'owner'])
            ->latest()
            ->paginate(15);

        return view('backoffice.admin.establishments', compact('establishments'));
    }

    public function toggleEstablishment(int $id)
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->update(['is_available' => ! $establishment->is_available]);

        return back()->with('status', 'Statut de l\'établissement mis à jour.');
    }

    public function deleteEstablishment(int $id)
    {
        Establishment::findOrFail($id)->delete();

        return back()->with('status', 'Établissement supprimé.');
    }

    public function users()
    {
        $users = User::withCount('establishments', 'reservations')
            ->latest()
            ->paginate(15);

        return view('backoffice.admin.users', compact('users'));
    }

public function updateUserRole(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role' => $request->validate(['role' => 'required|in:voyageur,hote,admin'])['role'],
        ]);

        return back()->with('status', 'Rôle de l\'utilisateur mis à jour.');
    }

    public function reservations()
    {
        $reservations = Reservation::with(['user', 'establishment', 'room'])
            ->latest()
            ->paginate(15);

        return view('backoffice.admin.reservations', compact('reservations'));
    }

public function updateReservation(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => $request->validate(['status' => 'required|in:en_attente,confirmee,annulee,terminee'])['status'],
        ]);

        return back()->with('status', 'Réservation mise à jour.');
    }

    public function transactions()
    {
        $transactions = Transaction::with('reservation.establishment')
            ->latest()
            ->paginate(15);

        return view('backoffice.admin.transactions', compact('transactions'));
    }

    public function messages()
    {
        $messages = ContactMessage::latest()->paginate(15);

        return view('backoffice.admin.messages', compact('messages'));
    }

    public function markMessageRead(int $id)
    {
        ContactMessage::findOrFail($id)->update(['is_read' => true]);

        return back()->with('status', 'Message marqué comme lu.');
    }
}
