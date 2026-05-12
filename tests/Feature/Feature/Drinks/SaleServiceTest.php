<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\PackagingMovement;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SaleArticleLine;
use App\Models\Drinks\SalePackagingLine;
use App\Models\Drinks\StockMovement;
use App\Models\User;
use App\Services\Drinks\SaleService;

test('validate crée un mouvement SaleOut pour chaque article', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100, 'discount_rate' => 0, 'rebate_rate' => 0]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 10,
        'amount_ht' => 5000,
    ]);

    app(SaleService::class)->validate($sale, $user->id);

    $movement = StockMovement::where('article_id', $article->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->kind)->toBe(StockMovementKind::SaleOut)
        ->and($movement->quantity)->toBe(10);
});

test('validate décrémente stock_qty des articles vendus', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100, 'discount_rate' => 0, 'rebate_rate' => 0]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 25,
        'amount_ht' => 10000,
    ]);

    app(SaleService::class)->validate($sale, $user->id);

    expect($article->fresh()->stock_qty)->toBe(75);
});

test('validate calcule discount_total depuis le taux de discount des articles', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100, 'discount_rate' => 10, 'rebate_rate' => 0]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 10,
        'amount_ht' => 10000,
    ]);

    $result = app(SaleService::class)->validate($sale, $user->id);

    // 10% de 10000 = 1000
    expect((float) $result->discount_total)->toBe(1000.0);
});

test('validate calcule rebate_credit depuis le taux de ristourne des articles', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100, 'discount_rate' => 0, 'rebate_rate' => 5]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 10,
        'amount_ht' => 20000,
    ]);

    $result = app(SaleService::class)->validate($sale, $user->id);

    // 5% de 20000 = 1000
    expect((float) $result->rebate_credit)->toBe(1000.0);
});

test('validate crée des mouvements ConsignmentOut pour les packaging lines', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SalePackagingLine::factory()->create([
        'sale_id' => $sale->id,
        'packaging_id' => $packaging->id,
        'quantity_out' => 12,
        'quantity_returned' => 0,
    ]);

    app(SaleService::class)->validate($sale, $user->id);

    expect($packaging->fresh()->stock_qty)->toBe(38);
    $movement = PackagingMovement::where('packaging_id', $packaging->id)->first();
    expect($movement->kind)->toBe(StockMovementKind::ConsignmentOut);
});

test('validate passe le statut à Validated', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $sale = Sale::factory()->create(['team_id' => $team->id]);

    $result = app(SaleService::class)->validate($sale, $user->id);

    expect($result->status)->toBe(TransactionStatus::Validated)
        ->and($result->validated_by)->toBe($user->id)
        ->and($result->validated_at)->not->toBeNull();
});

test('cancelValidation restore le stock et annule les mouvements', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100, 'discount_rate' => 0, 'rebate_rate' => 0]);
    $sale = Sale::factory()->create(['team_id' => $team->id]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 20,
        'amount_ht' => 10000,
    ]);

    $service = app(SaleService::class);
    $service->validate($sale, $user->id);
    expect($article->fresh()->stock_qty)->toBe(80);

    $service->cancelValidation($sale->fresh());

    expect($article->fresh()->stock_qty)->toBe(100)
        ->and(StockMovement::where('source_type', 'Sale')->where('source_id', $sale->id)->count())->toBe(0);
});

test('cancelValidation repasse le statut à Draft', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $sale = Sale::factory()->create(['team_id' => $team->id]);

    $service = app(SaleService::class);
    $service->validate($sale, $user->id);
    $result = $service->cancelValidation($sale->fresh());

    expect($result->status)->toBe(TransactionStatus::Draft)
        ->and($result->validated_at)->toBeNull()
        ->and((float) $result->discount_total)->toBe(0.0)
        ->and((float) $result->rebate_credit)->toBe(0.0);
});

test('cancelValidation lève une exception si la vente n\'est pas validée', function () {
    $sale = Sale::factory()->create();

    expect(fn () => app(SaleService::class)->cancelValidation($sale))
        ->toThrow(InvalidArgumentException::class);
});
