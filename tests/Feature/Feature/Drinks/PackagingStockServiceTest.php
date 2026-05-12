<?php

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\PackagingMovement;
use App\Models\User;
use App\Services\Drinks\PackagingStockService;

test('record crée un mouvement packaging et incrémente stock_qty pour un mouvement positif', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 100]);

    $service = app(PackagingStockService::class);
    $movement = $service->record(
        packaging: $packaging,
        kind: StockMovementKind::ProcurementIn,
        quantity: 24,
        sourceType: 'Procurement',
        sourceId: 1,
        documentDate: '2026-05-01',
        createdBy: $user->id,
    );

    expect($movement)->toBeInstanceOf(PackagingMovement::class)
        ->and($movement->kind)->toBe(StockMovementKind::ProcurementIn)
        ->and($movement->quantity)->toBe(24)
        ->and($movement->packaging_id)->toBe($packaging->id)
        ->and($movement->team_id)->toBe($team->id);

    expect($packaging->fresh()->stock_qty)->toBe(124);
});

test('record décrémente stock_qty pour un mouvement ConsignmentOut', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);

    $service = app(PackagingStockService::class);
    $service->record(
        packaging: $packaging,
        kind: StockMovementKind::ConsignmentOut,
        quantity: 12,
        sourceType: 'SalePackagingLine',
        sourceId: 5,
        documentDate: '2026-05-01',
        createdBy: $user->id,
    );

    expect($packaging->fresh()->stock_qty)->toBe(38);
});

test('record incrémente stock_qty pour un ConsignmentReturn', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 20]);

    $service = app(PackagingStockService::class);
    $service->record(
        packaging: $packaging,
        kind: StockMovementKind::ConsignmentReturn,
        quantity: 6,
        sourceType: 'SalePackagingLine',
        sourceId: 3,
        documentDate: '2026-05-01',
        createdBy: $user->id,
    );

    expect($packaging->fresh()->stock_qty)->toBe(26);
});

test('record attache le bon created_by et source', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);

    $service = app(PackagingStockService::class);
    $movement = $service->record(
        packaging: $packaging,
        kind: StockMovementKind::ProcurementIn,
        quantity: 10,
        sourceType: 'Procurement',
        sourceId: 42,
        documentDate: '2026-05-01',
        createdBy: $user->id,
    );

    expect($movement->created_by)->toBe($user->id)
        ->and($movement->source_type)->toBe('Procurement')
        ->and($movement->source_id)->toBe(42);
});
