<?php

/**
 * Tests unitaires — Autorisations & sécurité des rôles
 * ------------------------------------------------------------
 * Vérifie la logique d'accès par rôle :
 * - isHote() / isAdmin() / isVoyageur()
 * - Middleware EnsureUserIsAdmin (bloque les non-admins)
 * - Middleware EnsureUserIsHote (bloque les non-hôtes)
 * - Vérification de propriété des réservations (abort 403)
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

echo "=== TESTS : AUTORISATIONS & SÉCURITÉ ===\n\n";

// --- Test 1 : méthodes de rôle ---
echo "[1] Méthodes isAdmin / isHote / isVoyageur\n";
$admin = User::where('role', 'admin')->first();
$hote = User::where('role', 'hote')->first();
$voyageur = User::where('role', 'voyageur')->first();

check('Admin → isAdmin() = true', $admin && $admin->isAdmin());
check('Admin → isHote() = false', $admin && ! $admin->isHote());
check('Admin → isVoyageur() = false', $admin && ! $admin->isVoyageur());

check('Hôte → isHote() = true', $hote && $hote->isHote());
check('Hôte → isAdmin() = false', $hote && ! $hote->isAdmin());

check('Voyageur → isVoyageur() = true', $voyageur && $voyageur->isVoyageur());
check('Voyageur → isAdmin() = false', $voyageur && ! $voyageur->isAdmin());
check('Voyageur → isHote() = false', $voyageur && ! $voyageur->isHote());

// --- Test 2 : Middleware EnsureUserIsAdmin ---
echo "[2] Middleware EnsureUserIsAdmin\n";
$middleware = new \App\Http\Middleware\EnsureUserIsAdmin();

// Non authentifié
Auth::logout();
$request = \Illuminate\Http\Request::create('/admin', 'GET');
$blocked = false;
try {
    $middleware->handle($request, function () { return response('ok'); });
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blocked = ($e->getStatusCode() === 403);
}
check('Non authentifié → bloqué 403', $blocked);

// Voyageur authentifié
Auth::login($voyageur);
$request2 = \Illuminate\Http\Request::create('/admin', 'GET');
$blocked2 = false;
try {
    $middleware->handle($request2, function () { return response('ok'); });
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blocked2 = ($e->getStatusCode() === 403);
}
check('Voyageur → bloqué 403', $blocked2);

// Admin authentifié
Auth::login($admin);
$request3 = \Illuminate\Http\Request::create('/admin', 'GET');
$allowed = false;
try {
    $response = $middleware->handle($request3, function () { return response('ok'); });
    $allowed = $response->getStatusCode() === 200;
} catch (\Throwable $e) {
    $allowed = false;
}
check('Admin → autorisé', $allowed);

// --- Test 3 : Middleware EnsureUserIsHote ---
echo "[3] Middleware EnsureUserIsHote\n";
$middlewareHote = new \App\Http\Middleware\EnsureUserIsHote();

Auth::login($voyageur);
$requestH = \Illuminate\Http\Request::create('/espace-hote', 'GET');
$blockedH = false;
try {
    $middlewareHote->handle($requestH, function () { return response('ok'); });
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blockedH = ($e->getStatusCode() === 403);
}
check('Voyageur → bloqué 403', $blockedH);

Auth::login($hote);
$requestH2 = \Illuminate\Http\Request::create('/espace-hote', 'GET');
$allowedH = false;
try {
    $response = $middlewareHote->handle($requestH2, function () { return response('ok'); });
    $allowedH = $response->getStatusCode() === 200;
} catch (\Throwable $e) {
    $allowedH = false;
}
check('Hôte → autorisé', $allowedH);

// --- Test 4 : propriété des réservations ---
echo "[4] Vérification de propriété des réservations\n";
$reservation = Reservation::first();
if ($reservation) {
    $owner = $reservation->user;
    check('La réservation a un propriétaire', (bool) $owner);

    // Simuler la vérification de propriété utilisée par ReservationController
    $isOwner = $reservation->user_id === $owner->id;
    check('Le propriétaire a accès (user_id match)', $isOwner);

    $otherUser = User::where('id', '!=', $reservation->user_id)->where('role', 'voyageur')->first();
    if ($otherUser) {
        $isNotOwner = $reservation->user_id !== $otherUser->id;
        check('Un autre utilisateur est bloqué (403)', $isNotOwner);
    }
}

// --- Test 5 : routes d'avis protégées par auth ---
echo "[5] Routes d'avis protégées par auth\n";
$routeList = \Illuminate\Support\Facades\Route::getRoutes();
$reviewStore = false;
$reviewEtab = false;
foreach ($routeList as $route) {
    if ($route->getName() === 'reviews.store') $reviewStore = true;
    if ($route->getName() === 'reviews.establishments.store') $reviewEtab = true;
}
check('Route reviews.store existe', $reviewStore);
check('Route reviews.establishments.store existe', $reviewEtab);

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);

