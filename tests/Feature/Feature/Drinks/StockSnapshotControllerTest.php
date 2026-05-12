<?php

use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\StockSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Listing ───────────────────────────────────────────────────────────────────

test('magasinier peut lister les snapshots', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id]);
    StockSnapshot::factory()->count(3)->create([
        'team_id' => $team->id,
        'article_id' => $article->id,
    ]);

    $this->actingAs($magasinier)
        ->get(route('drinks.stock-snapshots.index', ['current_team' => $team->slug]))
        ->assertOk();
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('magasinier peut voir le détail d\'un snapshot par date', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    $article = Article::factory()->create(['team_id' => $team->id]);
    $date = today()->toDateString();
    StockSnapshot::factory()->create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'snapshot_date' => $date,
    ]);

    $this->actingAs($magasinier)
        ->get(route('drinks.stock-snapshots.show', [
            'current_team' => $team->slug,
            'date' => $date,
        ]))
        ->assertOk();
});

// ── Autorisation (403) ─────────────────────────────────────────────────────────

test('403 pour un caissier sur l\'index snapshots', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.stock-snapshots.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('403 pour un comptable sur l\'index snapshots', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->get(route('drinks.stock-snapshots.index', ['current_team' => $team->slug]))
        ->assertForbidden();
});
