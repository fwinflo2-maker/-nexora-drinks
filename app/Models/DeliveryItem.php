<?php

namespace App\Models;

use Database\Factories\DeliveryItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'delivery_id', 'product_id', 'ordered_qty', 'delivered_qty', 'returned_qty', 'reason_partial',
])]
class DeliveryItem extends Model
{
    /** @use HasFactory<DeliveryItemFactory> */
    use HasFactory;

    /**
     * Get the delivery for this item.
     *
     * @return BelongsTo<Delivery, $this>
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the product for this item.
     *
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ordered_qty' => 'integer',
            'delivered_qty' => 'integer',
            'returned_qty' => 'integer',
        ];
    }
}
