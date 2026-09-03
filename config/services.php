<?php

return [
    'fedapay' => [
        'public_key'     => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key'     => env('FEDAPAY_SECRET_KEY'),
        'environment'    => env('FEDAPAY_ENVIRONMENT', 'sandbox'),
        'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
    ],

    // Les clés KKiaPay sont stockées en base (modifiables par l'admin) avec un
    // repli sur ces variables d'environnement.
    'kkiapay' => [
        'public_key'  => env('KKIAPAY_PUBLIC_KEY'),
        'private_key' => env('KKIAPAY_PRIVATE_KEY'),
        'secret'      => env('KKIAPAY_SECRET'),
        'environment' => env('KKIAPAY_ENVIRONMENT', 'sandbox'),
    ],
];