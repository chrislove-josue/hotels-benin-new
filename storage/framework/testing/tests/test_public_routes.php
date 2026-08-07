<?php

/**
 * Tests unitaires — Routes publiques (vitrine)
 * ------------------------------------------------------------
 * Vérifie que les routes publiques existent et renvoient un
 * rendu valide : accueil, liste établissements, fiche
 * établissement, à propos, contact.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use Illuminate\Support\Facades\Route;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : ROUTES PUBLIQUES ===\n\n";

// --- Test 1 : Routes enregistrées ---
echo "[1] Routes publiques enregistrées\n";
$routes = collect(Route::getRoutes());
$expectedRoutes = [
    'home' => 'GET',
    'about' => 'GET',
    'contact.create' => 'GET',
    'establishments.index' => 'GET',
    'establishments.show' => 'GET',
];
foreach ($expectedRoutes as $name => $method) {
    check("Route {$method} {$name}", $routes->contains(fn ($r) => $r->getName() === $name && in_array($method, $r->methods())));
}

// --- Test 2 : Home rend une vue ---
echo "[2] Rendu de la page d'accueil\n";
try {
    $response = $app->handle($request = \Illuminate\Http\Request::create('/', 'GET'));
    check('Home renvoie HTTP 200', $response->getStatusCode() === 200);
    $content = $response->getContent();
    check('Home contient Hôtels Bénin', str_contains($content, 'Hôtels'));
} catch (\Throwable $e) {
    check('Home renvoie HTTP 200', false);
}

// --- Test 3 : Liste des établissements ---
echo "[3] Rendu de la liste des établissements\n";
try {
    $response = $app->handle(\Illuminate\Http\Request::create('/etablissements', 'GET'));
    check('Liste renvoie HTTP 200', $response->getStatusCode() === 200);
    $content = $response->getContent();
    check('Liste contient "Établissements"', str_contains($content, 'Établissements') || str_contains($content, 'établissements'));
} catch (\Throwable $e) {
    check('Liste renvoie HTTP 200', false);
}

// --- Test 4 : Fiche établissement ---
echo "[4] Rendu de la fiche établissement\n";
$etab = Establishment::where('is_available', true)->first();
if ($etab) {
    try {
        $response = $app->handle(\Illuminate\Http\Request::create('/etablissements/'.$etab->slug, 'GET'));
        check('Fiche renvoie HTTP 200', $response->getStatusCode() === 200);
        $content = $response->getContent();
        check('Fiche contient le nom', str_contains($content, $etab->name));
    } catch (\Throwable $e) {
        check('Fiche renvoie HTTP 200', false);
    }
}

// --- Test 5 : À propos ---
echo "[5] Rendu de la page à propos\n";
try {
    $response = $app->handle(\Illuminate\Http\Request::create('/a-propos', 'GET'));
    check('À propos renvoie HTTP 200', $response->getStatusCode() === 200);
} catch (\Throwable $e) {
    check('À propos renvoie HTTP 200', false);
}

// --- Test 6 : Contact ---
echo "[6] Rendu de la page contact\n";
try {
    $response = $app->handle(\Illuminate\Http\Request::create('/contact', 'GET'));
    check('Contact renvoie HTTP 200', $response->getStatusCode() === 200);
} catch (\Throwable $e) {
    check('Contact renvoie HTTP 200', false);
}

// --- Test 7 : Slug invalide → 404 ---
echo "[7] Slug invalide\n";
try {
    $response = $app->handle(\Illuminate\Http\Request::create('/etablissements/slug-inexistant-xyz', 'GET'));
    check('Slug inconnu renvoie 404', $response->getStatusCode() === 404);
} catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    check('Slug inconnu renvoie 404', true);
} catch (\Throwable $e) {
    check('Slug inconnu renvoie 404', false);
}

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
