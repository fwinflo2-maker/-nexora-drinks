<?php

namespace App\Models\Drinks;

use Database\Factories\Drinks\InventoryLineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['inventory_id', 'article_id', 'counted_qty', 'cost_price'])]
class InventoryLine extends Model
{
    /** @use HasFactory<InventoryLineFactory> */
    use HasFactory;

    protected $table = 'drinks_inventory_lines';

    /** @return BelongsTo<Inventory, $this> */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /** @return BelongsTo<Article, $this> */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
