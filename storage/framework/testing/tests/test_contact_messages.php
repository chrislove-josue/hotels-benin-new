<?php

/**
 * Tests unitaires — Messages de contact
 * ------------------------------------------------------------
 * Vérifie le modèle ContactMessage, la création de messages,
 * la validation des entrées, le champ is_read et les routes
 * de contact.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Route;
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

echo "=== TESTS : MESSAGES DE CONTACT ===\n\n";

// --- Test 1 : Structure ---
echo "[1] Structure de la table contact_messages\n";
check('Table contact_messages existe', Schema::hasTable('contact_messages'));
$cols = Schema::getColumnListing('contact_messages');
check('Colonne name', in_array('name', $cols));
check('Colonne email', in_array('email', $cols));
check('Colonne subject', in_array('subject', $cols));
check('Colonne message', in_array('message', $cols));
check('Colonne is_read', in_array('is_read', $cols));

// --- Test 2 : Création ---
echo "[2] Création d'un message\n";
$msg = ContactMessage::create([
    'name' => 'Test Contact',
    'email' => 'test.contact@example.com',
    'subject' => 'Demande de renseignements',
    'message' => 'Bonjour, je souhaite des informations sur vos établissements.',
]);
check('Message créé avec un id', (bool) $msg->id);
check('is_read défaut à 0/false', ! $msg->is_read);

// --- Test 3 : Validation (comme ContactController) ---
echo "[3] Validation des champs\n";
$valid = Validator::make([
    'name' => 'Jean Dupont',
    'email' => 'jean@example.com',
    'message' => 'Message valide',
], [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:180'],
    'message' => ['required', 'string', 'max:3000'],
]);
check('Formulaire valide accepté', ! $valid->fails());

$missingName = Validator::make([
    'email' => 'jean@example.com',
    'message' => 'Message',
], [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:180'],
    'message' => ['required', 'string', 'max:3000'],
]);
check('Nom manquant rejeté', $missingName->fails());

$badEmail = Validator::make([
    'name' => 'Jean',
    'email' => 'pas-un-email',
    'message' => 'Message',
], [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:180'],
    'message' => ['required', 'string', 'max:3000'],
]);
check('Email invalide rejeté', $badEmail->fails());

$longMsg = Validator::make([
    'name' => 'Jean',
    'email' => 'jean@example.com',
    'message' => str_repeat('a', 3500),
], [
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'max:180'],
    'message' => ['required', 'string', 'max:3000'],
]);
check('Message > 3000 rejeté', $longMsg->fails());

// --- Test 4 : marquage lu ---
echo "[4] Marquage lu / non lu\n";
$unread = ContactMessage::where('is_read', false)->first();
if ($unread) {
    $unread->update(['is_read' => true]);
    $fresh = ContactMessage::find($unread->id);
    check('Message marqué lu', $fresh->is_read == true);
}

// --- Test 5 : Routes contact ---
echo "[5] Routes contact\n";
$routes = collect(Route::getRoutes());
check('Route GET contact.create', $routes->contains(fn ($r) => $r->getName() === 'contact.create'));
check('Route POST contact.store', $routes->contains(fn ($r) => $r->getName() === 'contact.store'));

// Nettoyage
$msg->delete();

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);

