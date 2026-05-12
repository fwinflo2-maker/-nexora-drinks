<?php

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Client;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SaleArticleLine;
use App\Models\Drinks\StockSnapshot;
use App\Models\User;
use App\Services\Drinks\ReportService;

test('brouillard retourne les totaux cash inputs, deposits, expenses, payments et ventes', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    CashInput::factory()->create(['team_id' => $team->id, 'amount' => 50000, 'document_date' => '2026-05-01', 'status' => TransactionStatus::Validated]);
    Expense::factory()->create(['team_id' => $team->id, 'amount' => 10000, 'document_date' => '2026-05-05', 'status' => TransactionStatus::Validated]);
    Sale::factory()->create(['team_id' => $team->id, 'total_ttc' => 80000, 'document_date' => '2026-05-10', 'status' => TransactionStatus::Validated]);

    $result = app(ReportService::class)->brouillard($team->id, '2026-05-01', '2026-05-31');

    expect($result)->toHaveKeys(['cash_inputs', 'cash_deposits', 'expenses', 'payments', 'sales_total'])
        ->and($result['cash_inputs']['amount'])->toBe(50000.0)
        ->and($result['expenses']['amount'])->toBe(10000.0)
        ->and($result['sales_total'])->toBe(80000.0);
});

test('salesByArticle groupe les ventes validées par article', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id]);
    $sale1 = Sale::factory()->create(['team_id' => $team->id, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-01']);
    $sale2 = Sale::factory()->create(['team_id' => $team->id, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-15']);

    SaleArticleLine::factory()->create(['sale_id' => $sale1->id, 'article_id' => $article->id, 'quantity' => 10, 'amount_ht' => 5000]);
    SaleArticleLine::factory()->create(['sale_id' => $sale2->id, 'article_id' => $article->id, 'quantity' => 5, 'amount_ht' => 2500]);

    // Vente non validée — ne doit pas être incluse
    $draftSale = Sale::factory()->create(['team_id' => $team->id, 'status' => TransactionStatus::Draft, 'document_date' => '2026-05-20']);
    SaleArticleLine::factory()->create(['sale_id' => $draftSale->id, 'article_id' => $article->id, 'quantity' => 100, 'amount_ht' => 50000]);

    $result = app(ReportService::class)->salesByArticle($team->id, '2026-05-01', '2026-05-31');

    $row = collect($result)->firstWhere('article_id', $article->id);
    expect($row)->not->toBeNull()
        ->and($row['total_qty'])->toBe(15)
        ->and($row['total_amount_ht'])->toBe(7500.0);
});

test('stockState retourne le stock actuel de tous les articles de la team', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 42]);

    $result = app(ReportService::class)->stockState($team->id);

    $row = collect($result)->firstWhere('article_id', $article->id);
    expect($row)->not->toBeNull()
        ->and($row['stock_qty'])->toBe(42)
        ->and($row['article_name'])->toBe($article->name);
});

test('stockState utilise les snapshots si une date est fournie', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $article = Article::factory()->create(['team_id' => $team->id, 'stock_qty' => 99]);
    StockSnapshot::factory()->create([
        'team_id' => $team->id,
        'article_id' => $article->id,
        'snapshot_date' => '2026-05-01',
        'stock_qty' => 50,
    ]);

    $result = app(ReportService::class)->stockState($team->id, '2026-05-01');

    $row = collect($result)->firstWhere('article_id', $article->id);
    expect($row)->not->toBeNull()
        ->and($row['stock_qty'])->toBe(50); // snapshot, pas le live
});

test('clientTurnover groupe les ventes validées par client', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 100000, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-01']);
    Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 50000, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-15']);

    // Vente draft — ne doit pas être incluse
    Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 999999, 'status' => TransactionStatus::Draft, 'document_date' => '2026-05-20']);

    $result = app(ReportService::class)->clientTurnover($team->id, '2026-05-01', '2026-05-31');

    $row = collect($result)->firstWhere('client_id', $client->id);
    expect($row)->not->toBeNull()
        ->and($row['total_ttc'])->toBe(150000.0)
        ->and($row['sale_count'])->toBe(2)
        ->and($row['client_name'])->toBe($client->name);
});

test('clientTurnover isole par team_id', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;
    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam;

    $client = Client::factory()->create(['team_id' => $team->id]);
    Sale::factory()->create(['team_id' => $team->id, 'client_id' => $client->id, 'total_ttc' => 10000, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-01']);

    $otherClient = Client::factory()->create(['team_id' => $otherTeam->id]);
    Sale::factory()->create(['team_id' => $otherTeam->id, 'client_id' => $otherClient->id, 'total_ttc' => 999999, 'status' => TransactionStatus::Validated, 'document_date' => '2026-05-01']);

    $result = app(ReportService::class)->clientTurnover($team->id, '2026-05-01', '2026-05-31');

    expect(collect($result)->firstWhere('client_id', $otherClient->id))->toBeNull();
    expect(collect($result)->firstWhere('client_id', $client->id))->not->toBeNull();
});
