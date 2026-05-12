<?php

namespace App\Services\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\Sale;
use App\Models\Drinks\SaleArticleLine;
use App\Models\Drinks\StockSnapshot;

class ReportService
{
    public function __construct(
        private readonly CashService $cashService,
    ) {}

    /**
     * Generate the "brouillard" (cash journal) report.
     *
     * Aggregates cash inputs, deposits, expenses, payments and validated sales
     * for a team within a date range.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array{
     *   cash_inputs: array{amount: float, count: int},
     *   cash_deposits: array{amount: float, count: int},
     *   expenses: array{amount: float, count: int},
     *   payments: array{amount: float, count: int},
     *   sales_total: float,
     * }
     */
    public function brouillard(int $teamId, string $dateFrom, string $dateTo): array
    {
        $salesTotal = Sale::where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->sum('total_ttc');

        return [
            'cash_inputs' => $this->cashService->totalCashInputs($teamId, $dateFrom, $dateTo),
            'cash_deposits' => $this->cashService->totalCashDeposits($teamId, $dateFrom, $dateTo),
            'expenses' => $this->cashService->totalExpenses($teamId, $dateFrom, $dateTo),
            'payments' => $this->cashService->totalPayments($teamId, $dateFrom, $dateTo),
            'sales_total' => (float) $salesTotal,
        ];
    }

    /**
     * Generate a sales breakdown grouped by article.
     *
     * Returns validated sale lines grouped by article for a team
     * within a date range, with quantities and amounts.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array<int, array{article_id: int, article_name: string, total_qty: int, total_amount_ht: float}>
     */
    public function salesByArticle(int $teamId, string $dateFrom, string $dateTo): array
    {
        $validatedSaleIds = Sale::where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->pluck('id');

        return SaleArticleLine::with('article')
            ->whereIn('sale_id', $validatedSaleIds)
            ->selectRaw('article_id, SUM(quantity) as total_qty, SUM(amount_ht) as total_amount_ht')
            ->groupBy('article_id')
            ->get()
            ->map(fn ($row) => [
                'article_id' => $row->article_id,
                'article_name' => $row->article->name ?? '',
                'total_qty' => (int) $row->total_qty,
                'total_amount_ht' => (float) $row->total_amount_ht,
            ])
            ->values()
            ->all();
    }

    /**
     * Generate the current stock state for all articles of a team.
     *
     * When a snapshot date is provided, returns the historical snapshot quantities
     * instead of live stock quantities.
     *
     * @param  int  $teamId  The team ID
     * @param  string|null  $date  Optional snapshot date (Y-m-d); uses live stock if null
     * @return array<int, array{article_id: int, article_name: string, stock_qty: int, cost_price: float|null}>
     */
    public function stockState(int $teamId, ?string $date = null): array
    {
        if ($date !== null) {
            return StockSnapshot::with('article')
                ->where('team_id', $teamId)
                ->where('snapshot_date', $date)
                ->get()
                ->map(fn ($snap) => [
                    'article_id' => $snap->article_id,
                    'article_name' => $snap->article->name ?? '',
                    'stock_qty' => (int) $snap->stock_qty,
                    'cost_price' => $snap->cost_price !== null ? (float) $snap->cost_price : null,
                ])
                ->values()
                ->all();
        }

        return Article::where('team_id', $teamId)
            ->get()
            ->map(fn ($article) => [
                'article_id' => $article->id,
                'article_name' => $article->name,
                'stock_qty' => (int) $article->stock_qty,
                'cost_price' => $article->cost_price !== null ? (float) $article->cost_price : null,
            ])
            ->values()
            ->all();
    }

    /**
     * Generate client turnover for a team within a date range.
     *
     * Groups validated sales by client and sums the total_ttc amounts.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array<int, array{client_id: int|null, client_name: string, total_ttc: float, sale_count: int}>
     */
    public function clientTurnover(int $teamId, string $dateFrom, string $dateTo): array
    {
        return Sale::with('client')
            ->where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->selectRaw('client_id, SUM(total_ttc) as total_ttc, COUNT(*) as sale_count')
            ->groupBy('client_id')
            ->get()
            ->map(fn ($row) => [
                'client_id' => $row->client_id,
                'client_name' => $row->client?->name ?? 'Client inconnu',
                'total_ttc' => (float) $row->total_ttc,
                'sale_count' => (int) $row->sale_count,
            ])
            ->values()
            ->all();
    }

    /**
     * Generate a roadmap for a given date.
     *
     * Returns validated sales for the team on the specified date,
     * including client addresses and article details for delivery.
     *
     * @param  string  $date  (Y-m-d)
     * @return array<int, Sale>
     */
    public function roadmap(int $teamId, string $date): array
    {
        return Sale::with(['client', 'articleLines.article'])
            ->where('team_id', $teamId)
            ->where('document_date', $date)
            ->validated()
            ->orderBy('id')
            ->get()
            ->all();
    }
}
