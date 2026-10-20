<?php

use App\Http\Controllers\Admin\AppointmentActionController;
use App\Http\Controllers\Admin\BlockedDateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Booking\AppointmentController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('home');

Route::get('disponibilidad', [AppointmentController::class, 'availability'])
    ->name('booking.availability');

Route::post('citas', [AppointmentController::class, 'store'])
    ->middleware('throttle:12,1')
    ->name('appointments.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/admin/dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('disponibilidad', [DashboardController::class, 'availability'])->name('availability');

        Route::patch('appointments/{appointment}/confirm', [AppointmentActionController::class, 'confirm'])
            ->name('appointments.confirm');
        Route::patch('appointments/{appointment}/cancel', [AppointmentActionController::class, 'cancel'])
            ->name('appointments.cancel');
        Route::patch('appointments/{appointment}/reschedule', [AppointmentActionController::class, 'reschedule'])
            ->name('appointments.reschedule');

        Route::post('blocked-dates', [BlockedDateController::class, 'store'])
            ->name('blocked-dates.store');
        Route::delete('blocked-dates/{blockedDate}', [BlockedDateController::class, 'destroy'])
            ->name('blocked-dates.destroy');
    });
});

require __DIR__.'/settings.php';
