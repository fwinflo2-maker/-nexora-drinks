<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'packaging_movement_id', 'quantity', 'reason', 'cost_xaf', 'created_by',
])]
class PackagingDamage extends Model
{
    use BelongsToTeam;

    /**
     * Get the packaging movement for this damage record.
     *
     * @return BelongsTo<PackagingMovement, $this>
     */
    public function packagingMovement(): BelongsTo
    {
        return $this->belongsTo(PackagingMovement::class, 'packaging_movement_id');
    }

    /**
     * Get the user who created this damage record.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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
            'cost_xaf' => 'decimal:2',
        ];
    }
}
