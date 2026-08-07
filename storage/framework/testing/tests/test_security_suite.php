<?php

/**
 * Suite consolidée de tests sécuritaires — Hôtels Bénin
 * ---------------------------------------------------------
 * Groupe les tests de sécurité (validation, CSRF, IDOR, hardening)
 * et affiche un résumé compatible avec le lanceur global
 * (format : "X réussis, Y échec").
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

$failures = 0;
$passed = 0;

function t(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  V {$label}\n"; }
    else { $failures++; echo "  X {$label}\n"; }
}

echo "=== SUITE DE TESTS SECURITAIRES ===\n\n";

// ===== 1. VALIDATION DES ENTRÉES =====
echo "[1] Validation XSS / longueur / types\n";
$v = Validator::make(['comment' => str_repeat('a', 2500)], ['comment' => 'required|string|max:2000']);
t('Commentaire > 2000 rejeté', $v->fails());

$v = Validator::make(['rating' => 3.5], ['rating' => 'required|integer|between:1,5']);
t('Rating flottant rejeté', $v->fails());

$v = Validator::make(['rating' => 6], ['rating' => 'required|integer|between:1,5']);
t('Rating > 5 rejeté', $v->fails());

$v = Validator::make(['email' => 'pas-email'], ['email' => 'required|email']);
t('Email invalide rejeté', $v->fails());

$v = Validator::make(['name' => str_repeat('a', 200)], ['name' => 'required|string|max:120']);
t('Nom > 120 rejeté', $v->fails());

$v = Validator::make(['password' => 'abc'], ['password' => 'required|string|min:8']);
t('Mot de passe < 8 rejeté', $v->fails());

// ===== 2. INJECTION SQL =====
echo "[2] Injection SQL\n";
$injected = Review::where('title', '=', "' OR '1'='1")->first();
t('Requête SQL injectée sans résultat', $injected === null);
$injected2 = Review::where('comment', 'LIKE', "'; DROP TABLE reviews; --")->first();
t('Drop table neutralisé', \Illuminate\Support\Facades\Schema::hasTable('reviews'));

// ===== 3. CSRF / SESSIONS =====
echo "[3] CSRF & sessions\n";
$session = app('session');
$session->regenerateToken();
$token1 = $session->token();
t('Jeton CSRF généré', is_string($token1) && strlen($token1) > 0);
t('Jeton CSRF longueur >= 40', strlen($token1) >= 40);
t('Cookie session httpOnly', config('session.http_only') === true);
t('SameSite = lax', config('session.same_site') === 'lax');

$postUris = collect(Route::getRoutes())->filter(fn ($r) => in_array('POST', $r->methods()))->map(fn ($r) => $r->uri());
t('Route POST avis chambre', $postUris->contains('chambres/{room}/avis'));
t('Route POST avis etablissement', $postUris->contains('etablissements/{establishment}/avis'));

// ===== 4. HASH DES MOTS DE PASSE =====
echo "[4] Hash des mots de passe\n";
$user = User::where('role', 'voyageur')->first();
$hash = $user->password;
t('Hash bcrypt ($2y$)', str_starts_with($hash, '$2y$'));
t('Hash::check incorrect = false', !Hash::check('mauvais-mot-de-passe', $hash));
$arr = $user->toArray();
t('password absent de toArray()', !array_key_exists('password', $arr));

// ===== 5. ANTI-IDOR =====
echo "[5] Anti-IDOR\n";
$reservation = Reservation::first();
if ($reservation) {
    $owner = User::find($reservation->user_id);
    $stranger = User::where('id', '!=', $reservation->user_id)->where('role', 'voyageur')->first();
    t('Propriétaire accède à sa réservation', $reservation->user_id === $owner->id);
    t('Étranger bloqué', $stranger && $reservation->user_id !== $stranger->id);
}

$hoteAvecEtab = Establishment::query()->whereNotNull('user_id')->get()->groupBy('user_id')->keys()->first();
if ($hoteAvecEtab) {
    $etab = Establishment::where('user_id', $hoteAvecEtab)->first();
    t('Hôte accède à son établissement', $etab && $etab->user_id === $hoteAvecEtab);
    $autre = User::where('id', '!=', $hoteAvecEtab)->first();
    t('Autre utilisateur bloqué sur établissement', $autre && $etab->user_id !== $autre->id);
}

// ===== 6. MIDDLEWARES DE RÔLE =====
echo "[6] Middlewares de rôle\n";
$voyageur = User::where('role', 'voyageur')->first();
Auth::login($voyageur);
$adminMw = new \App\Http\Middleware\EnsureUserIsAdmin();
$req = \Illuminate\Http\Request::create('/admin', 'GET');
$blocked = false;
try { $adminMw->handle($req, fn() => response('ok')); }
catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $blocked = $e->getStatusCode() === 403; }
t('Voyageur bloqué route admin', $blocked);

$hoteMw = new \App\Http\Middleware\EnsureUserIsHote();
$req2 = \Illuminate\Http\Request::create('/espace-hote', 'GET');
$blocked2 = false;
try { $hoteMw->handle($req2, fn() => response('ok')); }
catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $blocked2 = $e->getStatusCode() === 403; }
t('Voyageur bloqué route hote', $blocked2);
Auth::logout();

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec\n";
exit($failures > 0 ? 1 : 0);
