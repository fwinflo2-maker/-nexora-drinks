<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\FieldOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'commercial_id', 'client_id', 'items_json',
    'gps_lat', 'gps_lng', 'offline_created_at', 'synced_at',
    'sync_status', 'converted_order_id', 'sync_error',
])]
class FieldOrder extends Model
{
    /** @use HasFactory<FieldOrderFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the commercial who created this field order.
     *
     * @return BelongsTo<User, $this>
     */
    public function commercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    /**
     * Get the client for this field order.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the converted server-side order.
     *
     * @return BelongsTo<Order, $this>
     */
    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items_json' => 'array',
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
            'offline_created_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }
}
