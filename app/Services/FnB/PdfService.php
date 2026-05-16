<?php

declare(strict_types=1);

namespace App\Services\FnB;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Exception as DompdfException;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data, string $format = 'a4'): Response
    {
        $pdf = Pdf::loadView($view, $data);

        $pdf->setPaper(...$this->paperOptions($format));

        try {
            return $pdf->stream();
        } catch (DompdfException $e) {
            if (str_contains($e->getMessage(), 'imagecreatefrom') || str_contains($e->getMessage(), 'Cannot convert')) {
                $data['team'] = isset($data['team']) ? clone $data['team'] : null;
                if ($data['team']) {
                    $data['team']->logo_path = null;
                }
                $pdf = Pdf::loadView($view, $data);
                $pdf->setPaper(...$this->paperOptions($format));

                return $pdf->stream();
            }

            throw $e;
        }
    }

    /** @return array{0: string|array<float>, 1: string} */
    private function paperOptions(string $format): array
    {
        return match ($format) {
            'thermal80mm' => [[0, 0, 226.77, 841.89], 'portrait'],
            default => ['a4', 'portrait'],
        };
    }
}
