<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Support\CacheKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EstablishmentController extends Controller
{
    /**
     * Liste des établissements avec filtres (localité, type, prix, capacité).
     * Le "moteur de recherche" est simulé via des requêtes Eloquent basiques (GET).
     */
public function index(Request $request)
    {
        $filters = $request->only(['locality', 'type', 'min_price', 'max_price', 'capacity', 'sort']);

        // Le scénario le plus visité (sans filtre, page 1) est le seul utile à
        // mettre en cache : les combinaisons de filtres varient trop pour un TTL.
        $noFilters = count(array_filter($filters)) === 0
            && ((int) $request->query('page', 1)) === 1;

        $establishments = $noFilters
            ? Cache::remember(CacheKeys::ESTABLISHMENTS_INDEX_DEFAULT, CacheKeys::TTL, function () use ($filters) {
                return $this->queryEstablishments($filters)->paginate(9)->withQueryString();
            })
            : $this->queryEstablishments($filters)->paginate(9)->withQueryString();

        $localities = Cache::remember(CacheKeys::ESTABLISHMENT_FILTERS . '.localities', CacheKeys::TTL, function () {
            return Locality::orderBy('name')->get();
        });

        $types = Cache::remember(CacheKeys::ESTABLISHMENT_FILTERS . '.types', CacheKeys::TTL, function () {
            return EstablishmentType::orderBy('name')->get();
        });

        return view('establishments.index', compact('establishments', 'localities', 'types', 'filters'));
    }

    /**
     * Requête de base de la liste des établissements (partagée entre le cas
     * caché et le cas filtré pour éviter la duplication).
     */
    private function queryEstablishments(array $filters)
    {
        return Establishment::with(['locality', 'type'])
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
            }, fn ($q) => $q->orderByDesc('rating'));
    }

    public function show(string $slug)
    {
        $data = Cache::remember(CacheKeys::ESTABLISHMENT_DETAIL_PREFIX . $slug, CacheKeys::TTL, function () use ($slug) {
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

            return compact('establishment', 'similar');
        });

        return view('establishments.show', $data);
    }
}
