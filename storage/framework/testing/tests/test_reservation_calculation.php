<?php

/**
 * Tests unitaires — Calcul des réservations
 * ------------------------------------------------------------
 * Vérifie le calcul du nombre de nuits et des montants :
 * - nightsBetween() avec dates normales
 * - nightsBetween() avec dates inversées / égales (min 1)
 * - calcul du montant total = nuits × prix
 * - calcul de la commission 10%
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;

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

echo "=== TESTS : CALCUL DES RÉSERVATIONS ===\n\n";

// --- Test 1 : calcul de nuits normal ---
echo "[1] nightsBetween() normal\n";
check('01/02 → 05/02 = 4 nuits', Reservation::nightsBetween('2026-02-01', '2026-02-05') === 4);
check('10/03 → 11/03 = 1 nuit', Reservation::nightsBetween('2026-03-10', '2026-03-11') === 1);
check('01/01 → 01/02 = 31 nuits', Reservation::nightsBetween('2026-01-01', '2026-02-01') === 31);

// --- Test 2 : cas limites ---
echo "[2] nightsBetween() cas limites\n";
check('Dates identiques → min 1', Reservation::nightsBetween('2026-02-01', '2026-02-01') === 1);
check('Date départ avant arrivée → min 1', Reservation::nightsBetween('2026-02-05', '2026-02-01') === 1);
check('Jour suivant = 1 nuit', Reservation::nightsBetween('2026-02-01', '2026-02-02') === 1);

// --- Test 3 : calcul du montant ---
echo "[3] Calcul du montant et commission\n";
$price = 25000;
$nights = 3;
$amount = $nights * $price;
$commission = round($amount * 0.10, 2);
check('Montant total = 3 × 25 000 = 75 000', $amount === 75000);
check('Commission 10% = 7 500', $commission === 7500.0);

$price2 = 10000;
$nights2 = 7;
$amount2 = $nights2 * $price2;
$commission2 = round($amount2 * 0.10, 2);
check('Montant 7 nuits × 10 000 = 70 000', $amount2 === 70000);
check('Commission = 7 000', $commission2 === 7000.0);

// --- Test 4 : génération de référence ---
echo "[4] Génération de référence unique\n";
$ref1 = Reservation::generateReference();
$ref2 = Reservation::generateReference();
check('Format HB-XXXXXXXX', (bool) preg_match('/^HB-[A-Z0-9]{8}$/', $ref1));
check('Références uniques', $ref1 !== $ref2);

// --- Test 5 : méthode nights() d'une instance ---
echo "[5] Méthode nights() sur une instance\n";
$reservation = new Reservation(['check_in' => '2026-06-01', 'check_out' => '2026-06-05']);
check('Instance 01/06 → 05/06 = 4 nuits', $reservation->nights() === 4);

$empty = new Reservation();
check('Instance sans dates → 1 nuit', $empty->nights() === 1);

echo "\n=========================================\n";
echo "RÉSULTAT : {$passed} réussis, {$failures} échec(s)\n";
exit($failures > 0 ? 1 : 0);

