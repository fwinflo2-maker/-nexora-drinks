<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use App\Enums\BaseUnit;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'team_id', 'category_id', 'name', 'sku', 'barcode',
    'description', 'image_path', 'base_unit',
    'units_per_pack', 'units_per_case', 'units_per_pallet',
    'purchase_price', 'sale_price', 'min_sale_price', 'vat_rate',
    'currency', 'is_consignable', 'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use BelongsToTeam, HasFactory, SoftDeletes;

    /**
     * Get the category that owns the product.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the stock levels for this product.
     *
     * @return HasMany<StockLevel, $this>
     */
    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    /**
     * Get the stock movements for this product.
     *
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get total available stock across all warehouses.
     */
    public function totalStock(): int
    {
        return (int) $this->stockLevels()->sum('quantity');
    }

    /**
     * Get total reserved stock across all warehouses.
     */
    public function totalReserved(): int
    {
        return (int) $this->stockLevels()->sum('reserved_quantity');
    }

    /**
     * Calculate the margin percentage.
     */
    public function marginPercent(): float
    {
        if ($this->purchase_price <= 0) {
            return 0;
        }

        return round((($this->sale_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_unit' => BaseUnit::class,
            'units_per_pack' => 'integer',
            'units_per_case' => 'integer',
            'units_per_pallet' => 'integer',
            'purchase_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'min_sale_price' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'is_consignable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
