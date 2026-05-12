<?php

use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\Client;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SaleArticleLine;
use App\Models\Drinks\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin peut lister les ventes', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    Sale::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($admin)
        ->get(route('drinks.sales.index', ['current_team' => $team->slug]))
        ->assertOk();
});

test('caissier peut créer une vente en brouillon', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $client = Client::factory()->create(['team_id' => $team->id]);
    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 100]);

    $this->actingAs($caissier)
        ->post(route('drinks.sales.store', ['current_team' => $team->slug]), [
            'kind' => 'normal',
            'document_date' => today()->toDateString(),
            'client_id' => $client->id,
            'observation' => 'Test vente',
            'lines' => [
                ['article_id' => $article->id, 'quantity' => 5, 'unit_price' => 1000],
            ],
        ])
        ->assertRedirect();

    $sale = Sale::withoutGlobalScopes()->where('team_id', $team->id)->first();

    expect($sale)->not->toBeNull()
        ->and($sale->status)->toBe(TransactionStatus::Draft)
        ->and((float) $sale->total_ht)->toEqual(5000.0)
        ->and(SaleArticleLine::where('sale_id', $sale->id)->count())->toBe(1);
});

test('validation d\'une vente crée des mouvements de stock', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 50]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'status' => TransactionStatus::Draft,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 10,
        'unit_price' => 500,
        'amount_ht' => 5000,
        'amount_ttc' => 5962.5,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.sales.validate', [
            'current_team' => $team->slug,
            'sale' => $sale->id,
        ]))
        ->assertRedirect();

    $sale->refresh();

    expect($sale->status)->toBe(TransactionStatus::Validated)
        ->and($sale->validated_at)->not->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()->where('source_type', 'Sale')->where('source_id', $sale->id)->count())->toBe(1);
});

test('annulation de validation remet la vente en brouillon', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 40]);
    $client = Client::factory()->create(['team_id' => $team->id]);
    $sale = Sale::factory()->validated()->create([
        'team_id' => $team->id,
        'client_id' => $client->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
        'validated_by' => $admin->id,
    ]);
    SaleArticleLine::factory()->create([
        'sale_id' => $sale->id,
        'article_id' => $article->id,
        'quantity' => 10,
        'unit_price' => 500,
        'amount_ht' => 5000,
        'amount_ttc' => 5962.5,
    ]);

    StockMovement::create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'kind' => 'sale_out',
        'quantity' => 10,
        'source_type' => 'Sale',
        'source_id' => $sale->id,
        'document_date' => today()->toDateString(),
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->post(route('drinks.sales.cancel-validation', [
            'current_team' => $team->slug,
            'sale' => $sale->id,
        ]))
        ->assertRedirect();

    $sale->refresh();

    expect($sale->status)->toBe(TransactionStatus::Draft)
        ->and($sale->validated_at)->toBeNull()
        ->and(StockMovement::withoutGlobalScopes()->where('source_type', 'Sale')->where('source_id', $sale->id)->count())->toBe(0);
});

test('magasinier ne peut pas accéder aux ventes', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $this->actingAs($magasinier)
        ->get(route('drinks.sales.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('magasinier ne peut pas valider une vente', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);
    [$admin] = drinksMember(TeamRole::Admin);

    $sale = Sale::factory()->create([
        'team_id' => $team->id,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($magasinier)
        ->post(route('drinks.sales.validate', [
            'current_team' => $team->slug,
            'sale' => $sale->id,
        ]))
        ->assertForbidden();
});
