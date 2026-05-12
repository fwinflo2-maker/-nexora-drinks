<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use Database\Factories\Drinks\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'code', 'name', 'brand',
    'category_id', 'packaging_id',
    'sale_price', 'retail_price', 'cost_price',
    'stock_qty', 'frigo_stock_qty', 'packs_per_unit',
    'discount_rate', 'rebate_rate',
    'is_active', 'is_consignable',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use \App\Concerns\LogsActivity, BelongsToTeam, HasCodeGeneration, HasFactory;

    protected $table = 'drinks_articles';

    protected function getCodePrefix(): string
    {
        return 'ART';
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return BelongsTo<Packaging, $this> */
    public function packaging(): BelongsTo
    {
        return $this->belongsTo(Packaging::class);
    }

    /** @return HasMany<PricingTier, $this> */
    public function pricingTiers(): HasMany
    {
        return $this->hasMany(PricingTier::class);
    }

    /** @return HasMany<StockMovement, $this> */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /** @return HasMany<StockSnapshot, $this> */
    public function snapshots(): HasMany
    {
        return $this->hasMany(StockSnapshot::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_consignable' => 'boolean',
        ];
    }
}
