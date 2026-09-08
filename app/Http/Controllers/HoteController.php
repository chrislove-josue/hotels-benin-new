<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Establishment;
use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Models\PendingEstablishmentChange;
use App\Models\PendingRoomChange;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Traits\UploadsImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HoteController extends Controller
{
    use UploadsImages;
    public function dashboard()
    {
        $establishments = Establishment::where('user_id', Auth::id())
            ->with(['locality', 'type', 'rooms', 'reservations'])
            ->latest()
            ->get();

        $establishmentIds = $establishments->pluck('id');

        $totalReservations = Reservation::whereIn('establishment_id', $establishmentIds)->count();
        $pendingReservations = Reservation::whereIn('establishment_id', $establishmentIds)
            ->where('status', 'en_attente')->count();
        $monthlyRevenue = $establishments->flatMap->reservations
            ->where('status', 'confirmee')
            ->sum('amount');

        // Avis reçus sur les chambres des établissements de l'hôte.
        $roomIds = $establishments->flatMap->rooms->pluck('id');
        $reviews = Review::with(['user', 'room', 'establishment'])
            ->whereIn('room_id', $roomIds)
            ->latest()
            ->take(8)
            ->get();

        return view('backoffice.hote.dashboard', compact(
            'establishments',
            'totalReservations',
            'pendingReservations',
            'monthlyRevenue',
            'reviews'
        ));
    }

    public function createEstablishment()
    {
        $localities = Locality::orderBy('name')->get();
        $types = EstablishmentType::orderBy('name')->get();
        $amenities = Amenity::orderBy('name')->get();

        return view('backoffice.hote.create-establishment', compact('localities', 'types', 'amenities'));
    }

public function storeEstablishment(Request $request)
    {
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
            'transfer_available' => ['sometimes', 'boolean'],
            'transfer_fee' => ['nullable', 'numeric', 'min:0'],
            'transfer_description' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'longitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'images' => ['array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
        ]);

        // Upload de la couverture (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null)
            ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80';

        $establishment = Auth::user()->establishments()->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(5)),
            'establishment_type_id' => $data['establishment_type_id'],
            'locality_id' => $data['locality_id'],
            'address' => $data['address'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'price_from' => $data['price_from'],
            'commission_pct' => $data['commission_pct'] ?? 25,
            'transfer_available' => $request->boolean('transfer_available'),
            'transfer_fee' => $data['transfer_fee'] ?? null,
            'transfer_description' => $data['transfer_description'] ?? null,
            'latitude' => filled($data['latitude'] ?? null) ? $data['latitude'] : null,
            'longitude' => filled($data['longitude'] ?? null) ? $data['longitude'] : null,
            'capacity' => $data['capacity'],
            'cover_image' => $cover,
            'is_available' => false, // en attente de validation par l'admin
        ]);

        $establishment->amenities()->sync($data['amenities'] ?? []);

        // Sauvegarde des images de galerie uploadées
        $gallery = $this->storeUploadedImages($request->file('images', []));
        foreach ($gallery as $i => $path) {
            $establishment->images()->create(['path' => $path, 'position' => $i]);
        }

        return redirect()->route('hote.dashboard')
            ->with('status', 'Établissement créé. Il sera visible après validation par un administrateur.');
    }

    public function editEstablishment(int $id)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($id);
        $localities = Locality::orderBy('name')->get();
        $types = EstablishmentType::orderBy('name')->get();
        $amenities = Amenity::orderBy('name')->get();

        return view('backoffice.hote.edit-establishment', compact('establishment', 'localities', 'types', 'amenities'));
    }

public function updateEstablishment(Request $request, int $id)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($id);

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
            'transfer_available' => ['sometimes', 'boolean'],
            'transfer_fee' => ['nullable', 'numeric', 'min:0'],
            'transfer_description' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'longitude' => ['nullable', 'regex:/^-?\d{1,3}(\.\d+)?$/'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'images' => ['array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
        ]);

        $data['transfer_available'] = $request->boolean('transfer_available');
        if (isset($data['commission_pct']) && is_numeric($data['commission_pct'])) {
            $data['commission_pct'] = (float) $data['commission_pct'];
        }
        $data['latitude'] = filled($data['latitude'] ?? null) ? $data['latitude'] : null;
        $data['longitude'] = filled($data['longitude'] ?? null) ? $data['longitude'] : null;

        // Upload de la couverture (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        // Images de galerie uploadées (stockées immédiatement, chemins ajoutés aux données en attente)
        $gallery = $this->storeUploadedImages($request->file('images', []));
        if ($gallery) {
            $existing = $establishment->images->pluck('path')->toArray();
            $data['gallery_add'] = array_merge($existing, $gallery);
        }

        // Emettre une modification en attente d'approbation admin au lieu d'appliquer directement
        // Garde anti-doublon : un seul changement en attente par établissement.
        if (PendingEstablishmentChange::where('establishment_id', $establishment->id)
            ->where('status', 'en_attente')
            ->exists()) {
            return back()->with('status', 'Vous avez déjà une modification en attente pour cet établissement. Attendez sa revue par un administrateur.');
        }

        PendingEstablishmentChange::create([
            'establishment_id' => $establishment->id,
            'user_id' => Auth::id(),
            'data' => array_merge($data, ['amenities' => $data['amenities'] ?? []]),
            'status' => 'en_attente',
        ]);

        return back()->with('status', 'Vos modifications sont enregistrées et seront appliquées après approbation par un administrateur.');
    }

    /**
     * Page dédiée de gestion des chambres d'un établissement.
     */
    public function rooms(int $establishmentId)
    {
        $establishment = Establishment::where('user_id', Auth::id())
            ->with('rooms')
            ->findOrFail($establishmentId);

        return view('backoffice.hote.rooms', compact('establishment'));
    }

    /**
     * Formulaire de modification d'une chambre.
     */
    public function editRoom(int $establishmentId, int $roomId)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($establishmentId);
        $room = $establishment->rooms()->findOrFail($roomId);

        return view('backoffice.hote.edit-room', compact('establishment', 'room'));
    }

    public function reservations(Request $request)
    {
        $establishmentIds = Establishment::where('user_id', Auth::id())->pluck('id');

        $q = trim((string) $request->query('q', ''));
        $status = $request->query('statut');

        $reservations = Reservation::with(['user', 'establishment', 'room'])
            ->whereIn('establishment_id', $establishmentIds)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('reference', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%"));
                });
            })
            ->when(in_array($status, ['en_attente', 'confirmee', 'annulee', 'terminee']),
                fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('backoffice.hote.reservations', compact('reservations'));
    }

    public function updateReservation(Request $request, int $id)
    {
        // Seul un administrateur de la plateforme peut modifier le statut d'une réservation.
        abort_unless(Auth::user()->isAdmin(), 403);

        $reservation = Reservation::findOrFail($id);

        $newStatus = $request->validate(['status' => 'required|in:en_attente,confirmee,annulee,terminee'])['status'];

        $reservation->update(['status' => $newStatus]);
        $reservation->syncTransactionWithStatus($newStatus);

        return back()->with('status', 'Réservation mise à jour.');
    }

    /**
     * Avis reçus sur les établissements et chambres de l'hôte.
     */
    public function reviews(Request $request)
    {
        $establishmentIds = Establishment::where('user_id', Auth::id())->pluck('id');

        $q = trim((string) $request->query('q', ''));
        $rating = $request->query('note');
        $establishmentId = $request->query('etablissement');

        $reviews = \App\Models\Review::with(['user', 'establishment', 'room'])
            ->whereIn('establishment_id', $establishmentIds)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('comment', 'like', "%{$q}%")
                        ->orWhere('title', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
                });
            })
            ->when(in_array((int) $rating, [1, 2, 3, 4, 5], true), fn ($query) => $query->where('rating', $rating))
            ->when($establishmentId, fn ($query) => $query->where('establishment_id', $establishmentId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $baseStats = \App\Models\Review::whereIn('establishment_id', $establishmentIds);
        $stats = [
            'total' => (clone $baseStats)->count(),
            'avg'   => round((float) (clone $baseStats)->avg('rating'), 2),
        ];

        $establishments = Establishment::where('user_id', Auth::id())->orderBy('name')->get(['id', 'name']);

        return view('backoffice.hote.reviews', compact('reviews', 'stats', 'establishments'));
    }

public function storeRoom(Request $request, int $establishmentId)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($establishmentId);

$data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        // Upload de la couverture de la chambre (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        // Création en attente d'approbation admin
        // Garde anti-doublon : un seul ajout de chambre en attente par établissement.
        if (PendingRoomChange::where('establishment_id', $establishment->id)
            ->whereNull('room_id')
            ->where('action', 'create')
            ->where('status', 'en_attente')
            ->exists()) {
            return back()->with('status', 'Un ajout de chambre / espace est déjà en attente pour cet établissement. Attendez sa revue par un administrateur.');
        }

        PendingRoomChange::create([
            'establishment_id' => $establishment->id,
            'user_id' => Auth::id(),
            'action' => 'create',
            'data' => $data,
            'status' => 'en_attente',
        ]);

        return back()->with('status', 'Chambre / espace ajouté. Il sera visible après approbation par un administrateur.');
    }

    public function updateRoom(Request $request, int $establishmentId, int $roomId)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($establishmentId);
        $room = $establishment->rooms()->findOrFail($roomId);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'url'],
            'cover_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        // Upload de la couverture de la chambre (fichier prioritaire sur l'URL)
        $cover = $this->resolveImage($request->file('cover_image_file'), $data['cover_image'] ?? null);
        if ($cover) {
            $data['cover_image'] = $cover;
        }

        // Modification en attente d'approbation admin
        // Garde anti-doublon : un seul changement (modif/suppression) en attente par chambre.
        if (PendingRoomChange::where('room_id', $room->id)
            ->whereIn('action', ['update', 'delete'])
            ->where('status', 'en_attente')
            ->exists()) {
            return back()->with('status', 'Un changement est déjà en attente pour cette chambre / cet espace. Attendez sa revue par un administrateur.');
        }

        PendingRoomChange::create([
            'room_id' => $room->id,
            'establishment_id' => $establishment->id,
            'user_id' => Auth::id(),
            'action' => 'update',
            'data' => $data,
            'status' => 'en_attente',
        ]);

        return back()->with('status', 'Vos modifications seront appliquées après approbation par un administrateur.');
    }

    public function deleteRoom(int $establishmentId, int $roomId)
    {
        $establishment = Establishment::where('user_id', Auth::id())->findOrFail($establishmentId);
        $room = $establishment->rooms()->findOrFail($roomId);

        // Garde-fou : ne pas permettre la suppression si des réservations
        // actives à venir sont liées à cette chambre (elles perdraient leur
        // chambre associée).
        if ($room->hasActiveReservations()) {
            return back()->with('status', 'Suppression impossible : cette chambre a des réservations actives à venir. Désactivez-la ou attendez la fin des séjours avant de la supprimer.');
        }

        // Garde anti-doublon : un seul changement (modif/suppression) en attente par chambre.
        if (PendingRoomChange::where('room_id', $room->id)
            ->whereIn('action', ['update', 'delete'])
            ->where('status', 'en_attente')
            ->exists()) {
            return back()->with('status', 'Un changement est déjà en attente pour cette chambre / cet espace. Attendez sa revue par un administrateur.');
        }

        // Suppression en attente d'approbation admin
        PendingRoomChange::create([
            'room_id' => $room->id,
            'establishment_id' => $establishment->id,
            'user_id' => Auth::id(),
            'action' => 'delete',
            'data' => null,
            'status' => 'en_attente',
        ]);

        return back()->with('status', 'La suppression sera appliquée après approbation par un administrateur.');
    }
}
