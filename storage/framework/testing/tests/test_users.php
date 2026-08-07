<?php

/**
 * Tests unitaires — Utilisateurs (modèle User)
 * ------------------------------------------------------------
 * Vérifie le modèle User, ses relations (establishments,
 * reviews, reservations), les rôles, et les champs cachés.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Review;
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

echo "=== TESTS : UTILISATEURS ===\n\n";

// --- Test 1 : Structure ---
echo "[1] Structure de la table users\n";
check('Table users existe', Schema::hasTable('users'));
$cols = Schema::getColumnListing('users');
foreach (['name', 'email', 'password', 'role', 'phone', 'avatar'] as $col) {
    check("Colonne {$col}", in_array($col, $cols));
}

// --- Test 2 : Rôles ---
echo "[2] Rôles des utilisateurs\n";
$admin = User::where('role', 'admin')->first();
$hote = User::where('role', 'hote')->first();
$voyageur = User::where('role', 'voyageur')->first();
check('Un admin existe', (bool) $admin);
check('Un hôte existe', (bool) $hote);
check('Un voyageur existe', (bool) $voyageur);
check('Admin → isAdmin', $admin && $admin->isAdmin());
check('Hôte → isHote', $hote && $hote->isHote());
check('Voyageur → isVoyageur', $voyageur && $voyageur->isVoyageur());

// --- Test 3 : Relations ---
echo "[3] Relations User\n";
$hote = User::where('role', 'hote')->first();
if ($hote) {
    check('Relation establishments()', $hote->establishments instanceof \Illuminate\Database\Eloquent\Collection);
}

$voyageur = User::where('role', 'voyageur')->first();
if ($voyageur) {
    check('Relation reservations()', $voyageur->reservations instanceof \Illuminate\Database\Eloquent\Collection);
    check('Relation reviews()', $voyageur->reviews instanceof \Illuminate\Database\Eloquent\Collection);
}

// --- Test 4 : Champs cachés ---
echo "[4] Champs sensibles cachés\n";
$user = User::first();
if ($user) {
    $arr = $user->toArray();
    check('password absent de toArray()', ! array_key_exists('password', $arr));
    check('remember_token absent de toArray()', ! array_key_exists('remember_token', $arr));
}

// --- Test 5 : Email unique ---
echo "[5] Emails uniques\n";
$emails = User::pluck('email');
check('Tous les emails sont uniques', $emails->unique()->count() === $emails->count());
check('Emails valides', $emails->every(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL) !== false));

// --- Test 6 : Relations avec établissements et réservations ---
echo "[6] Relations croisées\n";
$hoteAvecEtab = User::has('establishments')->where('role', 'hote')->first();
if ($hoteAvecEtab) {
    $etab = $hoteAvecEtab->establishments->first();
    check('Hôte possède un établissement', $etab instanceof Establishment);
    check('Établissement appartient à l\'hôte', $etab->user_id === $hoteAvecEtab->id);
}

$voyageurAvecRes = User::has('reservations')->where('role', 'voyageur')->first();
if ($voyageurAvecRes) {
    $res = $voyageurAvecRes->reservations->first();
    check('Voyageur a une réservation', $res instanceof Reservation);
    check('Réservation appartient au voyageur', $res->user_id === $voyageurAvecRes->id);
}

// --- Test 7 : Avis ---
echo "[7] Avis des utilisateurs\n";
$userAvecAvis = User::has('reviews')->first();
if ($userAvecAvis) {
    $review = $userAvecAvis->reviews->first();
    check('User a un avis', $review instanceof Review);
    check('Avis lié à l\'utilisateur', $review->user_id === $userAvecAvis->id);
}

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
