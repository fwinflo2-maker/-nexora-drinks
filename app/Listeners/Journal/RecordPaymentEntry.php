<?php

namespace App\Listeners\Journal;

use App\Domain\Journal\Services\JournalService;
use App\Enums\JournalEntryType;
use App\Events\PaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecordPaymentEntry implements ShouldQueue
{
    public string $queue = 'default';

    private const MOBILE_METHODS = ['orange_money', 'mtn_momo', 'wave'];

    private const BANK_METHODS = ['transfer', 'cheque'];

    public function __construct(private readonly JournalService $journalService) {}

    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $amount = (float) $payment->amount;

        $treasuryAccount = match (true) {
            in_array($payment->method, self::MOBILE_METHODS) => '573',
            in_array($payment->method, self::BANK_METHODS) => '572',
            default => '571',
        };

        $this->journalService->record([
            'team_id' => $payment->team_id,
            'entry_type' => JournalEntryType::PaymentIn,
            'amount' => $payment->amount,
            'occurred_at' => $payment->received_at ?? $payment->created_at,
            'reference_number' => $payment->reference,
            'description' => "Encaissement paiement #{$payment->id}",
            'source' => $payment,
            'metadata' => [
                'client_id' => $payment->client_id,
                'invoice_id' => $payment->invoice_id,
                'method' => $payment->method,
                'treasury_account' => $treasuryAccount,
            ],
            'lines' => [
                ['account_code' => $treasuryAccount, 'debit' => $amount, 'credit' => 0.0],
                ['account_code' => '411', 'debit' => 0.0, 'credit' => $amount],
            ],
        ]);
    }
}
