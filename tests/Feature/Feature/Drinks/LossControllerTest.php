<?php

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\Loss;
use App\Models\Drinks\LossLine;
use App\Models\Drinks\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('ops peut lister les pertes', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    Loss::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($ops)
        ->get(route('drinks.losses.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('ops peut créer une perte en brouillon', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100]);

    $this->actingAs($ops)
        ->post(route('drinks.losses.store', ['current_team' => $team->slug]), [
            'document_date' => today()->toDateString(),
            'observation' => 'Casse accidentelle',
            'lines' => [
                ['article_id' => $article->id, 'quantity' => 5],
            ],
        ])
        ->assertRedirect();

    $loss = Loss::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($loss)->not->toBeNull()
        ->and($loss->status)->toBe(TransactionStatus::Draft)
        ->and(LossLine::where('loss_id', $loss->id)->count())->toBe(1);
});

// ── Validate → stock decrement ─────────────────────────────────────────────────

test('validation d\'une perte décrémente le stock et crée un mouvement', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 80]);
    $loss = Loss::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Draft,
        'document_date' => today()->toDateString(),
        'created_by' => $ops->id,
    ]);
    LossLine::factory()->create([
        'loss_id' => $loss->id,
        'article_id' => $article->id,
        'quantity' => 12,
    ]);

    $this->actingAs($ops)
        ->post(route('drinks.losses.validate', [
            'current_team' => $team->slug,
            'loss' => $loss->id,
        ]))
        ->assertRedirect();

    $loss->refresh();
    $article->refresh();

    expect($loss->status)->toBe(TransactionStatus::Validated)
        ->and($loss->validated_at)->not->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()
            ->where('source_type', 'Loss')
            ->where('source_id', $loss->id)
            ->count()
        )->toBe(1)
        ->and($article->stock_qty)->toBe(68); // 80 - 12
});

// ── Cancel validation ──────────────────────────────────────────────────────────

test('annulation de validation remet la perte en brouillon et restaure le stock', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 68]);
    $loss = Loss::factory()->validated()->create([
        'team_id' => $team->id,
        'document_date' => today()->toDateString(),
        'created_by' => $ops->id,
        'validated_by' => $ops->id,
    ]);
    LossLine::factory()->create([
        'loss_id' => $loss->id,
        'article_id' => $article->id,
        'quantity' => 12,
    ]);

    // Mouvement créé lors de la validation originale
    StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => StockMovementKind::Loss,
        'quantity' => 12,
        'source_type' => 'Loss',
        'source_id' => $loss->id,
        'document_date' => today()->toDateString(),
        'created_by' => $ops->id,
    ]);

    $this->actingAs($ops)
        ->post(route('drinks.losses.cancel-validation', [
            'current_team' => $team->slug,
            'loss' => $loss->id,
        ]))
        ->assertRedirect();

    $loss->refresh();
    $article->refresh();

    expect($loss->status)->toBe(TransactionStatus::Draft)
        ->and($loss->validated_at)->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()
            ->where('source_type', 'Loss')
            ->where('source_id', $loss->id)
            ->count()
        )->toBe(0)
        ->and($article->stock_qty)->toBe(80); // 68 + 12 restauré
});

// ── Autorisation (403) ─────────────────────────────────────────────────────────

test('caissier ne peut pas accéder aux pertes', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.losses.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('caissier ne peut pas valider une perte', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);
    [$ops] = drinksMember(TeamRole::Ops);

    $loss = Loss::factory()->create([
        'team_id' => $team->id,
        'created_by' => $ops->id,
    ]);

    $this->actingAs($caissier)
        ->post(route('drinks.losses.validate', [
            'current_team' => $team->slug,
            'loss' => $loss->id,
        ]))
        ->assertForbidden();
});
