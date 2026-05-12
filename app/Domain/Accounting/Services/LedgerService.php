<?php

namespace App\Domain\Accounting\Services;

use App\Domain\Accounting\Models\LedgerAccount;
use App\Domain\Accounting\Models\LedgerLine;
use App\Domain\Journal\Models\BusinessJournalEntry;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LedgerService
{
    /**
     * Post ledger lines for a journal entry.
     * Uses the provided lines or builds default double-entry lines from the entry type.
     *
     * @param  array<int, array{account_code: string, debit: float, credit: float}>|null  $lines
     */
    public function postLines(BusinessJournalEntry $entry, ?array $lines = null): void
    {
        $lines ??= $this->buildDefaultLines($entry);

        $this->validateDoubleEntry($lines);

        DB::transaction(function () use ($entry, $lines) {
            foreach ($lines as $line) {
                $account = LedgerAccount::withoutGlobalScopes()
                    ->where('team_id', $entry->team_id)
                    ->where('code', $line['account_code'])
                    ->firstOrFail();

                LedgerLine::create([
                    'team_id' => $entry->team_id,
                    'journal_entry_id' => $entry->id,
                    'ledger_account_id' => $account->id,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                    'posted_at' => $entry->occurred_at,
                ]);
            }
        });
    }

    /**
     * Build the default double-entry lines based on the journal entry type.
     *
     * @return array<int, array{account_code: string, debit: float, credit: float}>
     */
    private function buildDefaultLines(BusinessJournalEntry $entry): array
    {
        $debitCode = $entry->entry_type->defaultDebitAccount();
        $creditCode = $entry->entry_type->defaultCreditAccount();
        $amount = (float) $entry->amount;

        return [
            ['account_code' => $debitCode, 'debit' => $amount, 'credit' => 0.0],
            ['account_code' => $creditCode, 'debit' => 0.0, 'credit' => $amount],
        ];
    }

    /**
     * Validate that the provided lines satisfy the double-entry constraint (debits = credits).
     *
     * @param  array<int, array{account_code: string, debit: float, credit: float}>  $lines
     *
     * @throws RuntimeException
     */
    private function validateDoubleEntry(array $lines): void
    {
        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) > 0.001) {
            throw new RuntimeException(
                sprintf(
                    'Déséquilibre comptable : débit total %.2f ≠ crédit total %.2f',
                    $totalDebit,
                    $totalCredit,
                )
            );
        }
    }

    /**
     * Generate a trial balance for a team.
     *
     * @return array<int, array{code: string, name: string, type: string, debit: float, credit: float, balance: float}>
     */
    public function trialBalance(int $teamId): array
    {
        return LedgerAccount::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->with(['lines' => fn ($q) => $q->withoutGlobalScopes()->where('team_id', $teamId)])
            ->orderBy('code')
            ->get()
            ->map(fn (LedgerAccount $account) => [
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->account_type->value,
                'debit' => (float) $account->lines->sum('debit'),
                'credit' => (float) $account->lines->sum('credit'),
                'balance' => $account->currentBalance(),
            ])
            ->filter(fn (array $row) => $row['debit'] > 0 || $row['credit'] > 0)
            ->values()
            ->toArray();
    }
}
