<?php

declare(strict_types=1);

namespace App\Models\FnB;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'category_id', 'name', 'description', 'price', 'cost_price', 'sku', 'is_available', 'image_path'])]
class MenuItem extends Model
{
    use BelongsToTeam;

    protected $table = 'fnb_menu_items';

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** @param Builder<MenuItem> $q */
    public function scopeAvailable(Builder $q): Builder
    {
        return $q->where('is_available', true);
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }
}
