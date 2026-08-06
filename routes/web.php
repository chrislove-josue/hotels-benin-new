<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthPageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HoteController;
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
});

Route::post('/deconnexion', [AuthPageController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Back-office — Hôte
|--------------------------------------------------------------------------
| Espace réservé aux utilisateurs ayant le rôle "hote".
*/
Route::prefix('espace-hote')->name('hote.')->middleware(['auth', 'hote'])->group(function () {
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
    Route::patch('/utilisateurs/{id}', [AdminController::class, 'updateUserRole'])->name('users.update');

    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::patch('/reservations/{id}', [AdminController::class, 'updateReservation'])->name('reservations.update');

    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/read', [AdminController::class, 'markMessageRead'])->name('messages.read');
});
