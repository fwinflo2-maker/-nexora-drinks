<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Inventory;
use App\Models\Drinks\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    /**
     * Validate an inventory.
     *
     * Compares counted_qty for each line against the article's current stock_qty
     * and creates a StockMovement of kind InventoryAdjust for any difference.
     * The stock is adjusted to match the counted quantity.
     *
     * @param  Inventory  $inventory  The inventory to validate
     * @param  int  $validatedBy  The user ID validating the inventory
     * @return Inventory The updated inventory
     */
    public function validate(Inventory $inventory, int $validatedBy): Inventory
    {
        return DB::transaction(function () use ($inventory, $validatedBy) {
            // Eager-load lines with articles to avoid N+1
            $inventory->load('lines.article');

            foreach ($inventory->lines as $line) {
                $article = $line->article;
                $delta = $line->counted_qty - $article->stock_qty;

                if ($delta === 0) {
                    continue;
                }

                // Create a movement recording the absolute difference
                StockMovement::create([
                    'team_id' => $article->team_id,
                    'article_id' => $article->id,
                    'kind' => StockMovementKind::InventoryAdjust,
                    'quantity' => abs($delta),
                    'source_type' => 'Inventory',
                    'source_id' => $inventory->id,
                    'document_date' => $inventory->document_date,
                    'created_by' => $validatedBy,
                ]);

                // Adjust stock directly to the counted quantity
                if ($delta > 0) {
                    $article->increment('stock_qty', $delta);
                } else {
                    $article->decrement('stock_qty', abs($delta));
                }
            }

            $inventory->update([
                'status' => TransactionStatus::Validated,
                'validated_at' => now(),
                'validated_by' => $validatedBy,
            ]);

            return $inventory->refresh();
        });
    }

    /**
     * Cancel the validation of an inventory.
     *
     * Reverses all InventoryAdjust stock movements and restores
     * article stock quantities to their pre-inventory state.
     *
     * @param  Inventory  $inventory  The inventory to cancel
     * @return Inventory The updated inventory
     */
    public function cancelValidation(Inventory $inventory): Inventory
    {
        return DB::transaction(function () use ($inventory) {
            if ($inventory->status !== TransactionStatus::Validated) {
                throw new \InvalidArgumentException('Only validated inventories can have their validation cancelled.');
            }

            $inventory->load('lines.article');

            // Retrieve the recorded adjustments to reverse them precisely.
            // Each movement's `quantity` is abs(delta). We determine direction
            // from counted_qty vs the pre-inventory stock (original = counted - delta).
            // After validation stock_qty == counted_qty, so we use movements to revert.
            $movements = StockMovement::where('source_type', 'Inventory')
                ->where('source_id', $inventory->id)
                ->get()
                ->keyBy('article_id');

            foreach ($inventory->lines as $line) {
                $article = $line->article;
                $movement = $movements->get($article->id);

                if (! $movement) {
                    continue; // delta was 0 — nothing to revert
                }

                // Original stock before validation was: counted_qty - delta
                // counted_qty > original → we incremented → revert: decrement
                // counted_qty < original → we decremented → revert: increment
                $originalStock = $line->counted_qty - $movement->quantity;
                if ($line->counted_qty > $originalStock) {
                    $article->decrement('stock_qty', $movement->quantity);
                } else {
                    $article->increment('stock_qty', $movement->quantity);
                }
            }

            // Delete all InventoryAdjust movements for this inventory
            StockMovement::where('source_type', 'Inventory')
                ->where('source_id', $inventory->id)
                ->delete();

            $inventory->update([
                'status' => TransactionStatus::Draft,
                'validated_at' => null,
                'validated_by' => null,
            ]);

            return $inventory->refresh();
        });
    }
}
