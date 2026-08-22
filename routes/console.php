<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Reservation;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Annule les réservations non payées dont le délai de 24h est dépassé.
Schedule::call(function () {
    Reservation::expireOverdue();
})->name('reservations-expire-overdue')->everyFifteenMinutes()->withoutOverlapping();
