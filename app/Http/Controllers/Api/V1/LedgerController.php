<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Accounting\Models\LedgerAccount;
use App\Domain\Accounting\Services\LedgerService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function __construct(private readonly LedgerService $ledgerService) {}

    public function accounts(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $accounts = LedgerAccount::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        return response()->json(['data' => $accounts]);
    }

    public function trialBalance(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $rows = $this->ledgerService->trialBalance($teamId);

        $totalDebit = array_sum(array_column($rows, 'debit'));
        $totalCredit = array_sum(array_column($rows, 'credit'));

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'is_balanced' => abs($totalDebit - $totalCredit) <= 0.001,
            ],
        ]);
    }
}
