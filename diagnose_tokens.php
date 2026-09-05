<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\LoginToken;
use App\Models\User;

$tokens = LoginToken::with('user')->orderBy('id', 'desc')->get();

echo 'Total login_tokens: '.$tokens->count().PHP_EOL;
foreach ($tokens as $t) {
    echo '#' . $t->id . ' user=' . ($t->user?->email ?? '?') . '(' . $t->user_id . ') expires=' . ($t->expires_at?->format('d/m/Y H:i:s') ?? 'N/A') . ' created=' . ($t->created_at?->format('d/m/Y H:i:s') ?? 'N/A') . PHP_EOL;
}

// Cherche les utilisateurs "récents" créés via inscription.
$newUsers = User::whereDate('created_at', '>=', now()->subDays(3))->get();
echo PHP_EOL.'Users created last 3 days: '.$newUsers->count().PHP_EOL;
foreach ($newUsers as $u) {
    $tokenCount = LoginToken::where('user_id', $u->id)->count();
    echo sprintf('%s (%d) role=%s approved=%d tokens=%d%n', $u->email, $u->id, $u->role, $u->is_approved, $tokenCount);
}

echo 'DONE'.PHP_EOL;