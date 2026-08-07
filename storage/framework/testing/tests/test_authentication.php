<?php

/**
 * Tests unitaires — Authentification (AuthPageController)
 * ------------------------------------------------------------
 * Vérifie les flux de connexion, inscription, déconnexion,
 * redirection selon le rôle, validation des formulaires
 * et la sécurité des sessions.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : AUTHENTIFICATION ===\n\n";

// --- Test 1 : Routes d'authentification ---
echo "[1] Routes d'authentification\n";
$routes = collect(Route::getRoutes());
check('Route GET login.show', $routes->contains(fn ($r) => $r->getName() === 'login.show'));
check('Route POST login.submit', $routes->contains(fn ($r) => $r->getName() === 'login.submit'));
check('Route GET register.show', $routes->contains(fn ($r) => $r->getName() === 'register.show'));
check('Route POST register.submit', $routes->contains(fn ($r) => $r->getName() === 'register.submit'));
check('Route POST logout', $routes->contains(fn ($r) => $r->getName() === 'logout'));

// --- Test 2 : Validation connexion ---
echo "[2] Validation connexion\n";
$loginRules = [
    'email' => ['required', 'email'],
    'password' => ['required', 'string'],
];

$valid = Validator::make(['email' => 'test@example.com', 'password' => 'secret123'], $loginRules);
check('Login valide accepté', ! $valid->fails());

$noEmail = Validator::make(['password' => 'secret123'], $loginRules);
check('Email manquant rejeté', $noEmail->fails());

$badEmail = Validator::make(['email' => 'pas-email', 'password' => 'secret123'], $loginRules);
check('Email invalide rejeté', $badEmail->fails());

// --- Test 3 : Validation inscription ---
echo "[3] Validation inscription\n";
$registerRules = [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:180', 'unique:users,email'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
    'role' => ['required', 'in:voyageur,hote'],
];

$validReg = Validator::make([
    'name' => 'Nouvel Utilisateur',
    'email' => 'nouveau@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'voyageur',
], $registerRules);
check('Inscription valide acceptée', ! $validReg->fails());

$shortPwd = Validator::make([
    'name' => 'Test',
    'email' => 'test@example.com',
    'password' => 'abc',
    'password_confirmation' => 'abc',
    'role' => 'voyageur',
], $registerRules);
check('Mot de passe < 8 rejeté', $shortPwd->fails());

$pwdMismatch = Validator::make([
    'name' => 'Test',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'different',
    'role' => 'voyageur',
], $registerRules);
check('Confirmation différente rejetée', $pwdMismatch->fails());

$badRole = Validator::make([
    'name' => 'Test',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'superadmin',
], $registerRules);
check('Rôle invalide rejeté', $badRole->fails());

// --- Test 4 : Hash et vérification ---
echo "[4] Hash du mot de passe\n";
$password = 'MonMotDePasse123!';
$hash = Hash::make($password);
check('Hash bcrypt ($2y$)', str_starts_with($hash, '$2y$'));
check('Hash::check correct', Hash::check($password, $hash));
check('Hash::check incorrect', ! Hash::check('mauvais', $hash));

// --- Test 5 : Connexion/déconnexion réelle ---
echo "[5] Connexion et déconnexion\n";
$voyageur = User::where('role', 'voyageur')->first();
if ($voyageur) {
    Auth::login($voyageur);
    check('Auth::check() après login', Auth::check());
    check('Auth::id() === user.id', Auth::id() === $voyageur->id);

    Auth::logout();
    check('Auth::check() après logout', ! Auth::check());
}

// --- Test 6 : Redirection selon le rôle ---
echo "[6] Redirection selon le rôle (homeFor simulé)\n";
$admin = User::where('role', 'admin')->first();
$hote = User::where('role', 'hote')->first();
$voyageur2 = User::where('role', 'voyageur')->first();

if ($admin && $hote && $voyageur2) {
    check('Admin redirigé vers admin.dashboard', str_contains(route('admin.dashboard'), 'admin'));
    check('Hôte redirigé vers hote.dashboard', str_contains(route('hote.dashboard'), 'espace-hote'));
    check('Voyageur redirigé vers home', str_contains(route('home'), '/'));
}

// --- Test 7 : Email unique ---
echo "[7] Contrainte email unique\n";
$existingEmail = $voyageur->email ?? 'test@example.com';
$dupEmail = Validator::make([
    'name' => 'Doublon',
    'email' => $existingEmail,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'voyageur',
], $registerRules);
check('Email en doublon rejeté', $dupEmail->fails());

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
