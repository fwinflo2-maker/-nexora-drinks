<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\DeliveryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'route_id', 'order_id', 'client_id', 'status',
    'sequence_number', 'delivered_at', 'signature_path', 'notes',
])]
class Delivery extends Model
{
    /** @use HasFactory<DeliveryFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the route for this delivery.
     *
     * @return BelongsTo<DeliveryRoute, $this>
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(DeliveryRoute::class, 'route_id');
    }

    /**
     * Get the order for this delivery.
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the client for this delivery.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the items for this delivery.
     *
     * @return HasMany<DeliveryItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    /**
     * Get the packaging movements for this delivery.
     *
     * @return HasMany<PackagingMovement, $this>
     */
    public function packagingMovements(): HasMany
    {
        return $this->hasMany(PackagingMovement::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sequence_number' => 'integer',
            'delivered_at' => 'datetime',
        ];
    }
}
