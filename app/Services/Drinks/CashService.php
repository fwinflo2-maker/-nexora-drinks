<?php

namespace App\Services\Drinks;

use App\Models\Drinks\CashDeposit;
use App\Models\Drinks\CashInput;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Payment;

class CashService
{
    /**
     * Aggregate all validated cash inputs for a team within a date range.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array{amount: float, count: int}
     */
    public function totalCashInputs(int $teamId, string $dateFrom, string $dateTo): array
    {
        $result = CashInput::where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        return [
            'amount' => (float) ($result->total ?? 0),
            'count' => (int) ($result->count ?? 0),
        ];
    }

    /**
     * Aggregate all validated cash deposits for a team within a date range.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array{amount: float, count: int}
     */
    public function totalCashDeposits(int $teamId, string $dateFrom, string $dateTo): array
    {
        $result = CashDeposit::where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->selectRaw('SUM(total_amount) as total, COUNT(*) as count')
            ->first();

        return [
            'amount' => (float) ($result->total ?? 0),
            'count' => (int) ($result->count ?? 0),
        ];
    }

    /**
     * Aggregate all validated expenses for a team within a date range.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array{amount: float, count: int}
     */
    public function totalExpenses(int $teamId, string $dateFrom, string $dateTo): array
    {
        $result = Expense::where('team_id', $teamId)
            ->validated()
            ->between($dateFrom, $dateTo)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        return [
            'amount' => (float) ($result->total ?? 0),
            'count' => (int) ($result->count ?? 0),
        ];
    }

    /**
     * Aggregate all validated payments received for a team within a date range.
     *
     * @param  int  $teamId  The team ID
     * @param  string  $dateFrom  Start date (Y-m-d)
     * @param  string  $dateTo  End date (Y-m-d)
     * @return array{amount: float, count: int}
     */
    public function totalPayments(int $teamId, string $dateFrom, string $dateTo): array
    {
        $result = Payment::where('team_id', $teamId)
            ->between($dateFrom, $dateTo)
            ->selectRaw('SUM(amount) as total, COUNT(*) as count')
            ->first();

        return [
            'amount' => (float) ($result->total ?? 0),
            'count' => (int) ($result->count ?? 0),
        ];
    }
}
