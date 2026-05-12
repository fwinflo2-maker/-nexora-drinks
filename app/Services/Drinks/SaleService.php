<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\PackagingMovement;
use App\Models\Drinks\Sale;
use App\Models\Drinks\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly PackagingStockService $packagingStockService,
    ) {}

    /**
     * Validate a sale transaction.
     *
     * Calculates discounts (discount_total) and ristournes (rebate_credit) from
     * article-level rates, records stock movements for each article line, records
     * packaging consignment movements, and updates the sale status.
     *
     * @param  Sale  $sale  The sale to validate
     * @param  int  $validatedBy  The user ID validating the sale
     * @return Sale The updated sale
     */
    public function validate(Sale $sale, int $validatedBy): Sale
    {
        return DB::transaction(function () use ($sale, $validatedBy) {
            // Eager-load to avoid N+1
            $sale->load('articleLines.article', 'packagingLines.packaging');

            // 1. Pre-validation: Check if all articles have enough stock
            foreach ($sale->articleLines as $line) {
                $article = $line->article;
                if ($article->stock_qty < $line->quantity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'sale' => ["Stock insuffisant pour l'article '{$article->name}' ({$article->stock_qty} disponible, {$line->quantity} demandé)."]
                    ]);
                }
            }

            $discountTotal = 0.0;
            $rebateCredit = 0.0;

            foreach ($sale->articleLines as $line) {
                $article = $line->article;

                // Record stock movement for article
                $this->stockService->record(
                    article: $article,
                    kind: StockMovementKind::SaleOut,
                    quantity: $line->quantity,
                    sourceType: 'Sale',
                    sourceId: $sale->id,
                    documentDate: $sale->document_date,
                    createdBy: $validatedBy,
                );

                // Accumulate discount from article-level discount rate
                if ($article->discount_rate > 0) {
                    $discountTotal += $line->amount_ht * ($article->discount_rate / 100);
                }

                // Accumulate ristourne (rebate) from article-level rebate rate
                if ($article->rebate_rate > 0) {
                    $rebateCredit += $line->amount_ht * ($article->rebate_rate / 100);
                }
            }

            // Record packaging consignments (bottles going out to client)
            foreach ($sale->packagingLines as $line) {
                $this->packagingStockService->record(
                    packaging: $line->packaging,
                    kind: StockMovementKind::ConsignmentOut,
                    quantity: $line->quantity_out,
                    sourceType: 'SalePackagingLine',
                    sourceId: $line->id,
                    documentDate: $sale->document_date,
                    createdBy: $validatedBy,
                );
            }

            // Update sale with calculated totals and validated status
            $sale->update([
                'discount_total' => $discountTotal,
                'rebate_credit' => $rebateCredit,
                'status' => TransactionStatus::Validated,
                'validated_at' => now(),
                'validated_by' => $validatedBy,
            ]);

            return $sale->refresh();
        });
    }

    /**
     * Cancel validation of a sale.
     *
     * Reverses all stock and packaging movements and resets the sale to draft.
     *
     * @param  Sale  $sale  The sale to cancel
     * @return Sale The updated sale
     */
    public function cancelValidation(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            if (! $sale->isValidated()) {
                throw new \InvalidArgumentException('Only validated sales can have their validation cancelled.');
            }

            $sale->load('articleLines.article', 'packagingLines.packaging');

            // Delete stock movements associated with this sale
            StockMovement::where('source_type', 'Sale')
                ->where('source_id', $sale->id)
                ->delete();

            // Delete packaging movements associated with this sale's packaging lines
            $packagingLineIds = $sale->packagingLines->pluck('id');
            PackagingMovement::where('source_type', 'SalePackagingLine')
                ->whereIn('source_id', $packagingLineIds)
                ->delete();

            // Restore article stock quantities
            foreach ($sale->articleLines as $line) {
                $line->article->increment('stock_qty', $line->quantity);
            }

            // Restore packaging stock quantities
            foreach ($sale->packagingLines as $line) {
                $line->packaging->increment('stock_qty', $line->quantity_out);
            }

            // Revert sale status and reset calculated fields
            $sale->update([
                'discount_total' => 0,
                'rebate_credit' => 0,
                'status' => TransactionStatus::Draft,
                'validated_at' => null,
                'validated_by' => null,
            ]);

            return $sale->refresh();
        });
    }
}
