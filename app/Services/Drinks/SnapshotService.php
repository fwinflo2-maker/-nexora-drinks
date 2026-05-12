<?php

declare(strict_types=1);

namespace App\Services\Drinks;

use App\Models\Drinks\Article;
use App\Models\Drinks\StockSnapshot;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SnapshotService
{
    /**
     * Take a stock snapshot for all active articles of the given team.
     *
     * Uses upsert to ensure idempotency on (team_id, snapshot_date, article_id).
     *
     * @return int Number of articles snapshotted
     */
    public function take(Team $team, ?Carbon $date = null): int
    {
        $snapshotDate = ($date ?? Carbon::today())->toDateString();

        $articles = Article::withoutGlobalScopes()
            ->where('team_id', $team->id)
            ->where('is_active', true)
            ->get(['id', 'team_id', 'stock_qty', 'cost_price']);

        if ($articles->isEmpty()) {
            return 0;
        }

        $rows = $articles->map(fn (Article $article) => [
            'team_id' => $team->id,
            'snapshot_date' => $snapshotDate,
            'article_id' => $article->id,
            'stock_qty' => $article->stock_qty,
            'cost_price' => $article->cost_price,
            'created_at' => now(),
            'updated_at' => now(),
        ])->values()->all();

        StockSnapshot::withoutGlobalScopes()->upsert(
            $rows,
            ['team_id', 'snapshot_date', 'article_id'],
            ['stock_qty', 'cost_price', 'updated_at'],
        );

        Log::info("SnapshotService: {$articles->count()} articles snapshotés pour la team {$team->id} au {$snapshotDate}.");

        return $articles->count();
    }
}
