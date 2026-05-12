<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\SalePackagingLine;
use Illuminate\Support\Facades\DB;

class DeconsignmentService
{
    public function __construct(
        private readonly PackagingStockService $packagingStockService,
    ) {}

    /**
     * Process a packaging deconsignment (return of consigned packaging).
     *
     * Records a ConsignmentReturn packaging movement and updates the
     * quantity_returned on the sale packaging line.
     *
     * @param  SalePackagingLine  $salePackagingLine  The packaging line being returned
     * @param  int  $quantityReturned  The number of units being returned
     * @param  int  $processedBy  The user ID processing the return
     */
    public function process(SalePackagingLine $salePackagingLine, int $quantityReturned, int $processedBy): void
    {
        DB::transaction(function () use ($salePackagingLine, $quantityReturned, $processedBy) {
            // Eager-load the packaging to avoid N+1
            $salePackagingLine->load('packaging');

            // Record packaging movement: bottles physically come back to the depot
            $this->packagingStockService->record(
                packaging: $salePackagingLine->packaging,
                kind: StockMovementKind::ConsignmentReturn,
                quantity: $quantityReturned,
                sourceType: 'SalePackagingLine',
                sourceId: $salePackagingLine->id,
                documentDate: now()->toDateString(),
                createdBy: $processedBy,
            );

            // Track how many units have been returned on the line
            $salePackagingLine->update([
                'quantity_returned' => $salePackagingLine->quantity_returned + $quantityReturned,
            ]);
        });
    }
}
