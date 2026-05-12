<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Journal\Models\BusinessJournalEntry;
use App\Enums\JournalEntryType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessJournalController extends Controller
{
    public function entries(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $query = BusinessJournalEntry::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->with(['creator', 'sourceable'])
            ->orderByDesc('occurred_at');

        if ($request->filled('type')) {
            $query->where('entry_type', $request->input('type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->input('date_to'));
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ],
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $teamId = $request->user()->current_team_id;

        $rows = BusinessJournalEntry::withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->selectRaw('entry_type, SUM(amount) as total')
            ->groupBy('entry_type')
            ->get()
            ->keyBy('entry_type');

        $revenueTypes = [
            JournalEntryType::Sale->value,
            JournalEntryType::PaymentIn->value,
            JournalEntryType::ConsignmentIn->value,
        ];

        $expenseTypes = [
            JournalEntryType::Expense->value,
            JournalEntryType::PaymentOut->value,
            JournalEntryType::Purchase->value,
        ];

        $revenue = $rows->filter(fn ($r) => in_array($r->entry_type, $revenueTypes))->sum('total');
        $expenses = $rows->filter(fn ($r) => in_array($r->entry_type, $expenseTypes))->sum('total');

        return response()->json([
            'by_type' => $rows->map(fn ($r) => [
                'type' => $r->entry_type,
                'total' => (float) $r->total,
            ])->values(),
            'totals' => [
                'revenue' => (float) $revenue,
                'expenses' => (float) $expenses,
                'net' => (float) ($revenue - $expenses),
            ],
        ]);
    }
}
