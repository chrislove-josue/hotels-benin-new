<?php

/**
 * Tests unitaires — Modifications en attente (Pending Changes)
 * ------------------------------------------------------------
 * Vérifie les modèles PendingEstablishmentChange et PendingRoomChange,
 * leurs relations, les actions (create/update/delete) et les statuts.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\PendingEstablishmentChange;
use App\Models\PendingRoomChange;
use App\Models\Room;
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

echo "=== TESTS : MODIFICATIONS EN ATTENTE ===\n\n";

// --- Test 1 : Structure des tables ---
echo "[1] Structure des tables\n";
check('Table pending_establishment_changes existe', Schema::hasTable('pending_establishment_changes'));
check('Table pending_room_changes existe', Schema::hasTable('pending_room_changes'));

$pendEtabCols = Schema::getColumnListing('pending_establishment_changes');
$pendRoomCols = Schema::getColumnListing('pending_room_changes');
check('Etab: establishment_id', in_array('establishment_id', $pendEtabCols));
check('Etab: user_id', in_array('user_id', $pendEtabCols));
check('Etab: data (json)', in_array('data', $pendEtabCols));
check('Etab: status', in_array('status', $pendEtabCols));
check('Room: room_id', in_array('room_id', $pendRoomCols));
check('Room: action', in_array('action', $pendRoomCols));
check('Room: data (json)', in_array('data', $pendRoomCols));

// --- Test 2 : Relations ---
echo "[2] Relations PendingEstablishmentChange\n";
$change = PendingEstablishmentChange::first();
if ($change) {
    check('Relation establishment()', $change->establishment instanceof Establishment);
    check('Relation user()', $change->user instanceof User);
    if ($change->reviewed_by) {
        check('Relation reviewer()', $change->reviewer instanceof User);
    }
}

// --- Test 3 : Relations PendingRoomChange ---
echo "[3] Relations PendingRoomChange\n";
$roomChange = PendingRoomChange::first();
if ($roomChange) {
    check('Relation establishment()', $roomChange->establishment instanceof Establishment);
    check('Relation user()', $roomChange->user instanceof User);
    if ($roomChange->room_id) {
        check('Relation room()', $roomChange->room instanceof Room);
    }
}

// --- Test 4 : Actions valides ---
echo "[4] Actions PendingRoomChange\n";
$actions = PendingRoomChange::pluck('action')->unique();
$validActions = ['create', 'update', 'delete'];
foreach ($actions as $action) {
    check("Action '{$action}' est valide", in_array($action, $validActions));
}

// --- Test 5 : Statuts valides ---
echo "[5] Statuts\n";
$statuts = collect(PendingEstablishmentChange::pluck('status')->unique()->merge(PendingRoomChange::pluck('status')->unique()))->unique();
$validStatuses = ['en_attente', 'approuvee', 'rejetee'];
foreach ($statuts as $status) {
    check("Statut '{$status}' valide", in_array($status, $validStatuses));
}

// --- Test 6 : Données JSON ---
echo "[6] Données JSON (data cast)\n";
$changeWithData = PendingEstablishmentChange::whereNotNull('data')->first();
if ($changeWithData) {
    check('data est un tableau (cast array)', is_array($changeWithData->data));
    check('data contient des clés utilisateur', count($changeWithData->data) > 0);
}

// --- Test 7 : Création d'une modification ---
echo "[7] Création PendingEstablishmentChange\n";
$etab = Establishment::first();
$user = User::where('role', 'hote')->first();
if ($etab && $user) {
    $newChange = PendingEstablishmentChange::create([
        'establishment_id' => $etab->id,
        'user_id' => $user->id,
        'data' => ['name' => 'Nouveau Nom', 'description' => 'Nouvelle description'],
        'status' => 'en_attente',
    ]);
    check('Modification créée', (bool) $newChange->id);
    check('Statut par défaut en_attente', $newChange->status === 'en_attente');
    check('data est un tableau', is_array($newChange->data));
    $newChange->delete();
}

// --- Test 8 : Création PendingRoomChange ---
echo "[8] Création PendingRoomChange\n";
$room = Room::first();
if ($room && $user) {
    $newRoomChange = PendingRoomChange::create([
        'room_id' => $room->id,
        'establishment_id' => $room->establishment_id,
        'user_id' => $user->id,
        'action' => 'update',
        'data' => ['name' => 'Chambre Modifiée', 'price_per_night' => 50000],
        'status' => 'en_attente',
    ]);
    check('RoomChange créée', (bool) $newRoomChange->id);
    check('Action = update', $newRoomChange->action === 'update');
    $newRoomChange->delete();
}

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);
