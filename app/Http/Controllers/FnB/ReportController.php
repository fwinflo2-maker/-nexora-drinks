<?php

declare(strict_types=1);

namespace App\Http\Controllers\FnB;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\FnB\PdfService;
use App\Services\FnB\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
        private readonly PdfService $pdfService,
    ) {}

    public function orders(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->ordersReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('fnb/reports/orders', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function ordersPdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->ordersReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('fnb.pdf.orders', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }

    public function revenue(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->revenueReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('fnb/reports/revenue', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function revenuePdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->revenueReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('fnb.pdf.revenue', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }

    public function menu(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->menuReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('fnb/reports/menu', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function menuPdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->menuReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('fnb.pdf.menu', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }
}
