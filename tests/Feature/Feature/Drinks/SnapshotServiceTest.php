<?php

use App\Models\Drinks\Article;
use App\Models\Drinks\StockSnapshot;
use App\Models\Team;
use App\Services\Drinks\SnapshotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('take() crée des snapshots pour tous les articles actifs de la team', function () {
    $team = Team::factory()->create();
    Article::factory()->count(3)->create(['team_id' => $team->id, 'is_active' => true]);

    $service = new SnapshotService;
    $count = $service->take($team, Carbon::today());

    expect($count)->toBe(3)
        ->and(StockSnapshot::withoutGlobalScopes()->where('team_id', $team->id)->count())->toBe(3);
});

test('take() ignore les articles inactifs', function () {
    $team = Team::factory()->create();
    Article::factory()->count(2)->create(['team_id' => $team->id, 'is_active' => true]);
    Article::factory()->count(3)->create(['team_id' => $team->id, 'is_active' => false]);

    $service = new SnapshotService;
    $count = $service->take($team, Carbon::today());

    expect($count)->toBe(2)
        ->and(StockSnapshot::withoutGlobalScopes()->where('team_id', $team->id)->count())->toBe(2);
});

test('take() est idempotent (upsert — pas de doublon)', function () {
    $team = Team::factory()->create();
    Article::factory()->count(2)->create(['team_id' => $team->id, 'is_active' => true]);

    $service = new SnapshotService;
    $date = Carbon::today();

    $service->take($team, $date);
    $service->take($team, $date);

    expect(StockSnapshot::withoutGlobalScopes()->where('team_id', $team->id)->count())->toBe(2);
});

test('take() retourne le bon count', function () {
    $team = Team::factory()->create();
    Article::factory()->count(5)->create(['team_id' => $team->id, 'is_active' => true]);
    Article::factory()->count(2)->create(['team_id' => $team->id, 'is_active' => false]);

    $service = new SnapshotService;
    $count = $service->take($team, Carbon::today());

    expect($count)->toBe(5);
});

test('take() utilise aujourd\'hui par défaut', function () {
    $team = Team::factory()->create();
    Article::factory()->count(2)->create(['team_id' => $team->id, 'is_active' => true]);

    $service = new SnapshotService;
    $service->take($team);

    $snapshots = StockSnapshot::withoutGlobalScopes()
        ->where('team_id', $team->id)
        ->where('snapshot_date', today()->toDateString())
        ->get();

    expect($snapshots)->toHaveCount(2);
});

test('take() copie correctement stock_qty et cost_price de l\'article', function () {
    $team = Team::factory()->create();
    $article = Article::factory()->create([
        'team_id' => $team->id,
        'is_active' => true,
        'stock_qty' => 42,
        'cost_price' => 1500,
    ]);

    $service = new SnapshotService;
    $service->take($team, Carbon::today());

    $snapshot = StockSnapshot::withoutGlobalScopes()
        ->where('team_id', $team->id)
        ->where('article_id', $article->id)
        ->first();

    expect($snapshot)->not->toBeNull()
        ->and($snapshot->stock_qty)->toBe(42)
        ->and((float) $snapshot->cost_price)->toBe(1500.0);
});
