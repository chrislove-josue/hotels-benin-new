<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentType;
use App\Models\Locality;
use Illuminate\Http\Request;

class EstablishmentController extends Controller
{
    /**
     * Liste des établissements avec filtres (localité, type, prix, capacité).
     * Le "moteur de recherche" est simulé via des requêtes Eloquent basiques (GET).
     */
public function index(Request $request)
    {
        $filters = $request->only(['locality', 'type', 'min_price', 'max_price', 'capacity', 'sort']);

        $establishments = Establishment::with(['locality', 'type'])
            ->where('is_available', true)
            ->filter($filters)
            ->orderByDesc('is_featured')
            ->when($filters['sort'] ?? null, function ($q, $sort) {
                return match ($sort) {
                    'rating' => $q->orderByDesc('rating'),
                    'price_asc' => $q->orderBy('price_from'),
                    'price_desc' => $q->orderByDesc('price_from'),
                    default => $q->orderByDesc('created_at'),
                };
            }, fn ($q) => $q->orderByDesc('rating'))
            ->paginate(9)
            ->withQueryString();

        $localities = Locality::orderBy('name')->get();
        $types = EstablishmentType::orderBy('name')->get();

        return view('establishments.index', compact('establishments', 'localities', 'types', 'filters'));
    }

    public function show(string $slug)
    {
        $establishment = Establishment::with(['locality', 'type', 'images', 'amenities', 'rooms', 'reviews.user', 'reviews.room'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Charge aussi les avis de chaque chambre pour un affichage groupé par chambre.
        $establishment->load(['rooms.reviews' => fn ($q) => $q->with('user')->latest()]);

        $similar = Establishment::with(['locality', 'type'])
            ->where('id', '!=', $establishment->id)
            ->where('locality_id', $establishment->locality_id)
            ->where('is_available', true)
            ->take(3)
            ->get();

        return view('establishments.show', compact('establishment', 'similar'));
    }
}
