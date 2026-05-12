<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'name', 'plate', 'capacity_cases', 'is_active', 'driver_id',
])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the default driver for this vehicle.
     *
     * @return BelongsTo<User, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the routes using this vehicle.
     *
     * @return HasMany<DeliveryRoute, $this>
     */
    public function routes(): HasMany
    {
        return $this->hasMany(DeliveryRoute::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity_cases' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
