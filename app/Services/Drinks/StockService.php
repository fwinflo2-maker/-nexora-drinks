<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\Article;
use App\Models\Drinks\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record a stock movement for an article.
     *
     * Creates a StockMovement record and updates the Article's stock_qty
     * atomically within a database transaction.
     *
     * @param  Article  $article  The article whose stock is being moved
     * @param  StockMovementKind  $kind  The type of movement
     * @param  int  $quantity  The quantity being moved
     * @param  string  $sourceType  The morphable source type (e.g., 'Procurement', 'Sale')
     * @param  int  $sourceId  The morphable source ID
     * @param  \DateTime|Carbon|string  $documentDate  The document date for the movement
     * @param  int  $createdBy  The user ID creating this movement
     * @return StockMovement The created movement record
     */
    public function record(
        Article $article,
        StockMovementKind $kind,
        int $quantity,
        string $sourceType,
        int $sourceId,
        $documentDate,
        int $createdBy,
    ): StockMovement {
        return DB::transaction(function () use ($article, $kind, $quantity, $sourceType, $sourceId, $documentDate, $createdBy) {
            // Create the stock movement record
            $movement = StockMovement::create([
                'team_id' => $article->team_id,
                'article_id' => $article->id,
                'kind' => $kind,
                'quantity' => $quantity,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'document_date' => $documentDate,
                'created_by' => $createdBy,
            ]);

            // Update the article stock based on movement kind
            if ($kind->isPositive()) {
                $article->increment('stock_qty', $quantity);
            } else {
                $article->decrement('stock_qty', $quantity);
            }

            return $movement;
        });
    }
}
