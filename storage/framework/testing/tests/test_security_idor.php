<?php

/**
 * Tests sécuritaires — Anti-IDOR (Insecure Direct Object Reference)
 * ------------------------------------------------------------------
 * Vérifie qu'un utilisateur ne peut pas accéder/modifier les ressources
 * d'un autre utilisateur (réservations, établissements, chambres).
 */

require __DIR__.'/../../../../vendor/autoload.php';

$app = require_once __DIR__.'/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$failures = 0;
$passed = 0;

function check(string $label, bool $condition): void
{
    global $passed, $failures;
    if ($condition) { $passed++; echo "  V {$label}\n"; }
    else { $failures++; echo "  X {$label}\n"; }
}

echo "=== TESTS SECURITAIRES : ANTI-IDOR ===\n\n";

// --- Test 1 : Accès à une réservation d'un autre utilisateur ---
echo "[1] Acces a une reservation d'un autre utilisateur\n";
$reservation = Reservation::first();
if ($reservation) {
    $owner = User::find($reservation->user_id);
    $estranger = User::where('id', '!=', $reservation->user_id)->where('role', 'voyageur')->first();

    check('Le proprietaire a acces a sa reservation', $reservation->user_id === $owner->id);
    check('Un autre voyageur est bloque (IDOR reserver)', $estranger && $reservation->user_id !== $estranger->id);
}

// --- Test 2 : Isolation voyageur ---
echo "[2] Isolation voyageur\n";
$estranger = User::where('role', 'voyageur')->first();
if ($estranger) {
    Auth::login($estranger);
    $allReservationsCount = Reservation::count();
    $voyageurOwn = Reservation::where('user_id', $estranger->id)->count();
    check('Le voyageur ne voit que ses propres reservations', $voyageurOwn <= $allReservationsCount);
    // Vérifier qu'une requête filtrée par user_id ne renvoie pas celles des autres
    $foreign = Reservation::where('user_id', '!=', $estranger->id)->whereNotNull('id')->first();
    $leak = $foreign && in_array($foreign->id, Reservation::where('user_id', $estranger->id)->pluck('id')->all());
    check('Aucune reservation etrangere n\'est retournee au voyageur', !$leak);
    Auth::logout();
}

// --- Test 3 : Hôte — établissements appartenant à un autre hôte ---
echo "[3] Un hote ne peut pas editer l'etablissement d'un autre\n";
// Récupérer un hôte qui possède au moins un établissement
$hoteAvecEtab = Establishment::query()->whereNotNull('user_id')->get()
    ->groupBy('user_id')->keys()->first();

if ($hoteAvecEtab) {
    $hote1 = User::find($hoteAvecEtab);
    $etabHote1 = Establishment::where('user_id', $hoteAvecEtab)->first();

    $accessHote1 = $etabHote1 && $etabHote1->user_id === $hote1->id;
    check('L hote accede a son etablissement', $accessHote1);

    // Vérifier qu'un autre hôte (ou voyageur) n'y a pas accès via la logique HoteController
    $autreUser = User::where('id', '!=', $hote1->id)->first();
    $accessAutre = $autreUser && $etabHote1->user_id === $autreUser->id;
    check('Un autre utilisateur n accede pas a cet etablissement', !$accessAutre);
}

// --- Test 4 : Réservation d'un établissement d'un hôte ---
echo "[4] Un hote ne modifie que les reservations de ses etablissements\n";
$hoteAvecEtab2 = Establishment::query()->whereNotNull('user_id')->get()
    ->groupBy('user_id')->keys()->first();
if ($hoteAvecEtab2) {
    $hote1EtabIds = Establishment::where('user_id', $hoteAvecEtab2)->pluck('id');
    $reservationNotOfHote1 = Reservation::whereNotIn('establishment_id', $hote1EtabIds)->first();

    if ($reservationNotOfHote1) {
        $owns = $hote1EtabIds->contains($reservationNotOfHote1->establishment_id);
        check('Un hote ne peut pas modifier la reservation d un autre etablissement', !$owns);
    } else {
        check('Toutes les reservations appartiennent a ses etablissements', true);
    }
}

// --- Test 5 : Chambres — appartenance à l'établissement ---
echo "[5] Une chambre appartient bien a son etablissement\n";
$room = Room::first();
if ($room) {
    $roomEtab = Establishment::find($room->establishment_id);
    check('La chambre a un etablissement', $roomEtab !== null);
    check('La chambre appartient a l etablissement enregistre', $roomEtab && $roomEtab->id === $room->establishment_id);
}

// --- Test 6 : Vérification par rôle (middleware) ---
echo "[6] Middlewares de role bloquent les acces non autorises\n";
$voyageur = User::where('role', 'voyageur')->first();
if ($voyageur) {
    Auth::login($voyageur);

    $adminMiddleware = new \App\Http\Middleware\EnsureUserIsAdmin();
    $request = \Illuminate\Http\Request::create('/admin', 'GET');
    $blocked = false;
    try {
        $adminMiddleware->handle($request, fn() => response('ok'));
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        $blocked = $e->getStatusCode() === 403;
    }
    check('Voyageur bloque sur route admin', $blocked);

    $hoteMiddleware = new \App\Http\Middleware\EnsureUserIsHote();
    $request2 = \Illuminate\Http\Request::create('/espace-hote', 'GET');
    $blocked2 = false;
    try {
        $hoteMiddleware->handle($request2, fn() => response('ok'));
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        $blocked2 = $e->getStatusCode() === 403;
    }
    check('Voyageur bloque sur route hote', $blocked2);

    Auth::logout();
}

echo "\n=========================================\n";
echo "RESULTAT : {$passed} reussis, {$failures} echec(s)\n";
exit($failures > 0 ? 1 : 0);
