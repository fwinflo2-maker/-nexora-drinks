<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\PackagingMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'team_id', 'client_id', 'packaging_type_id',
    'movement_type', 'quantity', 'delivery_id', 'notes', 'created_by',
])]
class PackagingMovement extends Model
{
    /** @use HasFactory<PackagingMovementFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the client for this movement.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the packaging type for this movement.
     *
     * @return BelongsTo<PackagingType, $this>
     */
    public function packagingType(): BelongsTo
    {
        return $this->belongsTo(PackagingType::class);
    }

    /**
     * Get the delivery associated with this movement.
     *
     * @return BelongsTo<Delivery, $this>
     */
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the user who created this movement.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the packaging damage report for this movement.
     *
     * @return HasOne<PackagingDamage, $this>
     */
    public function damage(): HasOne
    {
        return $this->hasOne(PackagingDamage::class, 'packaging_movement_id');
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
        ];
    }
}
