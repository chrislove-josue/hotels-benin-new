<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Mes avis (espace voyageur) : tous les avis laissés par l'utilisateur.
     */
    public function myReviews()
    {
        $reviews = Review::with(['establishment', 'room'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reservations.my-reviews', compact('reviews'));
    }

    /**
     * Supprime son propre avis.
     */
    public function destroyOwn(Review $review)
    {
        abort_unless($review->user_id === Auth::id(), 403);

        $establishment = $review->establishment;
        $review->delete();

        // Recalcule la note moyenne de l'établissement après suppression.
        if ($establishment) {
            $this->refreshEstablishmentRating($establishment);
        }

        return back()->with('status', 'Votre avis a été supprimé.');
    }
    /**
     * Soumet un avis sur une chambre par un utilisateur connecté.
     * Un utilisateur peut poster plusieurs avis sur la même chambre.
     */
    public function store(Request $request, Room $room)
    {
        // Vérifier que la chambre est rattachée à un établissement actif.
        abort_unless($room->establishment()->where('is_available', true)->exists(), 404);

        $data = $this->validated($request);

        Review::create([
            'establishment_id' => $room->establishment_id,
            'room_id'          => $room->id,
            'user_id'          => Auth::id(),
            'rating'           => $data['rating'],
            'title'            => $data['title'] ?? null,
            'comment'          => $data['comment'],
        ]);

        $this->refreshEstablishmentRating($room->establishment);

        return back()->with('status', 'Merci pour votre avis sur cette chambre ! Il a bien été publié.');
    }

    /**
     * Soumet un avis sur un établissement (hôtel) par un utilisateur connecté.
     * Un utilisateur peut poster plusieurs avis sur le même établissement.
     */
    public function storeEstablishment(Request $request, Establishment $establishment)
    {
        abort_unless($establishment->is_available, 404);

        $data = $this->validated($request);

        Review::create([
            'establishment_id' => $establishment->id,
            'room_id'          => null,
            'user_id'          => Auth::id(),
            'rating'           => $data['rating'],
            'title'            => $data['title'] ?? null,
            'comment'          => $data['comment'],
        ]);

        $this->refreshEstablishmentRating($establishment);

        return back()->with('status', 'Merci pour votre avis sur cet établissement ! Il a bien été publié.');
    }

    /**
     * Champs validés communs aux deux types d'avis.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'title'   => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);
    }

    /**
     * Recalcule la note moyenne et le nombre d'avis de l'établissement.
     */
    private function refreshEstablishmentRating(Establishment $establishment): void
    {
        $establishment->update([
            'rating'        => round($establishment->reviews()->avg('rating'), 2),
            'reviews_count' => $establishment->reviews()->count(),
        ]);
    }
}

