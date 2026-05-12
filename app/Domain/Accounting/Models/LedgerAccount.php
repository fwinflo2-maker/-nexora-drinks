<?php

namespace App\Domain\Accounting\Models;

use App\Concerns\BelongsToTeam;
use App\Enums\LedgerAccountType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'team_id', 'code', 'name', 'account_type', 'parent_id',
    'is_active', 'is_system', 'sort_order',
])]
class LedgerAccount extends Model
{
    use BelongsToTeam;

    /**
     * Get all ledger lines for this account.
     *
     * @return HasMany<LedgerLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(LedgerLine::class);
    }

    /**
     * Get the parent account.
     *
     * @return BelongsTo<LedgerAccount, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     *
     * @return HasMany<LedgerAccount, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(LedgerAccount::class, 'parent_id');
    }

    /**
     * Calculate the current balance of this account.
     * Debit-normal accounts: balance = sum(debit) - sum(credit)
     * Credit-normal accounts: balance = sum(credit) - sum(debit)
     */
    public function currentBalance(): float
    {
        $totalDebit = (float) $this->lines->sum('debit');
        $totalCredit = (float) $this->lines->sum('credit');

        if ($this->account_type->normalBalance() === 'debit') {
            return $totalDebit - $totalCredit;
        }

        return $totalCredit - $totalDebit;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'account_type' => LedgerAccountType::class,
            'is_active' => 'boolean',
            'is_system' => 'boolean',
        ];
    }
}
