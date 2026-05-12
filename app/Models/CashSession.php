<?php

namespace App\Models;

use App\Concerns\BelongsToTeam;
use Database\Factories\CashSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'cashier_id', 'opened_at', 'closed_at',
    'opening_balance', 'closing_balance', 'total_sales',
    'total_cash', 'total_mobile', 'discrepancy', 'notes',
])]
class CashSession extends Model
{
    /** @use HasFactory<CashSessionFactory> */
    use BelongsToTeam, HasFactory;

    /**
     * Get the cashier for this session.
     *
     * @return BelongsTo<User, $this>
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Determine if this cash session is currently open.
     */
    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_balance' => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'total_sales' => 'decimal:2',
            'total_cash' => 'decimal:2',
            'total_mobile' => 'decimal:2',
            'discrepancy' => 'decimal:2',
        ];
    }
}
