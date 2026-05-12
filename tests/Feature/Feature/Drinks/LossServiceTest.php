<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\Loss;
use App\Models\Drinks\LossLine;
use App\Models\Drinks\StockMovement;
use App\Models\User;
use App\Services\Drinks\LossService;

test('validate crée un mouvement Loss pour chaque ligne', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $loss = Loss::factory()->create(['team_id' => $team->id]);
    LossLine::factory()->create([
        'loss_id' => $loss->id,
        'article_id' => $article->id,
        'quantity' => 8,
    ]);

    app(LossService::class)->validate($loss, $user->id);

    $movement = StockMovement::where('article_id', $article->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->kind)->toBe(StockMovementKind::Loss)
        ->and($movement->quantity)->toBe(8)
        ->and($movement->source_type)->toBe('Loss')
        ->and($movement->source_id)->toBe($loss->id);
});

test('validate décrémente stock_qty de chaque article', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $loss = Loss::factory()->create(['team_id' => $team->id]);
    LossLine::factory()->create([
        'loss_id' => $loss->id,
        'article_id' => $article->id,
        'quantity' => 12,
    ]);

    app(LossService::class)->validate($loss, $user->id);

    expect($article->fresh()->stock_qty)->toBe(38);
});

test('validate gère plusieurs lignes', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article1 = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100]);
    $article2 = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 60]);
    $loss = Loss::factory()->create(['team_id' => $team->id]);
    LossLine::factory()->create(['loss_id' => $loss->id, 'article_id' => $article1->id, 'quantity' => 10]);
    LossLine::factory()->create(['loss_id' => $loss->id, 'article_id' => $article2->id, 'quantity' => 5]);

    app(LossService::class)->validate($loss, $user->id);

    expect($article1->fresh()->stock_qty)->toBe(90)
        ->and($article2->fresh()->stock_qty)->toBe(55)
        ->and(StockMovement::where('source_type', 'Loss')->where('source_id', $loss->id)->count())->toBe(2);
});

test('validate passe le statut à Validated', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $loss = Loss::factory()->create(['team_id' => $team->id]);

    $result = app(LossService::class)->validate($loss, $user->id);

    expect($result->status)->toBe(TransactionStatus::Validated)
        ->and($result->validated_by)->toBe($user->id)
        ->and($result->validated_at)->not->toBeNull();
});

test('cancelValidation restore le stock et supprime les mouvements', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $loss = Loss::factory()->create(['team_id' => $team->id]);
    LossLine::factory()->create([
        'loss_id' => $loss->id,
        'article_id' => $article->id,
        'quantity' => 10,
    ]);

    $service = app(LossService::class);
    $service->validate($loss, $user->id);
    expect($article->fresh()->stock_qty)->toBe(40);

    $service->cancelValidation($loss->fresh());

    expect($article->fresh()->stock_qty)->toBe(50)
        ->and(StockMovement::where('source_type', 'Loss')->where('source_id', $loss->id)->count())->toBe(0);
});

test('cancelValidation repasse le statut à Draft', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $loss = Loss::factory()->create(['team_id' => $team->id]);

    $service = app(LossService::class);
    $service->validate($loss, $user->id);
    $result = $service->cancelValidation($loss->fresh());

    expect($result->status)->toBe(TransactionStatus::Draft)
        ->and($result->validated_at)->toBeNull();
});

test('cancelValidation lève une exception si la perte n\'est pas validée', function () {
    $loss = Loss::factory()->create();

    expect(fn () => app(LossService::class)->cancelValidation($loss))
        ->toThrow(InvalidArgumentException::class);
});
