<?php

/**
 * Tests unitaires — Établissements (modèle, relations, filtres)
 * ------------------------------------------------------------
 * Vérifie le modèle Establishment, ses relations (locality, type,
 * owner, images, rooms, amenities, reviews, reservations),
 * le scope filter() et les slugs uniques.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Amenity;
use App\Models\Establishment;
use App\Models\EstablishmentImage;
use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : ÉTABLISSEMENTS ===\n\n";

// --- Test 1 : Structure ---
echo "[1] Structure de la table establishments\n";
check('Table establishments existe', Schema::hasTable('establishments'));
$cols = Schema::getColumnListing('establishments');
foreach (['name', 'slug', 'description', 'price_from', 'rating', 'reviews_count', 'is_available', 'locality_id', 'establishment_type_id', 'user_id'] as $col) {
    check("Colonne {$col}", in_array($col, $cols));
}

// --- Test 2 : Relations ---
echo "[2] Relations de base\n";
$etab = Establishment::first();
if ($etab) {
    check('Relation locality()', $etab->locality instanceof Locality);
    check('Relation type()', $etab->type instanceof EstablishmentType);
    check('Relation owner()', $etab->owner instanceof User);
    check('Relation images()', $etab->images instanceof \Illuminate\Database\Eloquent\Collection);
    check('Relation rooms()', $etab->rooms instanceof \Illuminate\Database\Eloquent\Collection);
    check('Relation reviews()', $etab->reviews instanceof \Illuminate\Database\Eloquent\Collection);
    check('Relation amenities()', $etab->amenities instanceof \Illuminate\Database\Eloquent\Collection);
}

// --- Test 3 : Slug unique ---
echo "[3] Slug unique\n";
$slugs = Establishment::pluck('slug');
check('Tous les slugs sont uniques', $slugs->unique()->count() === $slugs->count());
check('Slug non vide', $slugs->every(fn ($s) => strlen($s) > 0));

// --- Test 4 : Scope filter ---
echo "[4] Scope filter\n";
$etab = Establishment::where('is_available', true)->first();
if ($etab) {
    $filtered = Establishment::filter(['locality' => $etab->locality->slug])->get();
    check('Filtre par localité retourne des résultats', $filtered->count() > 0);
    check('Tous les résultats ont la bonne localité', $filtered->every(fn ($e) => $e->locality_id === $etab->locality_id));
}

// --- Test 5 : Prix et rating ---
echo "[5] Prix et rating\n";
$etabs = Establishment::all();
check('price_from >= 0', $etabs->every(fn ($e) => $e->price_from >= 0));
check('rating entre 0 et 5', $etabs->every(fn ($e) => $e->rating >= 0 && $e->rating <= 5));

// --- Test 6 : Relations avec chambres ---
echo "[6] Relations avec chambres\n";
$etabAvecRooms = Establishment::has('rooms')->first();
if ($etabAvecRooms) {
    $room = $etabAvecRooms->rooms->first();
    check('Chambre appartient à l\'établissement', $room->establishment_id === $etabAvecRooms->id);
    check('Prix de la chambre > 0', $room->price_per_night > 0);
}

// --- Test 7 : Images ---
echo "[7] Images\n";
$etabAvecImg = Establishment::has('images')->first();
if ($etabAvecImg) {
    check('Images avec position', $etabAvecImg->images->every(fn ($i) => isset($i->position)));
    check('Images ordonnées par position', $etabAvecImg->images->sortBy('position')->pluck('id')->values()->toArray() === $etabAvecImg->images->pluck('id')->toArray());
}

// --- Test 8 : Avis ---
echo "[8] Avis\n";
$etabAvecAvis = Establishment::has('reviews')->first();
if ($etabAvecAvis) {
    $review = $etabAvecAvis->reviews->first();
    check('Avis a un utilisateur', $review->user instanceof User);
    check('Avis a un rating entre 1 et 5', $review->rating >= 1 && $review->rating <= 5);
    check('reviews_count correspond', $etabAvecAvis->reviews_count === $etabAvecAvis->reviews()->count());
}

// --- Test 9 : Équipements (amenities) ---
echo "[9] Équipements\n";
$etabAvecAmenities = Establishment::has('amenities')->first();
if ($etabAvecAmenities) {
    $amenity = $etabAvecAmenities->amenities->first();
    check('Amenity est une instance Amenity', $amenity instanceof Amenity);
    check('Relation many-to-many fonctionnelle', $etabAvecAmenities->amenities->count() > 0);
}

// --- Test 10 : is_featured et is_available ---
echo "[10] Flags booléens\n";
$featured = Establishment::where('is_featured', true)->get();
check('Établissements à la une existent', $featured->count() > 0);
$available = Establishment::where('is_available', true)->get();
check('Établissements disponibles existent', $available->count() > 0);

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
