<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\PackagingTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'name', 'description', 'unit_value_xaf', 'is_active',
])]
class PackagingType extends Model
{
    /** @use HasFactory<PackagingTypeFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the packaging movements for this type.
     *
     * @return HasMany<PackagingMovement, $this>
     */
    public function movements(): HasMany
    {
        return $this->hasMany(PackagingMovement::class);
    }

    /**
     * Get the client balances for this packaging type.
     *
     * @return HasMany<ClientPackagingBalance, $this>
     */
    public function clientBalances(): HasMany
    {
        return $this->hasMany(ClientPackagingBalance::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'unit_value_xaf' => 'decimal:2',
        ];
    }
}
