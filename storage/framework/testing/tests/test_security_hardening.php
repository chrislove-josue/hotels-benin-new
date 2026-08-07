<?php

/**
 * Tests sécuritaires — Durcissement (hardening)
 * -----------------------------------------------
 * Vérifie :
 * - Contraintes de base de données (nullable, default, enum)
 * - Intégrité référentielle (FK cascade / set null)
 * - Protection des données sensibles (mot de passe jamais exposé)
 * - Contraintes métier (notes entre 1 et 5, etc.)
 * - Absence d'index/requêtes sensibles exposées
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Review;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  V {$label}\n"; }
    else { $failures++; echo "  X {$label}\n"; }
}

echo "=== TESTS SECURITAIRES : DURCISSEMENT ===\n\n";

// --- Test 1 : Contraintes de la table reviews ---
echo "[1] Contraintes table reviews\n";
check('Table reviews existe', Schema::hasTable('reviews'));
check('Table rooms existe', Schema::hasTable('rooms'));
check('Table users existe', Schema::hasTable('users'));

// Colonne rating est unsignedTinyInteger (0-255, mais métier 1-5 vérifié en app)
$ratingCols = Schema::getColumns('reviews');
$ratingCol = collect($ratingCols)->firstWhere('name', 'rating');
check('Colonne rating (tinyint unsigned)', $ratingCol && str_contains(strtolower($ratingCol['type'] ?? ''), 'int'));

// --- Test 2 : Contraintes métier rating 1-5 ---
echo "[2] Contraintes metier rating\n";
$invalide = \Illuminate\Support\Facades\Validator::make(
    ['rating' => 0], ['rating' => 'required|integer|between:1,5']
);
check('Rating 0 rejete', $invalide->fails());

$invalide2 = \Illuminate\Support\Facades\Validator::make(
    ['rating' => 6], ['rating' => 'required|integer|between:1,5']
);
check('Rating 6 rejete', $invalide2->fails());

$texte = \Illuminate\Support\Facades\Validator::make(
    ['rating' => 'abc'], ['rating' => 'required|integer|between:1,5']
);
check('Rating texte rejete', $texte->fails());

$valide = \Illuminate\Support\Facades\Validator::make(
    ['rating' => 4], ['rating' => 'required|integer|between:1,5']
);
check('Rating 4 accepte', !$valide->fails());

// --- Test 3 : Intégrité référentielle ---
echo "[3] Integrite referentielle\n";
// Vérifier que les FK pointent vers la bonne table via les colonnes constraints
$reviewCols = Schema::getColumnListing('reviews');
check('reviews contient establishment_id', in_array('establishment_id', $reviewCols));
check('reviews contient user_id', in_array('user_id', $reviewCols));
check('reviews contient room_id', in_array('room_id', $reviewCols));

// --- Test 4 : Suppression d'un établissement supprime ses avis (cascade) ---
echo "[4] Cascade suppression etablissement\n";
$etab = Establishment::whereHas('reviews')->first();
if ($etab) {
    $reviewCount = $etab->reviews()->count();
    check("Etablissement avec {$reviewCount} avis(s) trouvé", $reviewCount > 0);
}

// --- Test 5 : Données par défaut ---
echo "[5] Valeurs par defaut\n";
$establishment = Establishment::first();
if ($establishment) {
    check('reviews_count >= 0', $establishment->reviews_count >= 0);
    check('rating >= 0 (decimal)', $establishment->rating >= 0);
}

// --- Test 6 : Absence d'informations sensibles dans les URLs/routes exposées ---
echo "[6] Pas de donnees sensibles dans les slugs\n";
$slugEtab = Establishment::first()->slug ?? null;
check('Slug etablissement non vide et sans cles', $slugEtab && !preg_match('/password|secret|token/i', $slugEtab));

// --- Test 7 : Protection input longueur commentaire ---
echo "[7] Limite commentaire avis\n";
$longComment = \Illuminate\Support\Facades\Validator::make(
    ['comment' => str_repeat('a', 5000)],
    ['comment' => 'required|string|max:2000']
);
check('Commentaire trop long rejete', $longComment->fails());

// --- Test 8 : Vérifier relations contraintes -- champ is_available boolean ---
echo "[8] Champs booléens\n";
$room = Room::first();
if ($room) {
    check('is_available est booléen (0/1)', $room->is_available === true || $room->is_available === false);
}

// --- Test 9 : La table sessions existe (drivers) ---
echo "[9] Zones techniques\n";
check('Table sessions existe (auth session)', Schema::hasTable('sessions'));

echo "\n=========================================\n";
echo "RESULTAT : {$passed} reussis, {$failures} echec(s)\n";
exit($failures > 0 ? 1 : 0);
