<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\InventoryLine;
use App\Models\Drinks\StockMovement;
use App\Models\User;
use App\Services\Drinks\InventoryService;

test('validate crée un mouvement InventoryAdjust quand counted_qty diffère du stock actuel', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 10]);
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 15, // delta = +5
    ]);

    app(InventoryService::class)->validate($inventory, $user->id);

    $movement = StockMovement::where('article_id', $article->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->kind)->toBe(StockMovementKind::InventoryAdjust)
        ->and($movement->quantity)->toBe(5); // abs(delta)
});

test('validate incrémente stock_qty quand counted_qty > stock', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 10]);
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 20,
    ]);

    app(InventoryService::class)->validate($inventory, $user->id);

    expect($article->fresh()->stock_qty)->toBe(20);
});

test('validate décrémente stock_qty quand counted_qty < stock', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 30]);
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 20,
    ]);

    app(InventoryService::class)->validate($inventory, $user->id);

    expect($article->fresh()->stock_qty)->toBe(20);
});

test('validate ne crée pas de mouvement si counted_qty === stock_qty', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 15]);
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 15, // pas de delta
    ]);

    app(InventoryService::class)->validate($inventory, $user->id);

    expect(StockMovement::where('article_id', $article->id)->count())->toBe(0);
    expect($article->fresh()->stock_qty)->toBe(15);
});

test('validate passe le statut à Validated', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);

    $result = app(InventoryService::class)->validate($inventory, $user->id);

    expect($result->status)->toBe(TransactionStatus::Validated)
        ->and($result->validated_by)->toBe($user->id)
        ->and($result->validated_at)->not->toBeNull();
});

test('cancelValidation reverse les ajustements et restore le stock', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 10]);
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);
    InventoryLine::factory()->create([
        'inventory_id' => $inventory->id,
        'article_id' => $article->id,
        'counted_qty' => 20,
    ]);

    $service = app(InventoryService::class);
    $service->validate($inventory, $user->id);
    expect($article->fresh()->stock_qty)->toBe(20);

    $service->cancelValidation($inventory->fresh());

    expect($article->fresh()->stock_qty)->toBe(10)
        ->and(StockMovement::where('source_type', 'Inventory')->where('source_id', $inventory->id)->count())->toBe(0);
});

test('cancelValidation repasse le statut à Draft', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $inventory = Inventory::factory()->create(['team_id' => $team->id]);

    $service = app(InventoryService::class);
    $service->validate($inventory, $user->id);
    $result = $service->cancelValidation($inventory->fresh());

    expect($result->status)->toBe(TransactionStatus::Draft)
        ->and($result->validated_at)->toBeNull();
});

test('cancelValidation lève une exception si l\'inventaire n\'est pas validé', function () {
    $inventory = Inventory::factory()->create();

    expect(fn () => app(InventoryService::class)->cancelValidation($inventory))
        ->toThrow(InvalidArgumentException::class);
});
