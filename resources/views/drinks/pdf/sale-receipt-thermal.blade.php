<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #000; width: 226px; }
.center { text-align: center; }
.header { text-align: center; padding: 8px 0 6px; border-bottom: 1px dashed #000; margin-bottom: 8px; }
.header h1 { font-size: 14px; font-weight: 700; }
.doc-no { font-size: 11px; font-weight: 700; text-align: center; margin-bottom: 6px; }
.meta { margin-bottom: 6px; }
.meta p { font-size: 8px; }
hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
table { width: 100%; border-collapse: collapse; }
td { padding: 2px 0; }
.text-right { text-align: right; }
.total-row td { font-weight: 700; font-size: 10px; border-top: 1px solid #000; padding-top: 4px; }
.footer { text-align: center; font-size: 8px; color: #555; margin-top: 10px; }
</style>
</head>
<body>
<div class="header">
    <h1>{{ strtoupper($team->name) }}</h1>
    <p style="font-size:8px">Ticket de vente</p>
</div>
<div class="doc-no">{{ $sale->code }}</div>
<div class="meta">
    <p>Client : {{ $sale->client?->name ?? '—' }}</p>
    <p>Date : {{ $sale->document_date?->format('d/m/Y') }}</p>
</div>
<hr>
<table>
    @foreach($sale->articleLines as $line)
    <tr>
        <td>{{ $line->article?->name }}<br><small>{{ $line->quantity }} x {{ number_format($line->unit_price, 0, ',', ' ') }}</small></td>
        <td class="text-right">{{ number_format($line->amount_ht, 0, ',', ' ') }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
        <td>TOTAL TTC</td>
        <td class="text-right">{{ number_format($sale->total_ttc, 0, ',', ' ') }} FCFA</td>
    </tr>
</table>
<hr>
<div class="footer">{{ now()->format('d/m/Y H:i') }} — Merci !</div>
</body>
</html>
