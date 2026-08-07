<?php

/**
 * Tests unitaires — Avis sur les chambres
 * ------------------------------------------------------------
 * Vérifie le flux complet de soumission d'avis sur une chambre :
 * - création d'un avis lié à une chambre
 * - un utilisateur peut poster plusieurs avis sur la même chambre
 * - recalcul de la moyenne et du compteur d'avis de l'établissement
 * - relation Review <-> Room <-> Establishment
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) {
        $passed++;
        echo "  ✔ {$label}\n";
    } else {
        $failures++;
        echo "  ✘ {$label}\n";
    }
}

echo "=== TESTS : AVIS SUR LES CHAMBRES ===\n\n";

// --- Test 1 : relation Review -> Room ---
echo "[1] Relation Review -> Room\n";
$review = Review::with('room')->whereNotNull('room_id')->first();
check('Un avis avec room_id existe dans la base', (bool) $review);
if ($review) {
    check('La relation room() renvoie une chambre', $review->room instanceof Room);
    check('La chambre appartient au même établissement', $review->room->establishment_id === $review->establishment_id);
}

// --- Test 2 : relation Room -> reviews ---
echo "[2] Relation Room -> reviews\n";
$room = Room::with('reviews')->has('reviews')->first();
check('Une chambre avec avis existe', (bool) $room);
if ($room) {
    check('La relation reviews() renvoie une collection', $room->reviews instanceof \Illuminate\Database\Eloquent\Collection);
    check('La collection contient au moins 1 avis', $room->reviews->count() >= 1);
    foreach ($room->reviews as $rv) {
        check('Chaque avis pointe vers cette chambre (id '.$room->id.')', $rv->room_id === $room->id);
    }
}

// --- Test 3 : création d'un avis sur chambre ---
echo "[3] Création d'un avis sur chambre\n";
$establishment = Establishment::with('rooms')->whereHas('rooms')->first();
$user = User::where('role', 'voyageur')->first();
$targetRoom = $establishment->rooms->first();
$before = Review::where('room_id', $targetRoom->id)->count();

$newReview = Review::create([
    'establishment_id' => $establishment->id,
    'room_id'          => $targetRoom->id,
    'user_id'          => $user->id,
    'rating'           => 5,
    'title'            => 'Test unitaire',
    'comment'          => 'Avis créé par le test automatisé.',
]);
check('L\'avis est créé avec un id', $newReview->id > 0);
check('Le room_id est bien enregistré', $newReview->room_id === $targetRoom->id);
$after = Review::where('room_id', $targetRoom->id)->count();
check('Le compteur d\'avis de la chambre a augmenté de 1', $after === $before + 1);

// --- Test 4 : multiples avis du même utilisateur sur la même chambre ---
echo "[4] Multiples avis même utilisateur / même chambre\n";
$m1 = Review::create([
    'establishment_id' => $establishment->id,
    'room_id'          => $targetRoom->id,
    'user_id'          => $user->id,
    'rating'           => 4,
    'title'            => 'Test multiple 1',
    'comment'          => 'Premier avis.',
]);
$m2 = Review::create([
    'establishment_id' => $establishment->id,
    'room_id'          => $targetRoom->id,
    'user_id'          => $user->id,
    'rating'           => 3,
    'title'            => 'Test multiple 2',
    'comment'          => 'Second avis.',
]);
$multiCount = Review::where('user_id', $user->id)->where('room_id', $targetRoom->id)->count();
check('L\'utilisateur peut poster 2 avis sur la même chambre', $multiCount >= 2);
check('Les deux avis ont des ids distincts', $m1->id !== $m2->id);

// --- Test 5 : recalcul du rating établissement ---
echo "[5] Recalcul du rating établissement\n";
$avg = round($establishment->reviews()->avg('rating'), 2);
$count = $establishment->reviews()->count();
$establishment->update([
    'rating'        => $avg,
    'reviews_count' => $count,
]);
$establishment->refresh();
check('rating mis à jour avec la moyenne ('.$avg.')', abs($establishment->rating - $avg) < 0.01);
check('reviews_count mis à jour ('.$count.')', (int) $establishment->reviews_count === (int) $count);

// --- Test 6 : avis sur établissement (room_id null) ---
echo "[6] Avis sur établissement sans chambre\n";
$estReview = Review::create([
    'establishment_id' => $establishment->id,
    'room_id'          => null,
    'user_id'          => $user->id,
    'rating'           => 4,
    'title'            => 'Test établissement',
    'comment'          => 'Avis au niveau de l\'établissement.',
]);
check('room_id est null pour un avis établissement', $estReview->room_id === null);

// --- Nettoyage : supprimer les avis de test ---
Review::where('title', 'like', 'Test %')->delete();
$establishment->update([
    'rating'        => round($establishment->reviews()->avg('rating'), 2),
    'reviews_count' => $establishment->reviews()->count(),
]);

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);

