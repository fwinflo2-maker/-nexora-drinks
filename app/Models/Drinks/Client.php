<?php

namespace App\Models\Drinks;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCodeGeneration;
use App\Concerns\LogsActivity;
use Database\Factories\Drinks\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'code', 'name', 'address', 'phone', 'contact',
    'pickup_fee', 'pickup_fee_pet', 'credit_limit', 'is_active',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use BelongsToTeam, HasCodeGeneration, HasFactory, LogsActivity;

    protected $table = 'drinks_clients';

    protected function getCodePrefix(): string
    {
        return 'CLI';
    }

    /** @return HasMany<Sale, $this> */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
