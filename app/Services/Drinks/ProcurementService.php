<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Article;
use App\Models\Drinks\Procurement;
use App\Models\Drinks\StockMovement;
use App\Models\Drinks\StockSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProcurementService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly PackagingStockService $packagingStockService,
    ) {}

    /**
     * Validate a procurement transaction.
     *
     * Records stock movements for all article lines and packaging lines,
     * creates an end-of-day stock snapshot, and updates procurement status.
     *
     * @param  Procurement  $procurement  The procurement to validate
     * @param  int  $validatedBy  The user ID validating the procurement
     * @return Procurement The updated procurement
     */
    public function validate(Procurement $procurement, int $validatedBy): Procurement
    {
        return DB::transaction(function () use ($procurement, $validatedBy) {
            // Eager-load lines with their related models to avoid N+1
            $procurement->load('articleLines.article', 'packagingLines.packaging');

            // Record stock movements for all article lines
            foreach ($procurement->articleLines as $line) {
                $this->stockService->record(
                    article: $line->article,
                    kind: StockMovementKind::ProcurementIn,
                    quantity: $line->quantity_received,
                    sourceType: 'Procurement',
                    sourceId: $procurement->id,
                    documentDate: $procurement->document_date,
                    createdBy: $validatedBy,
                );
            }

            // Record stock movements for all packaging lines
            foreach ($procurement->packagingLines as $line) {
                $this->packagingStockService->record(
                    packaging: $line->packaging,
                    kind: StockMovementKind::ProcurementIn,
                    quantity: (int) $line->quantity,
                    sourceType: 'Procurement',
                    sourceId: $procurement->id,
                    documentDate: $procurement->document_date,
                    createdBy: $validatedBy,
                );
            }

            // Create or refresh end-of-day stock snapshots for all team articles
            $this->refreshStockSnapshots(
                teamId: $procurement->team_id,
                snapshotDate: is_string($procurement->document_date)
                    ? $procurement->document_date
                    : $procurement->document_date->format('Y-m-d'),
            );

            // Update procurement status
            $procurement->update([
                'status' => TransactionStatus::Validated,
                'validated_at' => now(),
                'validated_by' => $validatedBy,
            ]);

            return $procurement->refresh();
        });
    }

    /**
     * Cancel validation of a procurement.
     *
     * Reverses all stock movements for this procurement and restores
     * article/packaging stock quantities.
     *
     * @param  Procurement  $procurement  The procurement to cancel
     * @return Procurement The updated procurement
     */
    public function cancelValidation(Procurement $procurement): Procurement
    {
        return DB::transaction(function () use ($procurement) {
            if (! $procurement->isValidated()) {
                throw new \InvalidArgumentException('Only validated procurements can have their validation cancelled.');
            }

            $procurement->load('articleLines.article', 'packagingLines.packaging');

            // Delete stock movements associated only with this procurement
            StockMovement::where('source_type', 'Procurement')
                ->where('source_id', $procurement->id)
                ->delete();

            // Restore article stock quantities
            foreach ($procurement->articleLines as $line) {
                $line->article->decrement('stock_qty', $line->quantity_received);
            }

            // Restore packaging stock quantities
            foreach ($procurement->packagingLines as $line) {
                $line->packaging->decrement('stock_qty', (int) $line->quantity);
            }

            // Refresh snapshots for this date to reflect reverted stock
            $this->refreshStockSnapshots(
                teamId: $procurement->team_id,
                snapshotDate: is_string($procurement->document_date)
                    ? $procurement->document_date
                    : $procurement->document_date->format('Y-m-d'),
            );

            // Revert procurement status
            $procurement->update([
                'status' => TransactionStatus::Draft,
                'validated_at' => null,
                'validated_by' => null,
            ]);

            return $procurement->refresh();
        });
    }

    /**
     * Create or refresh end-of-day stock snapshots for all team articles.
     *
     * Uses updateOrCreate so multiple procurements on the same day overwrite
     * the snapshot with the latest stock position.
     *
     * @param  int  $teamId  The team ID
     * @param  \DateTime|Carbon|string  $snapshotDate  The snapshot date
     */
    private function refreshStockSnapshots(int $teamId, $snapshotDate): void
    {
        $articles = Article::where('team_id', $teamId)->get();

        foreach ($articles as $article) {
            StockSnapshot::updateOrCreate(
                [
                    'team_id' => $teamId,
                    'article_id' => $article->id,
                    'snapshot_date' => $snapshotDate,
                ],
                [
                    'stock_qty' => $article->stock_qty,
                    'cost_price' => $article->cost_price,
                ],
            );
        }
    }
}
