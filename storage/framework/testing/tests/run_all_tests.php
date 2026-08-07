<?php

/**
 * Lanceur de tests unitaires — Hôtels Bénin
 * ------------------------------------------------------------
 * Exécute tous les fichiers de test du dossier et affiche un
 * récapitulatif global. Chaque script retourne 0 si tous les
 * tests passent, 1 sinon.
 *
 * Usage : php storage/framework/testing/tests/run_all_tests.php
 */

$testDir = __DIR__;
$testFiles = glob($testDir.'/test_*.php');

$globalFailures = 0;
$globalPassed = 0;
$suites = 0;

echo "============================================================\n";
echo "  SUITE DE TESTS — HÔTELS BÉNIN\n";
echo "============================================================\n\n";

foreach ($testFiles as $file) {
    if (basename($file) === 'run_all_tests.php') {
        continue;
    }

    $suites++;
    echo "▶ Lancement : ".basename($file)."\n";
    echo str_repeat('─', 60)."\n";

$output = [];
    $exitCode = 0;
    // PHP_BINARY contient souvent des espaces (C:\Program Files\...)
    // => on encadre le binaire et le script entre guillemets.
    exec('"'.PHP_BINARY.'" "'.$file.'" 2>&1', $output, $exitCode);

    echo implode("\n", $output)."\n";
    echo str_repeat('─', 60)."\n\n";

// Extraire les compteurs depuis le résumé (gère accents et sans accents)
    foreach ($output as $line) {
        if (preg_match('/(\d+) r(é|e)ussis, (\d+) (é|e)chec/', $line, $m)) {
            $globalPassed += (int) $m[1];
            $globalFailures += (int) $m[3];
        }
    }
}

echo "============================================================\n";
echo "  RÉCAPITULATIF GLOBAL\n";
echo "============================================================\n";
echo "  Suites exécutées  : {$suites}\n";
echo "  Tests réussis     : {$globalPassed}\n";
echo "  Tests échoués     : {$globalFailures}\n";
echo "============================================================\n";

if ($globalFailures > 0) {
    echo "  → ÉCHEC : certains tests ne passent pas.\n";
    exit(1);
}

echo "  → SUCCÈS : tous les tests passent. ✅\n";
exit(0);

