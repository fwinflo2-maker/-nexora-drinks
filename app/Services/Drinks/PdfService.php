<?php

namespace App\Services\Drinks;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Render a Blade view to PDF and return an HTTP response.
     *
     * Supported formats:
     *   - 'a4'         → standard A4 portrait (210mm × 297mm)
     *   - 'thermal80mm' → 80mm-wide thermal receipt roll (80mm × 297mm)
     *
     * @param  string  $view  The Blade view name (e.g., 'drinks.pdfs.sale-invoice')
     * @param  array<string, mixed>  $data  Data to pass to the Blade view
     * @param  string  $format  Paper format: 'a4' | 'thermal80mm'
     * @return Response The PDF binary response with appropriate headers
     */
    public function render(string $view, array $data, string $format = 'a4'): Response
    {
        $pdf = Pdf::loadView($view, $data);

        $pdf->setPaper(...$this->paperOptions($format));

        return $pdf->stream();
    }

    /**
     * Resolve DomPDF paper options for the given format.
     *
     * @param  string  $format  'a4' | 'thermal80mm'
     * @return array{0: string|array<float>, 1: string}
     */
    private function paperOptions(string $format): array
    {
        return match ($format) {
            'thermal80mm' => [[0, 0, 226.77, 841.89], 'portrait'], // 80mm × 297mm in points
            default => ['a4', 'portrait'],
        };
    }
}
