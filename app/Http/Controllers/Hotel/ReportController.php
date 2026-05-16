<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\Hotel\PdfService;
use App\Services\Hotel\ReportService;
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

    public function reservations(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->reservationsReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('hotel/reports/reservations', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function reservationsPdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->reservationsReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('hotel.pdf.reservations', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }

    public function revenue(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->revenueReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('hotel/reports/revenue', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function revenuePdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->revenueReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('hotel.pdf.revenue', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }

    public function occupancy(Request $request, Team $current_team): InertiaResponse
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->occupancyReport($current_team->id, $dateFrom, $dateTo);

        return Inertia::render('hotel/reports/occupancy', compact('dateFrom', 'dateTo', 'rows'));
    }

    public function occupancyPdf(Request $request, Team $current_team): Response
    {
        $dateFrom = $request->get('date_from', today()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', today()->toDateString());

        $rows = $this->reportService->occupancyReport($current_team->id, $dateFrom, $dateTo);

        return $this->pdfService->render('hotel.pdf.occupancy', compact('dateFrom', 'dateTo', 'rows') + ['team' => $current_team]);
    }
}
