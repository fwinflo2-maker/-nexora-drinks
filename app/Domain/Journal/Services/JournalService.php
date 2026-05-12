<?php

namespace App\Domain\Journal\Services;

use App\Domain\Accounting\Services\LedgerService;
use App\Domain\Journal\Models\BusinessJournalEntry;
use App\Enums\JournalEntryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalService
{
    public function __construct(private readonly LedgerService $ledgerService) {}

    /**
     * Record a new journal entry and post its ledger lines atomically.
     *
     * @param array{
     *     team_id: int,
     *     entry_type: JournalEntryType,
     *     amount: float|string,
     *     occurred_at: \DateTimeInterface|string,
     *     reference_number?: string|null,
     *     description?: string|null,
     *     created_by?: int|null,
     *     metadata?: array<string, mixed>|null,
     *     source?: Model,
     *     lines?: array<int, array{account_code: string, debit: float, credit: float}>
     * } $data
     */
    public function record(array $data): BusinessJournalEntry
    {
        return DB::transaction(function () use ($data) {
            $entry = BusinessJournalEntry::create([
                'team_id' => $data['team_id'],
                'entry_type' => $data['entry_type'],
                'amount' => $data['amount'],
                'occurred_at' => $data['occurred_at'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            if (isset($data['source'])) {
                $entry->sourceable_type = get_class($data['source']);
                $entry->sourceable_id = $data['source']->id;
                $entry->save();
            }

            $this->ledgerService->postLines($entry, $data['lines'] ?? null);

            return $entry;
        });
    }
}
