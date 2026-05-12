<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\ClientPackagingBalanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'client_id', 'packaging_type_id', 'quantity_owed', 'last_updated_at',
])]
class ClientPackagingBalance extends Model
{
    /** @use HasFactory<ClientPackagingBalanceFactory> */
    use BelongsToTeam, HasFactory;

    public $timestamps = false;

    /**
     * Get the client for this balance.
     *
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the packaging type for this balance.
     *
     * @return BelongsTo<PackagingType, $this>
     */
    public function packagingType(): BelongsTo
    {
        return $this->belongsTo(PackagingType::class);
    }

    /**
     * Get the total monetary value owed.
     */
    public function totalValueXaf(): float
    {
        return $this->quantity_owed * (float) $this->packagingType->unit_value_xaf;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_owed' => 'integer',
            'last_updated_at' => 'datetime',
        ];
    }
}
