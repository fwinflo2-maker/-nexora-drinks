<?php

use App\Http\Controllers\Api\Hotel\StatsController;
use App\Http\Controllers\Hotel\DashboardController;
use App\Http\Controllers\Hotel\FolioController;
use App\Http\Controllers\Hotel\GuestController;
use App\Http\Controllers\Hotel\ReportController;
use App\Http\Controllers\Hotel\ReservationController;
use App\Http\Controllers\Hotel\RoomController;
use App\Http\Controllers\Hotel\RoomTypeController;
use App\Http\Middleware\CheckModuleAccess;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

// ── Web routes — Hotel module ─────────────────────────────────────────────────
Route::prefix('{current_team}/hotel')
    ->name('hotel.')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, CheckModuleAccess::class.':hotel'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Room types
        Route::resource('room-types', RoomTypeController::class)
            ->except(['show'])
            ->parameters(['room-types' => 'roomType']);

        // Rooms
        Route::resource('rooms', RoomController::class)
            ->except(['show']);

        // Guests
        Route::resource('guests', GuestController::class)
            ->parameters(['guests' => 'guest']);

        // Reservations
        Route::resource('reservations', ReservationController::class)
            ->except(['edit', 'update'])
            ->parameters(['reservations' => 'reservation']);

        Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])
            ->name('reservations.check-in');
        Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'quickCheckOut'])
            ->name('reservations.check-out');
        Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])
            ->name('reservations.cancel');

        // Bridge Hotel ↔ F&B
        Route::post('reservations/{reservation}/checkout', [ReservationController::class, 'checkout'])
            ->name('reservations.checkout');
        Route::get('reservations/{reservation}/fnb-orders', [ReservationController::class, 'fnbOrders'])
            ->name('reservations.fnb-orders');
        Route::get('reservations/{reservation}/folio', [FolioController::class, 'summary'])
            ->name('reservations.folio');
        Route::get('reservations/{reservation}/folio/pdf', [FolioController::class, 'pdf'])
            ->name('reservations.folio.pdf');

        // Folios (nested under reservations)
        Route::post('reservations/{reservation}/folios', [FolioController::class, 'store'])
            ->name('reservations.folios.store');
        Route::delete('reservations/{reservation}/folios/{folio}', [FolioController::class, 'destroy'])
            ->name('reservations.folios.destroy');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('reservations', [ReportController::class, 'reservations'])->name('reservations');
            Route::get('reservations/pdf', [ReportController::class, 'reservationsPdf'])->name('reservations.pdf');
            Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('revenue/pdf', [ReportController::class, 'revenuePdf'])->name('revenue.pdf');
            Route::get('occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
            Route::get('occupancy/pdf', [ReportController::class, 'occupancyPdf'])->name('occupancy.pdf');
        });
    });

// ── API routes — Hotel module ─────────────────────────────────────────────────
Route::prefix('api/v1/hotel')
    ->name('api.hotel.')
    ->middleware(['auth', 'verified', 'throttle:60,1', CheckModuleAccess::class.':hotel'])
    ->group(function () {
        Route::get('stats', [StatsController::class, 'stats'])->name('stats');
        Route::get('reservations', [StatsController::class, 'reservations'])->name('reservations');
    });
