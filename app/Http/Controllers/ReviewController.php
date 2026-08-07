<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Soumet un avis sur une chambre par un utilisateur connecté.
     */
    public function store(Request $request, Room $room)
    {
        // Seul un utilisateur connecté peut donner son avis (middleware auth sur la route).
        // Vérifier que la chambre est disponible et rattachée à un établissement actif.
        abort_unless($room->establishment()->where('is_available', true)->exists(), 404);

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'title'   => ['nullable', 'string', 'max:120'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        // Un utilisateur ne peut donner qu'un seul avis par chambre.
        $exists = Review::where('user_id', Auth::id())
            ->where('room_id', $room->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'review' => 'Vous avez déjà donné votre avis pour cette chambre.',
            ])->withInput();
        }

        Review::create([
            'establishment_id' => $room->establishment_id,
            'room_id'          => $room->id,
            'user_id'          => Auth::id(),
            'rating'           => $data['rating'],
            'title'            => $data['title'] ?? null,
            'comment'          => $data['comment'],
        ]);

        // Recalcule la note moyenne et le nombre d'avis de l'établissement.
        $establishment = $room->establishment;
        $establishment->update([
            'rating'        => round($establishment->reviews()->avg('rating'), 2),
            'reviews_count' => $establishment->reviews()->count(),
        ]);

        return back()->with('status', 'Merci pour votre avis ! Il a bien été publié.');
    }
}

