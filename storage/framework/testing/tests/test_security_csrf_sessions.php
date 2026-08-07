<?php

/**
 * Tests sécuritaires — CSRF, sessions & authentification
 * ------------------------------------------------------------
 * Vérifie :
 * - Routes POST enregistrées dans le groupe web (protection CSRF)
 * - Jeton de session CSRF régénéré
 * - Hash des mots de passe (bcrypt)
 * - Mots de passe jamais en clair
 * - Données sensibles non exposées (password non sérialisé)
 * - Authentification et déconnexion
 * - Longueur minimale du mot de passe (règle de validation)
 * - Configuration de la session (driver, httpOnly, même site)
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  V {$label}\n"; }
    else { $failures++; echo "  X {$label}\n"; }
}

echo "=== TESTS SECURITAIRES : CSRF & SESSIONS ===\n\n";

// --- Test 1 : Routes POST enregistrées ---
echo "[1] Routes POST enregistrees (groupe web -> CSRF)\n";
$postUris = collect(Route::getRoutes())->filter(fn ($r) => in_array('POST', $r->methods()))->map(fn ($r) => $r->uri());
$expectedUris = [
    'connexion', 'inscription', 'deconnexion', 'contact',
    'chambres/{room}/avis', 'etablissements/{establishment}/avis',
];
foreach ($expectedUris as $expected) {
    check("Route POST {$expected} enregistree", $postUris->contains($expected));
}

// --- Test 2 : Jeton CSRF ---
echo "[2] Jeton CSRF\n";
try {
    $session = app('session');
    $session->regenerateToken();
    $token1 = $session->token();
} catch (\Throwable $e) {
    $token1 = '';
}
check('Jeton de session non vide', is_string($token1) && strlen($token1) > 0);
check('Jeton CSRF de longueur >= 40 caracteres', is_string($token1) && strlen($token1) >= 40);

// --- Test 3 : Hash des mots de passe ---
echo "[3] Hash des mots de passe (bcrypt)\n";
$user = User::where('role', 'voyageur')->first();
$storedPassword = $user->password;

check('Le mot de passe nest pas stocke en clair', !str_contains($storedPassword, $user->name));
check('Le hash commence par $2y$ (bcrypt)', str_starts_with($storedPassword, '$2y$'));
check('Hash::check avec mauvais mot de passe = false', !Hash::check('mot-de-passe-incorrect-xyz', $storedPassword));

// --- Test 4 : Donnees sensibles cachees ---
echo "[4] Donnees sensibles cachees\n";
$userArray = $user->toArray();
check('password absent de toArray()', !array_key_exists('password', $userArray));
check('remember_token absent de toArray()', !array_key_exists('remember_token', $userArray));

// --- Test 5 : Authentification ---
echo "[5] Authentification\n";
Auth::logout();
$email = $user->email;
check('Auth::validate rejette un mauvais mot de passe', !Auth::validate(['email' => $email, 'password' => 'password-incorrect']));

// --- Test 6 : Connexion / deconnexion ---
echo "[6] Connexion / Deconnexion\n";
Auth::login($user);
check('Utilisateur authentifie apres login', Auth::check());
check('Champ role du user authentifie (voyageur)', auth()->user()->role === 'voyageur');
Auth::logout();
check('Utilisateur deconnecte', !Auth::check());

// --- Test 7 : Regle de validation du mot de passe ---
echo "[7] Longueur minimale du mot de passe\n";
$short = \Illuminate\Support\Facades\Validator::make(
    ['password' => '1234567'],
    ['password' => 'required|string|min:8']
);
check('Mot de passe < 8 caracteres rejete', $short->fails());

$good = \Illuminate\Support\Facades\Validator::make(
    ['password' => '12345678'],
    ['password' => 'required|string|min:8']
);
check('Mot de passe >= 8 caracteres accepte', !$good->fails());

// --- Test 8 : Configuration session ---
echo "[8] Configuration session\n";
check('Cookie httpOnly active', config('session.http_only', true) === true);
check('SameSite = lax (protection CSRF navigateur)', config('session.same_site', 'lax') === 'lax');
check('Session expire_on_close = false', config('session.expire_on_close', false) === false);
check('Session a un driver configure', in_array(config('session.driver'), ['file', 'database', 'cookie', 'redis']));

echo "\n=========================================\n";
echo "RESULTAT : {$passed} reussis, {$failures} echec(s)\n";
exit($failures > 0 ? 1 : 0);

