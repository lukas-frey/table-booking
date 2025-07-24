<?php

use App\Livewire\Actions\CancelReservation;
use App\Livewire\Reservations\CreateReservation;
use App\Livewire\Reservations\ListReservations;
use App\Livewire\Reservations\ReservationSuccessPrompt;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->group(function () {

        Route::get('/', ListReservations::class)
            ->name('reservations.index')
        ;

        Route::get('reservations/create', CreateReservation::class)
            ->name('reservations.create')
        ;

        Route::get('reservations/{reservation}/thank-you', ReservationSuccessPrompt::class)
            ->name('reservations.success')
        ;

        Route::post('reservations/{reservation}/cancel', CancelReservation::class)
            ->name('reservations.cancel')
            ;
    })
;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
});

require __DIR__ . '/auth.php';
