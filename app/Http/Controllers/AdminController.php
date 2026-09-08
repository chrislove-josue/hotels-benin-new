<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\PendingEstablishmentChange;
use App\Models\PendingRoomChange;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\UploadsImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use UploadsImages;
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
            'pending_changes' => PendingEstablishmentChange::where('status', 'en_attente')->count()
                + PendingRoomChange::where('status', 'en_attente')->count(),
            'pending_users' => User::where('is_approved', false)->count(),
        ];

        // ---- Chambres : disponibilité en temps réel (occupation d'aujourd'hui) ----
        // Une unité est occupée si une réservation active chevauche la date du jour :
        // confirmee toujours ; en_attente tant que son délai de paiement n'a pas expiré.
        $activeOccupancy = function ($q) {
            $q->where('status', 'confirmee')
                ->orWhere(function ($qq) {
                    $qq->where('status', 'en_attente')
                        ->where(function ($w) {
                            $w->whereNull('expires_at')->orWhere('expires_at', '>', now());
                        });
                });
        };

        $today = now()->toDateString();

        $totalUnits = (int) Room::where('is_available', true)->sum('quantity');
        $roomTypes = (int) Room::where('is_available', true)->count();
        $occupiedToday = (int) Reservation::where($activeOccupancy)
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->count();
        $arrivalsToday = (int) Reservation::where($activeOccupancy)
            ->whereDate('check_in', '=', $today)
            ->count();
        $departuresToday = (int) Reservation::where('status', 'confirmee')
            ->whereDate('check_out', '=', $today)
            ->count();

        $stats += [
            'room_types'      => $roomTypes,
            'total_units'     => $totalUnits,
            'available_units' => max(0, $totalUnits - $occupiedToday),
            'occupied_units'  => min($occupiedToday, $totalUnits),
            'occupancy_rate'  => $totalUnits > 0 ? (int) round(min($occupiedToday, $totalUnits) / $totalUnits * 100) : 0,
            'arrivals_today'  => $arrivalsToday,
            'departures_today'=> $departuresToday,
        ];

        // ---- Transactions : détail par statut ----
        $stats += [
            'tx_reussie'   => ['count' => Transaction::where('status', 'reussie')->count(),   'amount' => (float) Transaction::where('status', 'reussie')->sum('amount')],
            'tx_en_attente'=> ['count' => Transaction::where('status', 'en_attente')->count(), 'amount' => (float) Transaction::where('status', 'en_attente')->sum('amount')],
            'tx_echouee'   => ['count' => Transaction::where('status', 'echouee')->count(),   'amount' => (float) Transaction::where('status', 'echouee')->sum('amount')],
        ];

        $latestReservations = Reservation::with(['user', 'establishment'])
            ->latest()
            ->take(8)
            ->get();

        $latestTransactions = Transaction::with(['reservation.establishment', 'reservation.user'])
            ->latest()
            ->take(10)
            ->get();

        // ---- Classement des établissements par clients et revenus ----
        // Clients = voyageurs DISTINCTS ayant une réservation confirmée ou terminée.
        // Revenus = somme des transactions réussies liées à l'établissement.
        $topEstablishments = DB::table('establishments as e')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->leftJoin('reservations as r', 'r.establishment_id', '=', 'e.id')
            ->leftJoin('transactions as t', function ($join) {
                $join->on('t.reservation_id', '=', 'r.id')->where('t.status', 'reussie');
            })
            ->select(
                'e.id',
                'e.name',
                'u.name as host_name',
                DB::raw("COUNT(DISTINCT CASE WHEN r.status IN ('confirmee', 'terminee') THEN r.user_id END) as clients_count"),
                DB::raw('COUNT(DISTINCT r.id) as reservations_count'),
                DB::raw('COALESCE(SUM(t.amount), 0) as revenue'),
                DB::raw('COALESCE(SUM(t.commission), 0) as commission')
            )
            ->groupBy('e.id', 'e.name', 'u.name')
            ->orderByDesc('revenue')
            ->orderByDesc('clients_count')
            ->limit(10)
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
            'revenueByMonth',
            'topEstablishments'
        ));
    }

    public function establishments(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $typeId = $request->query('type');
        $localityId = $request->query('localite');
        $status = $request->query('statut');

        $establishments = Establishment::with(['locality', 'type', 'owner'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('address', 'like', "%{$q}%")
                        ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($typeId, fn ($query) => $query->where('establishment_type_id', $typeId))
            ->when($localityId, fn ($query) => $query->where('locality_id', $localityId))
            ->when($status === 'actif', fn ($query) => $query->where('is_available', true))
            ->when($status === 'en_attente', fn ($query) => $query->where('is_available', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $types = \App\Models\EstablishmentType::orderBy('name')->get();
        $localities = \App\Models\Locality::orderBy('name')->get();

        return view('backoffice.admin.establishments', compact('establishments', 'types', 'localities'));
    }

    public function toggleEstablishment(int $id)
    {
        $establishment = Establishment::findOrFail($id);
        $establishment->update(['is_available' => ! $establishment->is_available]);

        return back()->with('status', 'Statut de l\'établissement mis à jour.');
    }

    /**
     * Fixe directement la commission plateforme (20-30 %) d'un établissement.
     */
    public function updateCommission(Request $request, int $id)
    {
        $establishment = Establishment::findOrFail($id);

        $data = $request->validate([
            'commission_pct' => ['required', 'numeric', 'in:15,20,25,30'],
        ], [], ['commission_pct' => 'commission']);

        $establishment->update(['commission_pct' => (float) $data['commission_pct']]);

        return back()->with('status', "Commission de « {$establishment->name} » fixée à {$data['commission_pct']} %.");
    }

    public function deleteEstablishment(int $id)
    {
        $establishment = Establishment::findOrFail($id);

        // Un établissement avec historique de réservations ne se supprime pas :
        // préserver les traces financières et éviter les erreurs de clé étrangère.
        if ($establishment->reservations()->exists()) {
            return back()->with('status', 'Suppression impossible : cet établissement a des réservations. Désactivez-le plutôt (bouton « Masquer / Publier »).');
        }

        DB::transaction(function () use ($establishment) {
            PendingEstablishmentChange::where('establishment_id', $establishment->id)->delete();
            PendingRoomChange::where('establishment_id', $establishment->id)->delete();

            foreach ($establishment->rooms as $room) {
                PendingRoomChange::where('room_id', $room->id)->delete();
                $room->delete();
            }

            $establishment->images()->delete();
            $establishment->amenities()->detach();
            $establishment->delete();
        });

        return back()->with('status', 'Établissement supprimé.');
    }

public function users(Request $request)
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('statut');

        // Filtres communs : recherche + statut d'approbation
        $applyCommonFilters = function ($query) use ($search, $status) {
            $query->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })->when($status === 'approuve', fn ($q) => $q->where('is_approved', true))
              ->when($status === 'en_attente', fn ($q) => $q->where('is_approved', false));
        };

        // ---- Hôtes : classés par revenus encaissés, puis nombre de chambres ----
        $hosts = User::query()
            ->where('role', 'hote')
            ->select('users.*')
            ->addSelect([
                'rooms_count' => Room::query()
                    ->join('establishments', 'establishments.id', '=', 'rooms.establishment_id')
                    ->whereColumn('establishments.user_id', 'users.id')
                    ->selectRaw('COUNT(rooms.id)'),
                'establishments_count' => Establishment::query()
                    ->whereColumn('establishments.user_id', 'users.id')
                    ->selectRaw('COUNT(*)'),
                'host_reservations_count' => Reservation::query()
                    ->join('establishments as er', 'er.id', '=', 'reservations.establishment_id')
                    ->whereColumn('er.user_id', 'users.id')
                    ->selectRaw('COUNT(*)'),
                'revenue' => Transaction::query()
                    ->join('reservations as tr', 'tr.id', '=', 'transactions.reservation_id')
                    ->join('establishments as te', 'te.id', '=', 'tr.establishment_id')
                    ->whereColumn('te.user_id', 'users.id')
                    ->where('transactions.status', 'reussie')
                    ->selectRaw('COALESCE(SUM(transactions.amount), 0)'),
            ])
            ->tap($applyCommonFilters)
            ->orderByDesc('revenue')
            ->orderByDesc('rooms_count')
            ->orderByDesc('host_reservations_count')
            ->get();

        // ---- Voyageurs : classés par nombre de réservations, puis total dépensé ----
        // Inclut aussi les hôtes et admins ayant effectué au moins une réservation :
        // ils sont alors classés comme les autres, avec leur rôle secondaire affiché.
        $voyageurs = User::query()
            ->select('users.*')
            ->addSelect([
                'voyageur_reservations_count' => Reservation::query()
                    ->whereColumn('reservations.user_id', 'users.id')
                    ->selectRaw('COUNT(*)'),
                'spent' => Transaction::query()
                    ->join('reservations as sr', 'sr.id', '=', 'transactions.reservation_id')
                    ->whereColumn('sr.user_id', 'users.id')
                    ->where('transactions.status', 'reussie')
                    ->selectRaw('COALESCE(SUM(transactions.amount), 0)'),
            ])
            ->where(function ($q) {
                $q->where('role', 'voyageur')
                    ->orWhereHas('reservations');
            })
            ->tap($applyCommonFilters)
            ->orderByDesc('voyageur_reservations_count')
            ->orderByDesc('spent')
            ->get();

        // ---- Administrateurs : liste simple à part ----
        $admins = User::query()
            ->where('role', 'admin')
            ->tap($applyCommonFilters)
            ->orderBy('name')
            ->get();

        $pendingCount = $hosts->merge($voyageurs)->where('is_approved', false)->count();

        return view('backoffice.admin.users', compact('hosts', 'voyageurs', 'admins', 'pendingCount'));
    }

public function updateUserRole(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role' => $request->validate(['role' => 'required|in:voyageur,hote,admin'])['role'],
        ]);

        return back()->with('status', 'Rôle de l\'utilisateur mis à jour.');
    }

    /**
     * Approuve le compte d'un utilisateur (hôte ou voyageur) en attente de validation.
     */
    public function approveUser(int $id)
    {
        $user = User::findOrFail($id);

        $user->update(['is_approved' => true]);

        return back()->with('status', "Le compte de {$user->name} a été approuvé.");
    }

    /**
     * Rejette le compte d'un utilisateur (compte désactivé / non approuvé).
     */
    public function rejectUser(int $id)
    {
        $user = User::findOrFail($id);

        $user->update(['is_approved' => false]);

        return back()->with('status', "Le compte de {$user->name} a été désactivé.");
    }

    public function reservations(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('statut');
        $establishmentId = $request->query('etablissement');

        $reservations = Reservation::with(['user', 'establishment', 'room'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('reference', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%"))
                        ->orWhereHas('establishment', fn ($e) => $e->where('name', 'like', "%{$q}%"));
                });
            })
            ->when(in_array($status, ['en_attente', 'confirmee', 'annulee', 'terminee']),
                fn ($query) => $query->where('status', $status))
            ->when($establishmentId, fn ($query) => $query->where('establishment_id', $establishmentId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $establishments = Establishment::orderBy('name')->get(['id', 'name']);

        return view('backoffice.admin.reservations', compact('reservations', 'establishments'));
    }

public function updateReservation(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $newStatus = $request->validate(['status' => 'required|in:en_attente,confirmee,annulee,terminee'])['status'];

        $reservation->update(['status' => $newStatus]);
        $reservation->syncTransactionWithStatus($newStatus);

        return back()->with('status', 'Réservation mise à jour.');
    }

public function transactions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('statut');
        $method = trim((string) $request->query('methode', ''));

        $transactions = Transaction::with(['reservation.establishment', 'reservation.user'])
            ->when($q !== '', fn ($query) => $query->where('reference', 'like', "%{$q}%")
                ->orWhereHas('reservation', fn ($r) => $r->where('reference', 'like', "%{$q}%")))
            ->when(in_array($status, ['en_attente', 'reussie', 'echouee', 'remboursee']),
                fn ($query) => $query->where('status', $status))
            ->when($method !== '', fn ($query) => $query->where('method', $method))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $total = Transaction::where('status', 'reussie')->sum('amount');

        return view('backoffice.admin.transactions', compact('transactions', 'total'));
    }

    /**
     * Mise à jour manuelle du statut d'une transaction par l'admin
     * (paiement en espèces sur place, remboursement demandé par le client...).
     * Une raison est exigée pour toute décision manuelle et la réservation liée
     * est synchronisée : reussie => confirmee, remboursee => annulee.
     */
    public function updateTransaction(Request $request, int $id)
    {
        $transaction = Transaction::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'in:en_attente,reussie,echouee,remboursee'],
            // Raison obligatoire dès qu'une décision humaine est prise (pas pour « en attente »).
            'reason' => ['required_if:status,reussie', 'required_if:status,echouee', 'required_if:status,remboursee', 'nullable', 'string', 'max:255'],
        ], [], [
            'status' => 'statut',
            'reason' => 'raison',
        ]);

        $oldStatus = $transaction->status;
        $transaction->update([
            'status' => $data['status'],
            'reason' => $data['reason'] ?? null,
        ]);

        if ($data['status'] !== $oldStatus) {
            $reservation = $transaction->reservation;

            if ($reservation) {
                if ($data['status'] === 'reussie') {
                    // Paiement réglé hors plateforme (espèces...) ou régularisé : on confirme.
                    if (! $transaction->method) {
                        $transaction->update(['method' => 'sur_place']);
                    }
                    $reservation->update(['status' => 'confirmee']);
                } elseif ($data['status'] === 'remboursee') {
                    // Remboursement accordé : la réservation ne peut plus être tenue.
                    $reservation->update(['status' => 'annulee']);
                }
            }
        }

        return back()->with('status', "Transaction {$transaction->reference} mise à jour.");
    }

    public function messages(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $state = $request->query('etat');

        $messages = ContactMessage::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('subject', 'like', "%{$q}%")
                        ->orWhere('message', 'like', "%{$q}%");
                });
            })
            ->when($state === 'lu', fn ($query) => $query->where('is_read', true))
            ->when($state === 'non_lu', fn ($query) => $query->where('is_read', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('backoffice.admin.messages', compact('messages'));
    }

    public function markMessageRead(int $id)
    {
        ContactMessage::findOrFail($id)->update(['is_read' => true]);

        return back()->with('status', 'Message marqué comme lu.');
    }

    /**
     * Tous les avis de la plateforme avec statistiques et filtres.
     */
    public function reviews(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $rating = $request->query('note');
        $type = $request->query('type');

        $reviews = \App\Models\Review::with(['user', 'establishment', 'room'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('comment', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('establishment', fn ($e) => $e->where('name', 'like', "%{$q}%"));
                });
            })
            ->when(in_array((int) $rating, [1, 2, 3, 4, 5], true), fn ($query) => $query->where('rating', $rating))
            ->when($type === 'chambre', fn ($query) => $query->whereNotNull('room_id'))
            ->when($type === 'etablissement', fn ($query) => $query->whereNull('room_id'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => \App\Models\Review::count(),
            'avg'   => round((float) \App\Models\Review::avg('rating'), 2),
            'rooms' => \App\Models\Review::whereNotNull('room_id')->count(),
            'establishments' => \App\Models\Review::whereNull('room_id')->count(),
        ];

        return view('backoffice.admin.reviews', compact('reviews', 'stats'));
    }

    /**
     * Modifie un avis (modération) et recalcule la note de l'établissement.
     */
    public function updateReview(Request $request, int $id)
    {
        $review = \App\Models\Review::findOrFail($id);

        $data = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'title'   => 'nullable|string|max:120',
            'comment' => 'required|string|max:2000',
        ]);

        $review->update($data);
        $review->establishment?->update([
            'rating'        => round($review->establishment->reviews()->avg('rating'), 2),
            'reviews_count' => $review->establishment->reviews()->count(),
        ]);

        return back()->with('status', 'Avis modifié.');
    }

    /**
     * Supprime un avis (modération).
     */
    public function destroyReview(int $id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return back()->with('status', 'Avis supprimé.');
    }

/**
     * Formulaire de création d'un utilisateur par l'administrateur.
     */
    public function createUser()
    {
        return view('backoffice.admin.create-user');
    }

    /**
     * Enregistre un nouvel utilisateur créé par l'administrateur.
     */
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email', new \App\Rules\ValidRealEmail],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-\.]{8,30}$/'],
            'role' => ['required', 'in:voyageur,hote,admin'],
            'is_approved' => ['sometimes', 'boolean'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'phone' => 'numéro de téléphone / WhatsApp',
            'role' => 'profil',
        ]);

        // Mot de passe généré automatiquement : l'admin ne le connaît pas,
        // il est transmis à l'utilisateur par e-mail avec un lien de connexion directe.
        $password = \Illuminate\Support\Str::password(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => $data['role'],
            'is_approved' => $request->boolean('is_approved'),
        ]);

        $sent = $user->sendAccountCreatedNotification($password);

        $status = 'Utilisateur créé avec succès. Ses identifiants lui ont été envoyés par e-mail.';

        if (! $sent) {
            $status = 'Utilisateur créé avec succès. ATTENTION : l\'e-mail d\'accueil n\'a pas pu être envoyé (serveur SMTP injoignable). L\'utilisateur ne pourra pas valider son compte tant que le lien ne lui est pas transmis.';
        }

        return redirect()->route('admin.users')->with('status', $status);
    }

    /**
     * Formulaire de modification d'un utilisateur.
     */
    public function editUser(int $id)
    {
        $user = User::findOrFail($id);

        return view('backoffice.admin.edit-user', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email,' . $user->id, new \App\Rules\ValidRealEmail],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-\.]{8,30}$/'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:voyageur,hote,admin'],
            'is_approved' => ['sometimes', 'boolean'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'phone' => 'numéro de téléphone / WhatsApp',
            'password' => 'mot de passe',
            'role' => 'profil',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'is_approved' => $request->boolean('is_approved'),
        ]);

        // Mot de passe fourni => mise à jour uniquement si non vide.
        if (! empty($data['password'])) {
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($data['password'])]);
        }

        return redirect()->route('admin.users')
            ->with('status', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Impersonation : l'administrateur ouvre la session d'un hôte ou voyageur
     * sans connaître son mot de passe (diagnostic de compte, support).
     *
     * L'admin d'origine est mémorisé en session ; un bandeau permet de revenir.
     */
    public function impersonate(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->with('status', 'Impossible d\'ouvrir la session d\'un autre administrateur.');
        }

        session(['impersonated_by' => auth()->id()]);
        auth()->login($user);
        session()->regenerate();

        $destination = match (true) {
            $user->isHote() => route('hote.dashboard'),
            $user->role === 'voyageur' => route('reservations.dashboard'),
            default => route('home'),
        };

        return redirect($destination)
            ->with('status', "Session administrateur : vous naviguez actuellement en tant que {$user->name}. Un bandeau permet de revenir.");
    }

    /**
     * Met fin à l'impersonation et restaure la session administrateur.
     */
    public function stopImpersonation()
    {
        $adminId = session('impersonated_by');

        if (! $adminId) {
            return redirect()->route('home');
        }

        $admin = User::findOrFail($adminId);

        auth()->login($admin);
        session()->forget('impersonated_by');
        session()->regenerate();

        return redirect()->route('admin.users')
            ->with('status', "Retour à votre session administrateur ({$admin->name}).");
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);

        // Empêche de supprimer son propre compte.
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('status', "L'utilisateur {$name} a été supprimé.");
    }

    /**
     * Supprime plusieurs utilisateurs en une seule opération.
     */
    public function destroyUsersBulk(Request $request)
    {
        $raw = (string) $request->input('ids', '');

        $ids = collect(explode(',', $raw))
            ->map(fn ($v) => trim($v))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->reject(fn ($id) => $id === auth()->id())
            ->all();

        if (empty($ids)) {
            return back()->with('status', 'Aucun utilisateur sélectionné à supprimer.');
        }

        $count = User::whereIn('id', $ids)->count();
        User::whereIn('id', $ids)->delete();

        return back()->with('status', "{$count} utilisateur(s) supprimé(s).");
    }

    /**
     * Liste des modifications en attente d'approbation (hôtels + chambres).
     */
    public function pendingChanges()
    {
        $pendingEstablishments = PendingEstablishmentChange::with(['establishment', 'user'])
            ->where('status', 'en_attente')
            ->latest()
            ->get();

        $pendingRooms = PendingRoomChange::with(['room', 'establishment', 'user'])
            ->where('status', 'en_attente')
            ->latest()
            ->get();

        return view('backoffice.admin.pending', compact('pendingEstablishments', 'pendingRooms'));
    }

    /**
     * Approuve une modification d'établissement : applique les données puis marque comme approuvée.
     */
    public function approvePendingEstablishment(int $id)
    {
        $change = PendingEstablishmentChange::findOrFail($id);

        if ($change->status !== 'en_attente') {
            return back()->with('status', 'Cette modification a déjà été traitée.');
        }

        $establishment = $change->establishment;

        if (! $establishment) {
            $change->update([
                'status' => 'rejetee',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return back()->with('status', 'L\'établissement lié n\'existe plus : modification rejetée automatiquement.');
        }

        // Applique les nouvelles valeurs (champs non modifiables et méta-clés exclus)
        $data = collect($change->data)->except([
            'id', 'user_id', 'slug', 'created_at', 'updated_at',
            'rating', 'reviews_count', 'is_featured', 'is_available',
            'amenities', 'gallery_add',
        ])->toArray();

        DB::transaction(function () use ($establishment, $change, $data) {
            $establishment->update($data);

            // Galerie : ajoute uniquement les chemins absents
            if (! empty($change->data['gallery_add'])) {
                $existing = $establishment->images()->pluck('path')->all();
                $missing = array_values(array_diff($change->data['gallery_add'], $existing));
                $position = (int) $establishment->images()->max('position') + 1;
                foreach ($missing as $i => $path) {
                    $establishment->images()->create(['path' => $path, 'position' => $position + $i]);
                }
            }

            // Aménités cochées par l'hôte
            if (array_key_exists('amenities', $change->data ?? [])) {
                $establishment->amenities()->sync($change->data['amenities'] ?? []);
            }

            $change->update([
                'status' => 'approuvee',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('status', 'Modification de l\'établissement approuvée et appliquée.');
    }

    /**
     * Rejette une modification d'établissement.
     */
    public function rejectPendingEstablishment(int $id)
    {
        $change = PendingEstablishmentChange::findOrFail($id);

        $change->update([
            'status' => 'rejetee',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Modification de l\'établissement rejetée.');
    }

    /**
     * Approuve une modification de chambre (create/update/delete).
     */
    public function approvePendingRoom(int $id)
    {
        $change = PendingRoomChange::findOrFail($id);

        if ($change->status !== 'en_attente') {
            return back()->with('status', 'Cette modification a déjà été traitée.');
        }

        if ($change->action !== 'create' && ! $change->room) {
            $change->update([
                'status' => 'rejetee',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return back()->with('status', 'La chambre liée n\'existe plus : modification rejetée automatiquement.');
        }

        switch ($change->action) {
            case 'create':
            case 'update':
                $data = collect($change->data)->except([
                    'id', 'establishment_id', 'created_at', 'updated_at',
                ])->toArray();

                // Garde-fou surbooking : la quantité ne peut pas passer sous le pic
                // de chevauchement des réservations actives à venir.
                $target = $change->action === 'create' ? null : $change->room;
                $required = $target ? $target->minRequiredQuantity() : 0;
                if ((int) ($data['quantity'] ?? 1) < $required) {
                    return back()->with('status', "Refusé : la chambre a {$required} réservation(s) active(s) à venir. Ajustez la quantité (min. {$required}) ou annulez des réservations avant d'approuver.");
                }

                DB::transaction(function () use ($change, $data) {
                    if ($change->action === 'create') {
                        $change->establishment->rooms()->create($data);
                    } else {
                        $change->room->update($data);
                    }

                    // Le prix minimum de l'établissement suit le coût réel des chambres.
                    $change->establishment->refreshPriceFrom();

                    $change->update([
                        'status' => 'approuvee',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                });
                break;

            case 'delete':
                // Garde-fou : une chambre avec réservations actives à venir ne
                // se supprime pas (elles perdraient leur chambre associée).
                if ($change->room && $change->room->hasActiveReservations()) {
                    return back()->with('status', 'Refusé : cette chambre a des réservations actives à venir. Désactivez-la ou attendez la fin des séjours avant de la supprimer.');
                }

                DB::transaction(function () use ($change) {
                    $change->room?->delete();

                    // Recalcule le prix minimum après suppression.
                    $change->establishment->refreshPriceFrom();

                    $change->update([
                        'status' => 'approuvee',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                    ]);
                });
                break;
        }

        return back()->with('status', 'Modification de la chambre approuvée et appliquée.');
    }

    /**
     * Rejette une modification de chambre.
     */
    public function rejectPendingRoom(int $id)
    {
        $change = PendingRoomChange::findOrFail($id);

        $change->update([
            'status' => 'rejetee',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Modification de la chambre rejetée.');
    }

/**
     * Liste des chambres d'un établissement (vue admin).
     */
    public function establishmentRooms(int $establishmentId)
    {
        $establishment = Establishment::with('rooms')->findOrFail($establishmentId);

        return view('backoffice.admin.rooms', compact('establishment'));
    }

    /**
     * Formulaire d'édition directe d'un établissement (admin).
     */
    public function editEstablishment(int $id)
    {
        $establishment = Establishment::with(['amenities', 'locality', 'type'])->findOrFail($id);

        $localities = \App\Models\Locality::orderBy('name')->get();
        $types = \App\Models\EstablishmentType::orderBy('name')->get();
        $amenities = \App\Models\Amenity::orderBy('name')->get();

        return view('backoffice.admin.edit-establishment', compact('establishment', 'localities', 'types', 'amenities'));
    }

    /**
     * Mise à jour directe d'un établissement (admin) — applique immédiatement.
     */
public function updateEstablishment(Request $request, int $id)
    {
        $establishment = Establishment::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'establishment_type_id' => ['required', 'exists:establishment_types,id'],
            'locality_id' => ['required', 'exists:localities,id'],
            'address' => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_from' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'commission_pct' => ['nullable', 'numeric', 'in:15,20,25,30'],
            'latitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'longitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'transfer_available' => ['sometimes', 'boolean'],
            'transfer_fee' => ['nullable', 'numeric', 'min:0'],
            'transfer_description' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'images' => ['array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'is_available' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
        ]);

        // Upload de la couverture (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        $establishment->update(array_merge($data, [
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
            'transfer_available' => $request->boolean('transfer_available'),
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'transfer_fee' => $data['transfer_fee'] ?? null,
            'transfer_description' => $data['transfer_description'] ?? null,
            'commission_pct' => $data['commission_pct'] ?? 25,
        ]));

        $establishment->amenities()->sync($data['amenities'] ?? []);

        // Ajout des images de galerie uploadées
        $gallery = $this->storeUploadedImages($request->file('images', []));
        $position = $establishment->images()->max('position') + 1;
        foreach ($gallery as $i => $path) {
            $establishment->images()->create(['path' => $path, 'position' => $position + $i]);
        }

        return redirect()->route('admin.establishments')
            ->with('status', 'Établissement mis à jour directement par l\'administrateur.');
    }

    /**
     * Formulaire d'édition directe d'une chambre (admin).
     */
    public function editRoom(int $establishmentId, int $roomId)
    {
        $establishment = Establishment::findOrFail($establishmentId);
        $room = $establishment->rooms()->findOrFail($roomId);

        return view('backoffice.admin.edit-room', compact('establishment', 'room'));
    }

    /**
     * Mise à jour directe d'une chambre (admin) — applique immédiatement.
     */
public function updateRoom(Request $request, int $establishmentId, int $roomId)
    {
        $establishment = Establishment::findOrFail($establishmentId);
        $room = $establishment->rooms()->findOrFail($roomId);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        // Upload de la couverture de la chambre (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        // Garde-fou surbooking : la quantité ne peut pas passer sous le pic de
        // chevauchement des réservations actives à venir.
        if (isset($data['quantity']) && (int) $data['quantity'] < $room->minRequiredQuantity()) {
            return back()->withInput()
                ->with('status', "Impossible : la chambre a {$room->minRequiredQuantity()} réservation(s) active(s) à venir. Annulez-les ou attendez leur passage avant de réduire la quantité.");
        }

        $room->update($data);

        return redirect()->route('admin.establishments.rooms', $establishment->id)
            ->with('status', 'Chambre mise à jour directement par l\'administrateur.');
    }

    /*
    |--------------------------------------------------------------------------
    | Gestion des événements (admin)
    |--------------------------------------------------------------------------
    */

    public function events(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = $request->query('categorie');
        $visibility = $request->query('visibilite');

        $events = Event::query()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            }))
            ->when(in_array($category, ['festival', 'culture', 'sport', 'affaires', 'religieux']),
                fn ($query) => $query->where('category', $category))
            ->when($visibility === 'en_ligne', fn ($query) => $query->where('is_available', true))
            ->when($visibility === 'hors_ligne', fn ($query) => $query->where('is_available', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('backoffice.admin.events', compact('events'));
    }

    /**
     * Formulaire de création d'un événement.
     */
    public function createEvent()
    {
        $categories = [
            'festival' => 'Festival',
            'culture' => 'Culture & Arts',
            'sport' => 'Sport',
            'affaires' => 'Affaires & Conférences',
            'religieux' => 'Religieux',
        ];

        return view('backoffice.admin.edit-event', compact('categories'));
    }

    /**
     * Enregistre un nouvel événement.
     */
public function storeEvent(Request $request)
    {
        $data = $this->validatedEventData($request);

        // Upload de la couverture de l'événement (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        Event::create(array_merge($data, [
            'slug' => \Illuminate\Support\Str::slug($data['title']) . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4)),
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
        ]));

        return redirect()->route('admin.events')
            ->with('status', 'Événement créé et publié.');
    }

    /**
     * Formulaire de modification d'un événement.
     */
    public function editEvent(int $id)
    {
        $event = Event::findOrFail($id);

        $categories = [
            'festival' => 'Festival',
            'culture' => 'Culture & Arts',
            'sport' => 'Sport',
            'affaires' => 'Affaires & Conférences',
            'religieux' => 'Religieux',
        ];

        return view('backoffice.admin.edit-event', compact('event', 'categories'));
    }

    /**
     * Met à jour un événement existant.
     */
    public function updateEvent(Request $request, int $id)
    {
        $event = Event::findOrFail($id);

$data = $this->validatedEventData($request);

        // Upload de la couverture de l'événement (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        $event->update(array_merge($data, [
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
        ]));

        return redirect()->route('admin.events')
            ->with('status', 'Événement mis à jour.');
    }

    /**
     * Active / désactive un événement (visibilité sur la vitrine publique).
     */
    public function toggleEvent(int $id)
    {
        $event = Event::findOrFail($id);
        $event->update(['is_available' => ! $event->is_available]);

        return back()->with('status', 'Visibilité de l\'événement mise à jour.');
    }

    /**
     * Supprime un événement.
     */
    public function deleteEvent(int $id)
    {
        Event::findOrFail($id)->delete();

        return back()->with('status', 'Événement supprimé.');
    }

    /**
     * Validation partagée des données d'un événement.
     */
    private function validatedEventData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'in:festival,culture,sport,affaires,religieux'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date_start' => ['required', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
'location' => ['nullable', 'string', 'max:120'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Paramètres de paiement (agrégateurs FedaPay / KKiaPay)
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire des paramètres de paiement.
     */
    public function paymentSettings()
    {
        $set = \App\Models\Setting::class;

        return view('backoffice.admin.payment-settings', [
            'enabled'        => \App\Support\PaymentProvider::enabledProviders(),
            'default'        => \App\Support\PaymentProvider::defaultProvider(),
            'fedapay'        => [
                'environment' => \App\Support\PaymentProvider::fedapayEnvironment(),
                'sandbox'     => [
                    'public_key'     => $set::get('fedapay_public_key_sandbox'),
                    'secret_key'     => $set::get('fedapay_secret_key_sandbox'),
                    'webhook_secret' => $set::get('fedapay_webhook_secret_sandbox'),
                ],
                'production'  => [
                    'public_key'     => $set::get('fedapay_public_key_production'),
                    'secret_key'     => $set::get('fedapay_secret_key_production'),
                    'webhook_secret' => $set::get('fedapay_webhook_secret_production'),
                ],
            ],
            'kkiapay'        => [
                'environment' => \App\Support\PaymentProvider::kkiapayEnvironment(),
                'sandbox'     => [
                    'public_key'  => $set::get('kkiapay_public_key_sandbox'),
                    'private_key' => $set::get('kkiapay_private_key_sandbox'),
                    'secret'      => $set::get('kkiapay_secret_sandbox'),
                ],
                'production'  => [
                    'public_key'  => $set::get('kkiapay_public_key_production'),
                    'private_key' => $set::get('kkiapay_private_key_production'),
                    'secret'      => $set::get('kkiapay_secret_production'),
                ],
            ],
        ]);
    }

    /**
     * Enregistre les paramètres de paiement.
     */
    public function updatePaymentSettings(Request $request)
    {
        $data = $request->validate([
            'providers'                                  => ['required', 'array', 'min:1'],
            'providers.*'                                => ['required', 'in:fedapay,kkiapay'],
            'default_provider'                           => ['required', 'in:fedapay,kkiapay'],
            'fedapay_environment'                        => ['required', 'in:sandbox,production'],
            'kkiapay_environment'                        => ['required', 'in:sandbox,production'],
            'fedapay_public_key_sandbox'                 => ['nullable', 'string', 'max:255'],
            'fedapay_secret_key_sandbox'                 => ['nullable', 'string', 'max:255'],
            'fedapay_webhook_secret_sandbox'             => ['nullable', 'string', 'max:255'],
            'fedapay_public_key_production'              => ['nullable', 'string', 'max:255'],
            'fedapay_secret_key_production'              => ['nullable', 'string', 'max:255'],
            'fedapay_webhook_secret_production'          => ['nullable', 'string', 'max:255'],
            'kkiapay_public_key_sandbox'                 => ['nullable', 'string', 'max:255'],
            'kkiapay_private_key_sandbox'                => ['nullable', 'string', 'max:255'],
            'kkiapay_secret_sandbox'                     => ['nullable', 'string', 'max:255'],
            'kkiapay_public_key_production'              => ['nullable', 'string', 'max:255'],
            'kkiapay_private_key_production'             => ['nullable', 'string', 'max:255'],
            'kkiapay_secret_production'                  => ['nullable', 'string', 'max:255'],
        ], [], [
            'providers'                                  => 'agrégateurs',
            'default_provider'                           => 'agrégateur par défaut',
            'fedapay_environment'                        => 'environnement FedaPay',
            'kkiapay_environment'                        => 'environnement KKiaPay',
            'fedapay_public_key_sandbox'                 => 'clé publique FedaPay (sandbox)',
            'fedapay_secret_key_sandbox'                 => 'clé secrète FedaPay (sandbox)',
            'fedapay_webhook_secret_sandbox'             => 'secret de webhook FedaPay (sandbox)',
            'fedapay_public_key_production'              => 'clé publique FedaPay (production)',
            'fedapay_secret_key_production'              => 'clé secrète FedaPay (production)',
            'fedapay_webhook_secret_production'          => 'secret de webhook FedaPay (production)',
            'kkiapay_public_key_sandbox'                 => 'clé publique KKiaPay (sandbox)',
            'kkiapay_private_key_sandbox'                => 'clé privée KKiaPay (sandbox)',
            'kkiapay_secret_sandbox'                     => 'secrète KKiaPay (sandbox)',
            'kkiapay_public_key_production'              => 'clé publique KKiaPay (production)',
            'kkiapay_private_key_production'             => 'clé privée KKiaPay (production)',
            'kkiapay_secret_production'                  => 'secrète KKiaPay (production)',
        ]);

        $providers = array_values(array_unique($data['providers']));

        // L'agrégateur par défaut doit faire partie des activés.
        $default = in_array($data['default_provider'], $providers, true)
            ? $data['default_provider']
            : $providers[0];

        \App\Models\Setting::set('payment_providers_enabled', $providers);
        \App\Models\Setting::set('payment_provider_default', $default);

        $fedKeys = ['public_key', 'secret_key', 'webhook_secret'];
        foreach (['sandbox', 'production'] as $env) {
            foreach ($fedKeys as $key) {
                $field = "fedapay_{$key}_{$env}";
                if (! empty($data[$field])) {
                    \App\Models\Setting::set("fedapay_{$key}_{$env}", $data[$field]);
                }
            }
        }
        \App\Models\Setting::set('fedapay_environment', $data['fedapay_environment']);

        $kkiaKeys = ['public_key', 'private_key', 'secret'];
        foreach (['sandbox', 'production'] as $env) {
            foreach ($kkiaKeys as $key) {
                $field = "kkiapay_{$key}_{$env}";
                if (! empty($data[$field])) {
                    \App\Models\Setting::set("kkiapay_{$key}_{$env}", $data[$field]);
                }
            }
        }
        \App\Models\Setting::set('kkiapay_environment', $data['kkiapay_environment']);

        return back()->with('status', 'Paramètres de paiement mis à jour.');
    }
}
