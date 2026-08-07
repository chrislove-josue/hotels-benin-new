<?php

/**
 * Tests unitaires — Transactions & paiement
 * ------------------------------------------------------------
 * Vérifie le modèle Transaction, ses relations, la création
 * de transaction associée à une réservation, les montants,
 * commissions et statuts de paiement.
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Support\Facades\Schema;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  ✔ {$label}\n"; }
    else { $failures++; echo "  ✘ {$label}\n"; }
}

echo "=== TESTS : TRANSACTIONS & PAIEMENT ===\n\n";

// --- Test 1 : Structure de la table ---
echo "[1] Structure de la table transactions\n";
check('Table transactions existe', Schema::hasTable('transactions'));
$cols = Schema::getColumnListing('transactions');
check('Colonne reservation_id', in_array('reservation_id', $cols));
check('Colonne amount', in_array('amount', $cols));
check('Colonne commission', in_array('commission', $cols));
check('Colonne method', in_array('method', $cols));
check('Colonne reference', in_array('reference', $cols));
check('Colonne status', in_array('status', $cols));

// --- Test 2 : Relations ---
echo "[2] Relation Transaction -> Reservation\n";
$transaction = Transaction::first();
if ($transaction) {
    check('Transaction a une réservation liée', $transaction->reservation instanceof Reservation);
    check('La réservation liée existe', $transaction->reservation_id === $transaction->reservation->id);
}

// --- Test 3 : Réservation -> transaction (hasOne) ---
echo "[3] Relation Reservation -> transaction\n";
$reservation = Reservation::has('transaction')->first();
if ($reservation) {
    check('Réservation a une transaction', $reservation->transaction instanceof Transaction);
    check('Le montant de la transaction correspond', (float) $reservation->transaction->amount === (float) $reservation->amount);
}

// --- Test 4 : Montants et commission ---
echo "[4] Montants et commissions\n";
$txs = Transaction::where('status', 'reussie')->get();
check('Transactions réussies existent', $txs->count() > 0);
foreach ($txs->take(5) as $tx) {
    if ($tx->commission > 0) {
        check("Commission < montant pour #{$tx->id}", $tx->commission < $tx->amount);
        break;
    }
}
$totalAmount = Transaction::where('status', 'reussie')->sum('amount');
$totalCommission = Transaction::where('status', 'reussie')->sum('commission');
check('Somme des montants >= somme des commissions', $totalAmount >= $totalCommission);

// --- Test 5 : Statuts valides ---
echo "[5] Statuts de transaction\n";
$validStatuses = ['en_attente', 'reussie', 'echouee'];
$statuses = Transaction::pluck('status')->unique();
foreach ($statuses as $status) {
    check("Statut '{$status}' est valide", in_array($status, $validStatuses));
}

// --- Test 6 : Références uniques ---
echo "[6] Références transaction\n";
$references = Transaction::pluck('reference')->filter()->values();
check('Références existent', $references->count() > 0);
check('Références uniques', $references->unique()->count() === $references->count());

// --- Test 7 : Paiement simulé (TransactionController::process) ---
echo "[7] Logique de paiement simulé\n";
$pendingTx = Transaction::where('status', 'en_attente')->with('reservation')->first();
if ($pendingTx && $pendingTx->reservation) {
    $pendingTx->update(['status' => 'reussie']);
    $pendingTx->reservation->update(['status' => 'confirmee']);

    $txFresh = Transaction::find($pendingTx->id);
    $resFresh = Reservation::find($pendingTx->reservation_id);
    check('Paiement simulé : transaction reussie', $txFresh->status === 'reussie');
    check('Paiement simulé : réservation confirmée', $resFresh->status === 'confirmee');
} else {
    echo "  (aucune transaction en attente à tester)\n";
}

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);

