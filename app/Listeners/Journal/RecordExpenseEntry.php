<?php

namespace App\Listeners\Journal;

use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Events\ExpenseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordExpenseEntry implements ShouldQueue
{
    public string $queue = 'default';

    public function __construct(private readonly JournalService $journalService) {}

    public function handle(ExpenseCreated $event): void
    {
        $expense = $event->expense;

        $this->journalService->record([
            'team_id' => $expense->team_id,
            'entry_type' => JournalEntryType::Expense,
            'amount' => $expense->amount,
            'occurred_at' => $expense->date ?? $expense->created_at,
            'description' => $expense->description ?? "Dépense #{$expense->id}",
            'source' => $expense,
            'metadata' => [
                'category' => $expense->category,
                'payment_method' => $expense->payment_method,
            ],
        ]);
    }
}
