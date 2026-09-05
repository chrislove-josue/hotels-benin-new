<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Support\Str;

$user = User::find(36);

if (! $user) {
    echo 'User 36 introuvable'.PHP_EOL;
    exit(1);
}

LoginToken::where('user_id', $user->id)->delete();

$plain = Str::random(48);

$user->loginTokens()->create([
    'token' => hash('sha256', $plain),
    'expires_at' => now()->addHours(24),
]);

$url = route('account.validate', ['token' => $plain]);

echo 'Compte: '.$user->email.' ('.$user->role.')'.PHP_EOL;
echo 'Approuvé: '.($user->is_approved ? 'oui' : 'non').PHP_EOL;
echo 'Lien magique: '.$url.PHP_EOL;

echo 'DONE'.PHP_EOL;