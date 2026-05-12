<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\StockMovementKind;
use App\Models\Drinks\Packaging;
use App\Models\Drinks\PackagingMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PackagingStockService
{
    /**
     * Record a packaging stock movement (consignation/déconsignation).
     *
     * Creates a PackagingMovement record and updates the Packaging's stock_qty
     * atomically within a database transaction.
     *
     * @param  Packaging  $packaging  The packaging whose stock is being moved
     * @param  StockMovementKind  $kind  The type of movement
     * @param  int  $quantity  The quantity being moved
     * @param  string  $sourceType  The morphable source type (e.g., 'Sale', 'SalePackagingLine')
     * @param  int  $sourceId  The morphable source ID
     * @param  \DateTime|Carbon|string  $documentDate  The document date for the movement
     * @param  int  $createdBy  The user ID creating this movement
     * @return PackagingMovement The created movement record
     */
    public function record(
        Packaging $packaging,
        StockMovementKind $kind,
        int $quantity,
        string $sourceType,
        int $sourceId,
        $documentDate,
        int $createdBy,
    ): PackagingMovement {
        return DB::transaction(function () use ($packaging, $kind, $quantity, $sourceType, $sourceId, $documentDate, $createdBy) {
            // Create the packaging movement record
            $movement = PackagingMovement::create([
                'team_id' => $packaging->team_id,
                'packaging_id' => $packaging->id,
                'kind' => $kind,
                'quantity' => $quantity,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'document_date' => $documentDate,
                'created_by' => $createdBy,
            ]);

            // Update the packaging stock based on movement kind
            if ($kind->isPositive()) {
                $packaging->increment('stock_qty', $quantity);
            } else {
                $packaging->decrement('stock_qty', $quantity);
            }

            return $movement;
        });
    }
}
