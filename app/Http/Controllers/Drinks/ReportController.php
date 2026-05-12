<?php

declare(strict_types=1);

namespace App\Http\Controllers\Drinks;

use App\Http\Controllers\Controller;
use App\Models\Drinks\Expense;
use App\Models\Drinks\Sale;
use App\Models\Drinks\StockMovement;
use App\Models\Team;
use App\Services\Drinks\PdfService;
use App\Services\Drinks\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly PdfService $pdfService,
    ) {}

    // ── Brouillard (journal caisse) ──────────────────────────────────────────

    public function brouillard(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Expense::class); // Comptable

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $data = $this->reportService->brouillard($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('drinks/reports/brouillard', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'data' => $data,
        ]);
    }

    public function brouillardPdf(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', Expense::class);

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $data = $this->reportService->brouillard($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('drinks.pdf.brouillard', [
            'team' => $current_team,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'data' => $data,
        ]);
    }

    // ── Ventes par article ────────────────────────────────────────────────────

    public function salesReport(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Sale::class);

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->salesByArticle($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('drinks/reports/sales-report', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rows' => $rows,
        ]);
    }

    public function salesReportPdf(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', Sale::class);

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->salesByArticle($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('drinks.pdf.sales-report', [
            'team' => $current_team,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rows' => $rows,
        ]);
    }

    // ── État des stocks ────────────────────────────────────────────────────────

    public function stockReport(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', StockMovement::class);

        $date = $request->get('date');
        $rows = $this->reportService->stockState($current_team->id, $date);

        return Inertia::render('drinks/reports/stock-report', [
            'date' => $date,
            'rows' => $rows,
        ]);
    }

    public function stockReportPdf(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', StockMovement::class);

        $date = $request->get('date');
        $rows = $this->reportService->stockState($current_team->id, $date);

        return $this->pdfService->render('drinks.pdf.stock-report', [
            'team' => $current_team,
            'date' => $date,
            'rows' => $rows,
        ]);
    }

    // ── CA Client ──────────────────────────────────────────────────────────────

    public function clientReport(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Sale::class);

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->clientTurnover($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('drinks/reports/client-report', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rows' => $rows,
        ]);
    }

    public function clientReportPdf(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', Sale::class);

        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->clientTurnover($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('drinks.pdf.client-report', [
            'team' => $current_team,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rows' => $rows,
        ]);
    }

    // ── Feuille de route ────────────────────────────────────────────────────────

    public function roadmap(Request $request, Team $current_team): InertiaResponse
    {
        Gate::authorize('viewAny', Sale::class);

        $date = $request->get('date', today()->toDateString());
        $sales = $this->reportService->roadmap($current_team->id, $date);

        return Inertia::render('drinks/reports/roadmap', [
            'date' => $date,
            'sales' => $sales,
        ]);
    }

    public function roadmapPdf(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', Sale::class);

        $date = $request->get('date', today()->toDateString());
        $sales = $this->reportService->roadmap($current_team->id, $date);

        return $this->pdfService->render('drinks.pdf.roadmap', [
            'team' => $current_team,
            'date' => $date,
            'sales' => $sales,
        ]);
    }
}
