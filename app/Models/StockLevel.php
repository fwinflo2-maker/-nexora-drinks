<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\StockLevelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'product_id', 'warehouse_id',
    'quantity', 'reserved_quantity', 'min_threshold', 'max_threshold',
])]
class StockLevel extends Model
{
    /** @use HasFactory<StockLevelFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the product for this stock level.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse for this stock level.
     *
     * @return BelongsTo<Warehouse, $this>
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get available quantity (total minus reserved).
     */
    public function availableQuantity(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    /**
     * Determine if stock is below the minimum threshold.
     */
    public function isLow(): bool
    {
        return $this->min_threshold !== null && $this->quantity <= $this->min_threshold;
    }

    /**
     * Determine if stock exceeds the maximum threshold.
     */
    public function isOverstock(): bool
    {
        return $this->max_threshold !== null && $this->quantity >= $this->max_threshold;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved_quantity' => 'integer',
            'min_threshold' => 'integer',
            'max_threshold' => 'integer',
        ];
    }
}
