<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\DeliveryRouteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'name', 'date', 'driver_id', 'vehicle_id',
    'status', 'total_distance_km', 'departure_time', 'arrival_time', 'created_by',
])]
class DeliveryRoute extends Model
{
    /** @use HasFactory<DeliveryRouteFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * The actual table name — avoids conflict with Laravel Route facade.
     */
    protected $table = 'routes';

    /**
     * Get the driver for this route.
     *
     * @return BelongsTo<User, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the vehicle for this route.
     *
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who created this route.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the deliveries for this route.
     *
     * @return HasMany<Delivery, $this>
     */
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'route_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_distance_km' => 'decimal:2',
        ];
    }
}
