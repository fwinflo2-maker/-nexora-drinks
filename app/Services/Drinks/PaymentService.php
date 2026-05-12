<?php

namespace App\Services\Drinks;

use App\Enums\Drinks\TransactionStatus;
use App\Models\Drinks\Payment;
use App\Models\Drinks\PaymentAdjustment;
use App\Models\Drinks\Sale;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Allocate a payment to a sale.
     *
     * Creates or updates a PaymentAdjustment linking the payment to the sale.
     * Updates the payment status to Validated when the sale is fully settled.
     *
     * @param  Payment  $payment  The payment to allocate
     * @param  Sale  $sale  The sale to allocate the payment to
     * @param  string|null  $observation  Optional note for the adjustment
     * @return PaymentAdjustment The created or updated adjustment
     */
    public function allocate(Payment $payment, Sale $sale, ?string $observation = null): PaymentAdjustment
    {
        return DB::transaction(function () use ($payment, $sale, $observation) {
            // Create or update the payment adjustment
            /** @var PaymentAdjustment $adjustment */
            $adjustment = PaymentAdjustment::updateOrCreate(
                [
                    'team_id' => $payment->team_id,
                    'sale_id' => $sale->id,
                ],
                [
                    'amount' => $payment->amount,
                    'observation' => $observation,
                    'status' => TransactionStatus::Validated,
                    'created_by' => $payment->created_by,
                ],
            );

            // Calculate total amount already allocated to this sale
            $totalAllocated = PaymentAdjustment::where('sale_id', $sale->id)
                ->where('status', TransactionStatus::Validated)
                ->sum('amount');

            // Mark payment as validated once the sale is fully or over-settled
            if ($totalAllocated >= $sale->total_ttc) {
                $payment->update(['status' => TransactionStatus::Validated]);
            }

            return $adjustment;
        });
    }
}
