<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthPageController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HoteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web — partie vitrine (publique)
|--------------------------------------------------------------------------
| Aucune route de back-office ici : uniquement les pages visibles par les
| visiteurs et utilisateurs non connectés, conformément au cahier des charges.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/etablissements', [EstablishmentController::class, 'index'])->name('establishments.index');
Route::get('/etablissements/{slug}', [EstablishmentController::class, 'show'])->name('establishments.show');

Route::get('/evenements', [EventController::class, 'index'])->name('events.index');
Route::get('/evenements/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/a-propos', [AboutController::class, 'index'])->name('about');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::prefix('connexion')->name('login.')->group(function () {
    Route::get('/', [AuthPageController::class, 'showLogin'])->name('show');
    Route::post('/', [AuthPageController::class, 'login'])->name('submit');
});

Route::prefix('inscription')->name('register.')->group(function () {
    Route::get('/', [AuthPageController::class, 'showRegister'])->name('show');
    Route::post('/', [AuthPageController::class, 'register'])->name('submit');
    Route::get('/compte-en-attente', [AuthPageController::class, 'pending'])->name('pending');
});

Route::post('/deconnexion', [AuthPageController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Impersonation — retour à la session administrateur
|--------------------------------------------------------------------------
| Accessible avec une simple session "auth" (PAS le middleware admin) :
// pendant l'impersonation, l'utilisateur connecté est l'hôte/voyageur.
*/
Route::post('/impersonation/quitter', [AdminController::class, 'stopImpersonation'])
    ->middleware(['auth'])
    ->name('impersonation.stop');

/*
|--------------------------------------------------------------------------
| Récupération de compte — mot de passe oublié
|--------------------------------------------------------------------------
| Envoi du lien par e-mail puis réinitialisation via le broker Laravel
| (table password_reset_tokens, lien valable 60 minutes).
*/
Route::prefix('mot-de-passe')->name('password.')->group(function () {
    Route::get('/oublie', [PasswordResetController::class, 'requestForm'])->name('request');
    Route::post('/lien', [PasswordResetController::class, 'sendResetLink'])->name('email');
    Route::get('/reinitialiser/{token}', [PasswordResetController::class, 'resetForm'])->name('reset');
    Route::post('/reinitialiser', [PasswordResetController::class, 'reset'])->name('update');
});

/*
|--------------------------------------------------------------------------
| Avis sur les chambres
|--------------------------------------------------------------------------
| Tout utilisateur connecté peut laisser un avis sur une chambre.
*/
Route::post('/chambres/{room}/avis', [ReviewController::class, 'store'])
    ->middleware(['auth'])
    ->name('reviews.store');

Route::post('/etablissements/{establishment}/avis', [ReviewController::class, 'storeEstablishment'])
    ->middleware(['auth'])
    ->name('reviews.establishments.store');

/*
|--------------------------------------------------------------------------
| Réservations & espace voyageur
|--------------------------------------------------------------------------
| Création d'une réservation, paiement simulé et espace personnel du voyageur.
*/
Route::post('/etablissements/{establishment:slug}/reserver', [ReservationController::class, 'store'])
    ->middleware('auth')
    ->name('reservations.store');

Route::prefix('espace-voyageur')->name('reservations.')->middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [ReservationController::class, 'myReservations'])->name('index');

    // Mes avis
    Route::get('/mes-avis', [ReviewController::class, 'myReviews'])->name('my_reviews');
    Route::patch('/mes-avis/{review}', [ReviewController::class, 'updateOwn'])->name('reviews.update');
    Route::delete('/mes-avis/{review}', [ReviewController::class, 'destroyOwn'])->name('reviews.destroy');
    Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
    Route::get('/{reservation}/confirmation', [ReservationController::class, 'confirm'])->name('confirm');

    // Paiement
    Route::get('/{reservation}/paiement', [TransactionController::class, 'checkout'])->name('checkout');
    Route::post('/{reservation}/paiement', [TransactionController::class, 'process'])->name('process');
    Route::get('/{reservation}/paiement/fedapay/retour', [TransactionController::class, 'fedapayCallback'])->name('fedapay.callback');
});


/*
|--------------------------------------------------------------------------
| Webhooks — Paiement FedaPay
|--------------------------------------------------------------------------
| Appelé par les serveurs FedaPay (pas de session, pas de CSRF).
*/
Route::post('/webhooks/fedapay', [TransactionController::class, 'fedapayWebhook'])
    ->name('webhooks.fedapay');

/*
|--------------------------------------------------------------------------
| Back-office — Hôte
|--------------------------------------------------------------------------
| Espace réservé aux utilisateurs ayant le rôle "hote".
*/
Route::prefix('espace-hote')->name('hote.')->middleware(['auth', 'hote', 'approved'])->group(function () {
    Route::get('/', [HoteController::class, 'dashboard'])->name('dashboard');
    Route::get('/etablissements/nouveau', [HoteController::class, 'createEstablishment'])->name('establishments.create');
    Route::post('/etablissements', [HoteController::class, 'storeEstablishment'])->name('establishments.store');
    Route::get('/etablissements/{id}/modifier', [HoteController::class, 'editEstablishment'])->name('establishments.edit');
    Route::put('/etablissements/{id}', [HoteController::class, 'updateEstablishment'])->name('establishments.update');

    // Gestion dédiée des chambres par établissement
    Route::get('/etablissements/{establishmentId}/chambres', [HoteController::class, 'rooms'])->name('rooms.index');
    Route::get('/etablissements/{establishmentId}/chambres/{roomId}/modifier', [HoteController::class, 'editRoom'])->name('rooms.edit');
    Route::post('/etablissements/{establishmentId}/chambres', [HoteController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/etablissements/{establishmentId}/chambres/{roomId}', [HoteController::class, 'updateRoom'])->name('rooms.update');
    Route::delete('/etablissements/{establishmentId}/chambres/{roomId}', [HoteController::class, 'deleteRoom'])->name('rooms.destroy');

    Route::get('/reservations', [HoteController::class, 'reservations'])->name('reservations');
    Route::patch('/reservations/{id}', [HoteController::class, 'updateReservation'])->name('reservations.update');

    // Avis reçus sur ses établissements et chambres
    Route::get('/avis', [HoteController::class, 'reviews'])->name('avis');
});

/*
|--------------------------------------------------------------------------
| Back-office — Administrateur
|--------------------------------------------------------------------------
| Espace réservé aux utilisateurs ayant le rôle "admin".
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

Route::get('/etablissements', [AdminController::class, 'establishments'])->name('establishments');
    Route::get('/etablissements/{id}/modifier', [AdminController::class, 'editEstablishment'])->name('establishments.edit');
    Route::put('/etablissements/{id}', [AdminController::class, 'updateEstablishment'])->name('establishments.update');
    Route::post('/etablissements/{id}/toggle', [AdminController::class, 'toggleEstablishment'])->name('establishments.toggle');
    Route::delete('/etablissements/{id}', [AdminController::class, 'deleteEstablishment'])->name('establishments.destroy');
    Route::get('/etablissements/{id}/chambres', [AdminController::class, 'establishmentRooms'])->name('establishments.rooms');
    Route::get('/etablissements/{establishmentId}/chambres/{roomId}/modifier', [AdminController::class, 'editRoom'])->name('rooms.edit');
    Route::put('/etablissements/{establishmentId}/chambres/{roomId}', [AdminController::class, 'updateRoom'])->name('rooms.update');

    // Modifications en attente d'approbation (hôtels + chambres)
    Route::get('/modifications', [AdminController::class, 'pendingChanges'])->name('pending');
    Route::post('/modifications/etablissements/{id}/approuver', [AdminController::class, 'approvePendingEstablishment'])->name('pending.establishments.approve');
    Route::post('/modifications/etablissements/{id}/rejeter', [AdminController::class, 'rejectPendingEstablishment'])->name('pending.establishments.reject');
    Route::post('/modifications/chambres/{id}/approuver', [AdminController::class, 'approvePendingRoom'])->name('pending.rooms.approve');
    Route::post('/modifications/chambres/{id}/rejeter', [AdminController::class, 'rejectPendingRoom'])->name('pending.rooms.reject');

Route::get('/utilisateurs', [AdminController::class, 'users'])->name('users');
    Route::get('/utilisateurs/nouveau', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/utilisateurs', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/utilisateurs/{id}/modifier', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/utilisateurs/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/utilisateurs/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/utilisateurs/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::post('/utilisateurs/{id}/approuver', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/utilisateurs/{id}/rejeter', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::get('/utilisateurs/{id}/impersonner', [AdminController::class, 'impersonate'])->name('users.impersonate');

    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::patch('/reservations/{id}', [AdminController::class, 'updateReservation'])->name('reservations.update');

    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::patch('/transactions/{id}', [AdminController::class, 'updateTransaction'])->name('transactions.update');

Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/read', [AdminController::class, 'markMessageRead'])->name('messages.read');

    // Avis clients (modération)
    Route::get('/avis', [AdminController::class, 'reviews'])->name('avis');
    Route::patch('/avis/{id}', [AdminController::class, 'updateReview'])->name('avis.update');
    Route::delete('/avis/{id}', [AdminController::class, 'destroyReview'])->name('avis.destroy');

    // Gestion des événements
    Route::get('/evenements', [AdminController::class, 'events'])->name('events');
    Route::get('/evenements/nouveau', [AdminController::class, 'createEvent'])->name('events.create');
    Route::post('/evenements', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::get('/evenements/{id}/modifier', [AdminController::class, 'editEvent'])->name('events.edit');
    Route::put('/evenements/{id}', [AdminController::class, 'updateEvent'])->name('events.update');
    Route::post('/evenements/{id}/toggle', [AdminController::class, 'toggleEvent'])->name('events.toggle');
    Route::delete('/evenements/{id}', [AdminController::class, 'deleteEvent'])->name('events.destroy');
});
