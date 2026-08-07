<?php
// Vérifie le rendu de la vue établissement avec un utilisateur authentifié.
require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Trouver un voyageur
$user = User::where('role', 'voyageur')->first();
Auth::login($user);

$establishment = Establishment::with(['locality', 'type', 'images', 'amenities', 'rooms', 'reviews.user', 'reviews.room'])
    ->where('slug', 'hotel-du-lac-cotonou')
    ->firstOrFail();
$establishment->load(['rooms.reviews' => fn ($q) => $q->with('user')->latest()]);

$similar = collect();

// Partager un ViewErrorBag vide (normalement injecté par le middleware en requête HTTP)
\Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag);

// Rendre la vue
$html = view('establishments.show', compact('establishment', 'similar'))->render();

$foundRoom = str_contains($html, 'Donner mon avis sur cette chambre');
$foundEtab = str_contains($html, 'Donner mon avis sur');

echo "=== RENDU AUTHENTIFIÉ ===\n";
echo "Formulaire avis chambre : ".($foundRoom ? 'OUI' : 'NON')."\n";
echo "Formulaire avis etablissement : ".($foundEtab ? 'OUI' : 'NON')."\n";
echo "Nom du voyageur testé : {$user->name} (id {$user->id})\n";
echo "Établissement : {$establishment->name} ({$establishment->rooms->count()} chambres)\n";

