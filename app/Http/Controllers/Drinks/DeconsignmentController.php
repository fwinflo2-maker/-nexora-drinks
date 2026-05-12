<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Models\Drinks\SalePackagingLine;
use App\Models\Team;
use App\Services\Drinks\DeconsignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DeconsignmentController extends Controller
{
    public function __construct(private readonly DeconsignmentService $service) {}

    public function process(Request $request, Team $current_team, SalePackagingLine $salePackagingLine): RedirectResponse
    {
        $sale = $salePackagingLine->sale;
        Gate::authorize('update', $sale);

        $validated = $request->validate([
            'quantity_returned' => ['required', 'integer', 'min:1'],
        ]);

        $this->service->process($salePackagingLine, $validated['quantity_returned'], auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => __("Retour d'emballage enregistré.")]);

        return back();
    }
}
