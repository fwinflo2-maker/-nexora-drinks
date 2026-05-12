<?php

use App\Enums\Drinks\TransactionStatus;
use App\Enums\TeamRole;
use App\Models\Drinks\Article;
use App\Models\Drinks\Expense;
use App\Models\Drinks\ExpenseType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ── Brouillard ────────────────────────────────────────────────────────────────

test('comptable peut accéder au brouillard', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $this->actingAs($comptable)
        ->get(route('drinks.reports.brouillard', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/reports/brouillard'));
});

test('brouillard contient les données correctes', function () {
    [$comptable, $team] = drinksMember(TeamRole::Comptable);

    $expenseType = ExpenseType::factory()->create(['team_id' => $team->id]);

    Expense::factory()->create([
        'team_id' => $team->id,
        'status' => TransactionStatus::Validated,
        'validated_at' => now(),
        'amount' => 20000,
        'document_date' => today(),
        'expense_type_id' => $expenseType->id,
        'created_by' => $comptable->id,
    ]);

    $this->actingAs($comptable)
        ->get(route('drinks.reports.brouillard', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/reports/brouillard')
            ->has('data.expenses')
            ->has('data.cash_inputs')
            ->has('data.cash_deposits')
            ->has('data.payments')
            ->has('data.sales_total')
        );
});

// ── Sales Report ──────────────────────────────────────────────────────────────

test('gérant peut accéder au rapport des ventes', function () {
    [$gerant, $team] = drinksMember(TeamRole::Gerant);

    $this->actingAs($gerant)
        ->get(route('drinks.reports.sales', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/reports/sales-report'));
});

// ── Stock Report ──────────────────────────────────────────────────────────────

test('magasinier peut accéder au rapport des stocks', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    Article::factory()->count(3)->create(['team_id' => $team->id]);

    $this->actingAs($magasinier)
        ->get(route('drinks.reports.stock', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/reports/stock-report')
            ->has('rows', 3)
        );
});

// ── Client Report ─────────────────────────────────────────────────────────────

test('gérant peut accéder au rapport CA clients', function () {
    [$gerant, $team] = drinksMember(TeamRole::Gerant);

    $this->actingAs($gerant)
        ->get(route('drinks.reports.clients', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/reports/client-report'));
});

// ── Dashboard ─────────────────────────────────────────────────────────────────

test('admin peut accéder au dashboard drinks', function () {
    [$admin, $team] = drinksMember(TeamRole::Admin);

    $this->actingAs($admin)
        ->get(route('drinks.dashboard', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/dashboard')
            ->has('stats')
            ->has('role')
        );
});

test('le dashboard adapte les stats au rôle magasinier', function () {
    [$magasinier, $team] = drinksMember(TeamRole::Magasinier);

    Article::factory()->count(2)->create(['team_id' => $team->id]);

    $this->actingAs($magasinier)
        ->get(route('drinks.dashboard', ['current_team' => $team->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('drinks/dashboard')
            ->where('role', 'magasinier')
            ->where('stats.articles_count', 2)
        );
});

// ── 403 ───────────────────────────────────────────────────────────────────────

test('caissier ne peut pas accéder au brouillard', function () {
    [$caissier, $team] = drinksMember(TeamRole::Caissier);

    $this->actingAs($caissier)
        ->get(route('drinks.reports.brouillard', ['current_team' => $team->slug]))
        ->assertForbidden();
});

test('ops ne peut pas accéder au brouillard', function () {
    [$ops, $team] = drinksMember(TeamRole::Ops);

    $this->actingAs($ops)
        ->get(route('drinks.reports.brouillard', ['current_team' => $team->slug]))
        ->assertForbidden();
});
