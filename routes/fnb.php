<?php

use App\Http\Controllers\Api\FnB\StatsController;
use App\Http\Controllers\FnB\CategoryController;
use App\Http\Controllers\FnB\DashboardController;
use App\Http\Controllers\FnB\MenuItemController;
use App\Http\Controllers\FnB\OrderController;
use App\Http\Controllers\FnB\ReportController;
use App\Http\Controllers\FnB\RoomSearchController;
use App\Http\Controllers\FnB\RoomServiceController;
use App\Http\Controllers\FnB\TableController;
use App\Http\Middleware\CheckModuleAccess;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

// ── Web routes — F&B module ───────────────────────────────────────────────────
Route::prefix('{current_team}/fnb')
    ->name('fnb.')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, CheckModuleAccess::class.':fnb'])
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('kitchen', [DashboardController::class, 'kitchen'])->name('kitchen');

        // Tables
        Route::get('tables', [TableController::class, 'index'])->name('tables.index');
        Route::post('tables', [TableController::class, 'store'])->name('tables.store');
        Route::put('tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');

        // Categories
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Menu items
        Route::get('menu-items', [MenuItemController::class, 'index'])->name('menu-items.index');
        Route::get('menu-items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
        Route::post('menu-items', [MenuItemController::class, 'store'])->name('menu-items.store');
        Route::get('menu-items/{menuItem}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit');
        Route::put('menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
        Route::delete('menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');
        Route::post('menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
        Route::delete('orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');
        Route::post('orders/{order}/send', [OrderController::class, 'sendToKitchen'])->name('orders.send');
        Route::post('orders/{order}/items/{item}/status', [OrderController::class, 'updateItemStatus'])->name('orders.items.status');
        Route::post('orders/{order}/close', [OrderController::class, 'close'])->name('orders.close');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        // Bridge Hotel ↔ F&B
        Route::patch('orders/{order}/attach-reservation', [OrderController::class, 'attachToReservation'])->name('orders.attach-reservation');
        Route::patch('orders/{order}/detach-reservation', [OrderController::class, 'detachFromReservation'])->name('orders.detach-reservation');
        Route::get('room-search', [RoomSearchController::class, 'search'])->name('room-search');
        Route::post('room-service', [RoomServiceController::class, 'store'])->name('room-service.store');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('orders', [ReportController::class, 'orders'])->name('orders');
            Route::get('orders/pdf', [ReportController::class, 'ordersPdf'])->name('orders.pdf');
            Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('revenue/pdf', [ReportController::class, 'revenuePdf'])->name('revenue.pdf');
            Route::get('menu', [ReportController::class, 'menu'])->name('menu');
            Route::get('menu/pdf', [ReportController::class, 'menuPdf'])->name('menu.pdf');
        });
    });

// ── API routes — F&B module ───────────────────────────────────────────────────
Route::prefix('api/v1/fnb')
    ->name('api.fnb.')
    ->middleware(['auth', 'verified', 'throttle:60,1', CheckModuleAccess::class.':fnb'])
    ->group(function () {
        Route::get('stats', [StatsController::class, 'stats'])->name('stats');
        Route::get('orders', [StatsController::class, 'orders'])->name('orders');
    });
