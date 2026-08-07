<?php

/**
 * Tests unitaires — Routes back-office (admin & hôte)
 * ------------------------------------------------------------
 * Vérifie que toutes les routes admin et hôte sont enregistrées
 * et protégées par les middlewares adéquats.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : ROUTES BACK-OFFICE ===\n\n";

$routes = collect(Route::getRoutes());

// --- Test 1 : Routes admin ---
echo "[1] Routes admin enregistrées\n";
$adminRoutes = [
    'admin.dashboard' => 'GET',
    'admin.establishments' => 'GET',
    'admin.users' => 'GET',
    'admin.reservations' => 'GET',
    'admin.transactions' => 'GET',
    'admin.messages' => 'GET',
    'admin.pending' => 'GET',
];
foreach ($adminRoutes as $name => $method) {
    check("Route {$method} {$name}", $routes->contains(fn ($r) => $r->getName() === $name && in_array($method, $r->methods())));
}

// --- Test 2 : Routes hôte ---
echo "[2] Routes hôte enregistrées\n";
$hoteRoutes = [
    'hote.dashboard' => 'GET',
    'hote.establishments.create' => 'GET',
    'hote.establishments.store' => 'POST',
    'hote.rooms.index' => 'GET',
    'hote.rooms.store' => 'POST',
    'hote.reservations' => 'GET',
];
foreach ($hoteRoutes as $name => $method) {
    check("Route {$method} {$name}", $routes->contains(fn ($r) => $r->getName() === $name && in_array($method, $r->methods())));
}

// --- Test 3 : Middlewares sur routes admin ---
echo "[3] Protection des routes admin (middleware)\n";
$adminMw = new \App\Http\Middleware\EnsureUserIsAdmin();
$voyageur = User::where('role', 'voyageur')->first();

Auth::login($voyageur);
$req = \Illuminate\Http\Request::create('/admin', 'GET');
$blocked = false;
try {
    $adminMw->handle($req, fn () => response('ok'));
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blocked = $e->getStatusCode() === 403;
}
check('Voyageur bloqué sur route admin', $blocked);

$hote = User::where('role', 'hote')->first();
Auth::login($hote);
$req2 = \Illuminate\Http\Request::create('/admin', 'GET');
$blocked2 = false;
try {
    $adminMw->handle($req2, fn () => response('ok'));
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blocked2 = $e->getStatusCode() === 403;
}
check('Hôte bloqué sur route admin', $blocked2);

// --- Test 4 : Middlewares sur routes hôte ---
echo "[4] Protection des routes hôte (middleware)\n";
$hoteMw = new \App\Http\Middleware\EnsureUserIsHote();
$voyageur2 = User::where('role', 'voyageur')->first();
Auth::login($voyageur2);
$reqH = \Illuminate\Http\Request::create('/espace-hote', 'GET');
$blockedH = false;
try {
    $hoteMw->handle($reqH, fn () => response('ok'));
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blockedH = $e->getStatusCode() === 403;
}
check('Voyageur bloqué sur route hôte', $blockedH);

$admin2 = User::where('role', 'admin')->first();
Auth::login($admin2);
$reqH2 = \Illuminate\Http\Request::create('/espace-hote', 'GET');
$blockedH2 = false;
try {
    $hoteMw->handle($reqH2, fn () => response('ok'));
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blockedH2 = $e->getStatusCode() === 403;
}
check('Admin bloqué sur route hôte', $blockedH2);

// --- Test 5 : Routes admin sans auth → 403 ---
echo "[5] Routes admin bloquées sans authentification\n";
Auth::logout();
$reqNoAuth = \Illuminate\Http\Request::create('/admin', 'GET');
$blockedNoAuth = false;
try {
    $adminMw->handle($reqNoAuth, fn () => response('ok'));
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $blockedNoAuth = $e->getStatusCode() === 403;
}
check('Non authentifié bloqué route admin', $blockedNoAuth);

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
