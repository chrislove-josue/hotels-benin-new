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

public function users(Request $request)
    {
        $query = User::withCount('establishments', 'reservations');

        // Recherche par nom, e-mail ou téléphone
        if ($search = trim($request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        // Filtre par statut : en_attente | approuve | desactive
        if ($status = $request->query('statut')) {
            if ($status === 'en_attente') {
                $query->where('is_approved', false)->where('role', '!=', 'admin');
            } elseif ($status === 'approuve') {
                $query->where('is_approved', true);
            } elseif ($status === 'desactive') {
                $query->where('is_approved', false)->where('role', '!=', 'admin');
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

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

        $total = Transaction::where('status', 'reussie')->sum('amount');

        return view('backoffice.admin.transactions', compact('transactions', 'total'));
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
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s\-\.]{8,30}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:voyageur,hote,admin'],
            'is_approved' => ['sometimes', 'boolean'],
        ], [], [
            'name' => 'nom',
            'email' => 'e-mail',
            'phone' => 'numéro de téléphone / WhatsApp',
            'password' => 'mot de passe',
            'role' => 'profil',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => $data['role'],
            'is_approved' => $request->boolean('is_approved'),
        ]);

        return redirect()->route('admin.users')
            ->with('status', 'Utilisateur créé avec succès.');
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
            'email' => ['required', 'email', 'max:180', 'unique:users,email,' . $user->id],
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

        // Applique les nouvelles valeurs (sauf champs non modifiables)
        $data = collect($change->data)->except([
            'id', 'user_id', 'slug', 'created_at', 'updated_at',
            'rating', 'reviews_count', 'is_featured', 'is_available',
        ])->toArray();

        $establishment->update($data);

        $change->update([
            'status' => 'approuvee',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

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

        switch ($change->action) {
            case 'create':
            case 'update':
                $data = collect($change->data)->except([
                    'id', 'establishment_id', 'created_at', 'updated_at',
                ])->toArray();

                if ($change->action === 'create') {
                    $room = $change->establishment->rooms()->create($data);
                } else {
                    $room = $change->room;
                    $room?->update($data);
                }
                break;

            case 'delete':
                $change->room?->delete();
                break;
        }

        $change->update([
            'status' => 'approuvee',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

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

        $room->update($data);

        return redirect()->route('admin.establishments.rooms', $establishment->id)
            ->with('status', 'Chambre mise à jour directement par l\'administrateur.');
    }

    /*
    |--------------------------------------------------------------------------
    | Gestion des événements (admin)
    |--------------------------------------------------------------------------
    */

    /**
     * Liste paginée de tous les événements.
     */
    public function events()
    {
        $events = Event::latest()
            ->paginate(15);

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
}
