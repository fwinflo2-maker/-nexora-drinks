<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use App\Enums\ClientType;
use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'team_id', 'name', 'phone', 'phone2', 'email', 'address',
    'gps_lat', 'gps_lng', 'zone', 'credit_limit', 'payment_terms_days',
    'commercial_id', 'client_type', 'is_active', 'notes',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use BelongsToTeam, HasFactory, SoftDeletes;

    /**
     * Get the commercial (sales rep) assigned to this client.
     *
     * @return BelongsTo<User, $this>
     */
    public function commercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    /**
     * Get the packaging balances for this client.
     *
     * @return HasMany<ClientPackagingBalance, $this>
     */
    public function packagingBalances(): HasMany
    {
        return $this->hasMany(ClientPackagingBalance::class);
    }

    /**
     * Determine if the client has exceeded their credit limit.
     */
    public function isOverCreditLimit(float $currentDebt): bool
    {
        return $this->credit_limit > 0 && $currentDebt > $this->credit_limit;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
            'credit_limit' => 'decimal:2',
            'payment_terms_days' => 'integer',
            'client_type' => ClientType::class,
            'is_active' => 'boolean',
        ];
    }
}
