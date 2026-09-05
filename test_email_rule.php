<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Rules\ValidRealEmail;

$cases = [
    'toto@gmail.com'        => 'attendu: VALIDE',
    'test@mailinator.com'   => 'attendu: JETABLE (bloqué)',
    'user@domain-inexistant-xyz123.com' => 'attendu: PAS DE MX (bloqué)',
    'pas-un-email'          => 'attendu: FORMAT INVALIDE (bloqué)',
    'demo@yahoo.com'        => 'attendu: VALIDE',
];

foreach ($cases as $email => $comment) {
    $rule = new ValidRealEmail();
    $passed = false;
    $message = null;

    $rule->validate('email', $email, function ($msg) use (&$passed, &$message) {
        $passed = true;
        $message = $msg;
    });

    echo str_pad($email, 40, ' ') . ' => ';
    if ($passed) {
        echo 'BLOQUÉ: ' . $message . PHP_EOL;
    } else {
        echo 'ACCEPTÉ' . PHP_EOL;
    }
    echo '   ' . $comment . PHP_EOL;
}

echo 'DONE'.PHP_EOL;