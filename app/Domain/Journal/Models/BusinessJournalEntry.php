<?php

namespace App\Domain\Journal\Models;

use App\Concerns\BelongsToTeam;
use App\Domain\Accounting\Models\LedgerLine;
use App\Enums\JournalEntryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'team_id', 'entry_type', 'amount', 'reference_number', 'description',
    'occurred_at', 'created_by', 'metadata', 'sourceable_type', 'sourceable_id',
])]
class BusinessJournalEntry extends Model
{
    use BelongsToTeam;

    protected $table = 'business_journal_entries';

    /**
     * Get the source model that triggered this journal entry.
     *
     * @return MorphTo<Model, $this>
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the ledger lines for this journal entry.
     *
     * @return HasMany<LedgerLine, $this>
     */
    public function ledgerLines(): HasMany
    {
        return $this->hasMany(LedgerLine::class, 'journal_entry_id');
    }

    /**
     * Get the user who created this journal entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function delete(): ?bool
    {
        throw new \RuntimeException('Les entrées journal sont immuables et ne peuvent pas être supprimées.');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_type' => JournalEntryType::class,
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
            'metadata' => 'array',
        ];
    }
}
