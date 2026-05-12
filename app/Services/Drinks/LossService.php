<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Loss;
use App\Models\Drinks\StockMovement;
use Illuminate\Support\Facades\DB;

class LossService
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    /**
     * Validate a loss transaction.
     *
     * Records a StockMovement of kind Loss for each loss line and
     * decrements the article stock accordingly.
     *
     * @param  Loss  $loss  The loss to validate
     * @param  int  $validatedBy  The user ID validating the loss
     * @return Loss The updated loss
     */
    public function validate(Loss $loss, int $validatedBy): Loss
    {
        return DB::transaction(function () use ($loss, $validatedBy) {
            // Eager-load lines with articles to avoid N+1
            $loss->load('lines.article');

            foreach ($loss->lines as $line) {
                $this->stockService->record(
                    article: $line->article,
                    kind: StockMovementKind::Loss,
                    quantity: $line->quantity,
                    sourceType: 'Loss',
                    sourceId: $loss->id,
                    documentDate: $loss->document_date,
                    createdBy: $validatedBy,
                );
            }

            $loss->update([
                'status' => TransactionStatus::Validated,
                'validated_at' => now(),
                'validated_by' => $validatedBy,
            ]);

            return $loss->refresh();
        });
    }

    /**
     * Cancel the validation of a loss.
     *
     * Reverses all Loss stock movements and restores article stock quantities.
     *
     * @param  Loss  $loss  The loss to cancel
     * @return Loss The updated loss
     */
    public function cancelValidation(Loss $loss): Loss
    {
        return DB::transaction(function () use ($loss) {
            if ($loss->status !== TransactionStatus::Validated) {
                throw new \InvalidArgumentException('Only validated losses can have their validation cancelled.');
            }

            $loss->load('lines.article');

            // Restore each article's stock quantity
            foreach ($loss->lines as $line) {
                $line->article->increment('stock_qty', $line->quantity);
            }

            // Delete all Loss movements for this loss
            StockMovement::where('source_type', 'Loss')
                ->where('source_id', $loss->id)
                ->delete();

            $loss->update([
                'status' => TransactionStatus::Draft,
                'validated_at' => null,
                'validated_by' => null,
            ]);

            return $loss->refresh();
        });
    }
}
