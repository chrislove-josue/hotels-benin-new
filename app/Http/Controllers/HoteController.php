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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HoteController extends Controller
{
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
            'cover_image' => ['nullable', 'url'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
        ]);

        $establishment = Auth::user()->establishments()->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::lower(Str::random(5)),
            'establishment_type_id' => $data['establishment_type_id'],
            'locality_id' => $data['locality_id'],
            'address' => $data['address'],
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'price_from' => $data['price_from'],
            'capacity' => $data['capacity'],
            'cover_image' => $data['cover_image'] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80',
            'is_available' => false, // en attente de validation par l'admin
        ]);

        $establishment->amenities()->sync($data['amenities'] ?? []);

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
            'cover_image' => ['nullable', 'url'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
        ]);

// Emettre une modification en attente d'approbation admin au lieu d'appliquer directement
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

    public function reservations()
    {
        $establishmentIds = Establishment::where('user_id', Auth::id())->pluck('id');

        $reservations = Reservation::with(['user', 'establishment', 'room'])
            ->whereIn('establishment_id', $establishmentIds)
            ->latest()
            ->paginate(20);

        return view('backoffice.hote.reservations', compact('reservations'));
    }

    public function updateReservation(Request $request, int $id)
    {
        $reservation = Reservation::findOrFail($id);

        // Vérifier que la réservation appartient à un établissement de l'hôte
        $owns = Establishment::where('user_id', Auth::id())
            ->where('id', $reservation->establishment_id)
            ->exists();

        abort_unless($owns, 403);

        $reservation->update([
            'status' => $request->validate(['status' => 'required|in:en_attente,confirmee,annulee,terminee'])['status'],
        ]);

        return back()->with('status', 'Réservation mise à jour.');
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
        ]);

        // Création en attente d'approbation admin
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
        ]);

        // Modification en attente d'approbation admin
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
