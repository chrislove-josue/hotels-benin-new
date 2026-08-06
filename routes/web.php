<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\AuthPageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\HomeController;
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
