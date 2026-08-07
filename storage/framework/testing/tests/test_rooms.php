<?php

/**
 * Tests unitaires — Chambres (modèle Room)
 * ------------------------------------------------------------
 * Vérifie le modèle Room, ses relations, la validation,
 * les champs casts (is_available booléen, prix décimal),
 * et la cohérence prix/capacité.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Room;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : CHAMBRES ===\n\n";

// --- Test 1 : Structure ---
echo "[1] Structure de la table rooms\n";
check('Table rooms existe', Schema::hasTable('rooms'));
$cols = Schema::getColumnListing('rooms');
foreach (['establishment_id', 'name', 'description', 'capacity', 'price_per_night', 'quantity', 'is_available'] as $col) {
    check("Colonne {$col}", in_array($col, $cols));
}

// --- Test 2 : Relations ---
echo "[2] Relation Room -> Establishment\n";
$room = Room::first();
if ($room) {
    check('Relation establishment()', $room->establishment instanceof Establishment);
    check('L\'établissement de la chambre existe', (bool) $room->establishment_id);
}

// --- Test 3 : Champs casts ---
echo "[3] Casts booléen / décimal\n";
$room = Room::first();
if ($room) {
    check('is_available est booléen', is_bool($room->is_available));
    check('price_per_night numérique', is_numeric($room->price_per_night));
}

// --- Test 4 : Validation création ---
echo "[4] Validation des chambres\n";
$rules = [
    'name' => ['required', 'string', 'max:120'],
    'capacity' => ['required', 'integer', 'min:1'],
    'price_per_night' => ['required', 'numeric', 'min:0'],
    'quantity' => ['required', 'integer', 'min:1'],
];

$valid = Validator::make([
    'name' => 'Chambre Test',
    'capacity' => 2,
    'price_per_night' => 25000,
    'quantity' => 1,
], $rules);
check('Chambre valide acceptée', ! $valid->fails());

$noName = Validator::make([
    'capacity' => 2,
    'price_per_night' => 25000,
    'quantity' => 1,
], $rules);
check('Nom manquant rejeté', $noName->fails());

$negPrice = Validator::make([
    'name' => 'Chambre',
    'capacity' => 2,
    'price_per_night' => -1000,
    'quantity' => 1,
], $rules);
check('Prix négatif rejeté', $negPrice->fails());

$zeroCap = Validator::make([
    'name' => 'Chambre',
    'capacity' => 0,
    'price_per_night' => 1000,
    'quantity' => 1,
], $rules);
check('Capacité 0 rejetée', $zeroCap->fails());

// --- Test 5 : Données existantes ---
echo "[5] Données de chambres existantes\n";
$rooms = Room::all();
check('Des chambres existent', $rooms->count() > 0);
check('Prix de toutes les chambres > 0', $rooms->every(fn ($r) => $r->price_per_night > 0));
check('Capacité de toutes les chambres >= 1', $rooms->every(fn ($r) => $r->capacity >= 1));
check('Quantité de toutes les chambres >= 1', $rooms->every(fn ($r) => $r->quantity >= 1));

// --- Test 6 : Chambres par établissement ---
echo "[6] Chambres par établissement\n";
$etabAvecRooms = Establishment::has('rooms')->withCount('rooms')->first();
if ($etabAvecRooms) {
    check("Établissement a {$etabAvecRooms->rooms_count} chambres", $etabAvecRooms->rooms_count > 0);
    $allRooms = $etabAvecRooms->rooms;
    check('Toutes les chambres appartiennent à l\'établissement', $allRooms->every(fn ($r) => $r->establishment_id === $etabAvecRooms->id));
}

// --- Test 7 : Relation avec avis ---
echo "[7] Relation Room -> reviews\n";
$roomWithReviews = Room::has('reviews')->first();
if ($roomWithReviews) {
    check('Relation reviews() retourne une collection', $roomWithReviews->reviews instanceof \Illuminate\Database\Eloquent\Collection);
    check('La chambre a des avis', $roomWithReviews->reviews->count() > 0);
}

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
