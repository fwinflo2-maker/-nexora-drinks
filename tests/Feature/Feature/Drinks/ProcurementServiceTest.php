<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\ProcurementArticleLine;
use App\Models\Drinks\ProcurementPackagingLine;
use App\Models\Drinks\StockMovement;
use App\Models\Drinks\StockSnapshot;
use App\Models\User;
use App\Services\Drinks\ProcurementService;

test('validate enregistre des mouvements ProcurementIn pour chaque article', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 30,
    ]);

    app(ProcurementService::class)->validate($procurement, $user->id);

    expect(StockMovement::where('article_id', $article->id)->count())->toBe(1);
    $movement = StockMovement::where('article_id', $article->id)->first();
    expect($movement->kind)->toBe(StockMovementKind::ProcurementIn)
        ->and($movement->quantity)->toBe(30)
        ->and($movement->source_type)->toBe('Procurement')
        ->and($movement->source_id)->toBe($procurement->id);
});

test('validate incrémente stock_qty des articles', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 10]);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 50,
    ]);

    app(ProcurementService::class)->validate($procurement, $user->id);

    expect($article->fresh()->stock_qty)->toBe(60);
});

test('validate enregistre des mouvements pour les packaging lines', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $packaging = Packaging::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);
    ProcurementPackagingLine::factory()->create([
        'procurement_id' => $procurement->id,
        'packaging_id' => $packaging->id,
        'quantity' => 100,
    ]);

    app(ProcurementService::class)->validate($procurement, $user->id);

    expect($packaging->fresh()->stock_qty)->toBe(100);
});

test('validate passe le statut à Validated', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);

    $result = app(ProcurementService::class)->validate($procurement, $user->id);

    expect($result->status)->toBe(TransactionStatus::Validated)
        ->and($result->validated_by)->toBe($user->id)
        ->and($result->validated_at)->not->toBeNull();
});

test('validate crée des snapshots de stock pour les articles de la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 5]);
    $procurement = Procurement::factory()->create([
        'team_id' => $team->id,
        'document_date' => '2026-05-01',
    ]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 10,
    ]);

    app(ProcurementService::class)->validate($procurement, $user->id);

    expect(StockSnapshot::where('team_id', $team->id)
        ->where('snapshot_date', '2026-05-01')
        ->exists()
    )->toBeTrue();
});

test('cancelValidation annule les mouvements et restore le stock', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 0]);
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);
    ProcurementArticleLine::factory()->create([
        'procurement_id' => $procurement->id,
        'article_id' => $article->id,
        'quantity_received' => 20,
    ]);

    $service = app(ProcurementService::class);
    $service->validate($procurement, $user->id);

    expect($article->fresh()->stock_qty)->toBe(20);

    $service->cancelValidation($procurement->fresh());

    expect($article->fresh()->stock_qty)->toBe(0)
        ->and(StockMovement::where('source_type', 'Procurement')->where('source_id', $procurement->id)->count())->toBe(0);
});

test('cancelValidation repasse le statut à Draft', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $procurement = Procurement::factory()->create(['team_id' => $team->id]);

    $service = app(ProcurementService::class);
    $service->validate($procurement, $user->id);
    $result = $service->cancelValidation($procurement->fresh());

    expect($result->status)->toBe(TransactionStatus::Draft)
        ->and($result->validated_at)->toBeNull()
        ->and($result->validated_by)->toBeNull();
});

test('cancelValidation lève une exception si le procurement n\'est pas validé', function () {
    $procurement = Procurement::factory()->create();

    expect(fn () => app(ProcurementService::class)->cancelValidation($procurement))
        ->toThrow(InvalidArgumentException::class);
});
