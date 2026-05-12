<?php

namespace App\Domain\Accounting\Models;

use App\Concerns\BelongsToTeam;
use App\Domain\Journal\Models\BusinessJournalEntry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'team_id', 'journal_entry_id', 'ledger_account_id',
    'debit', 'credit', 'description', 'posted_at',
])]
class LedgerLine extends Model
{
    use BelongsToTeam;

    /**
     * Get the journal entry for this ledger line.
     *
     * @return BelongsTo<BusinessJournalEntry, $this>
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(BusinessJournalEntry::class, 'journal_entry_id');
    }

    /**
     * Get the ledger account for this line.
     *
     * @return BelongsTo<LedgerAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'credit' => 'decimal:2',
            'posted_at' => 'datetime',
        ];
    }
}
