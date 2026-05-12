<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
.pdf-header { display: table; width: 100%; padding: 14px 0 10px; border-bottom: 2px solid #1a1a1a; margin-bottom: 14px; }
.pdf-header-logo { display: table-cell; width: 70px; vertical-align: middle; padding-right: 14px; }
.logo-img { max-width: 60px; max-height: 60px; object-fit: contain; }
.pdf-header-info { display: table-cell; vertical-align: middle; }
.company-name { font-size: 18px; font-weight: 700; }
.company-meta { font-size: 9px; color: #555; margin-top: 2px; }
.doc-subtitle { font-size: 10px; color: #666; margin-top: 4px; }
.doc-title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 12px; text-transform: uppercase; }
.meta-grid { display: table; width: 100%; margin-bottom: 12px; }
.meta-col { display: table-cell; width: 50%; vertical-align: top; }
.meta-label { color: #666; font-size: 9px; text-transform: uppercase; }
.meta-value { font-weight: 600; margin-top: 1px; }
.amount-box { text-align: center; border: 2px solid #1a1a1a; border-radius: 4px; padding: 12px; margin: 16px 0; }
.amount-box .label { font-size: 10px; color: #666; }
.amount-box .value { font-size: 22px; font-weight: 700; margin-top: 4px; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'Reçu de paiement'])
<div class="doc-title">{{ $payment->code }}</div>
<div class="meta-grid">
    <div class="meta-col">
        <div class="meta-label">Client</div>
        <div class="meta-value">{{ $payment->client?->name ?? '—' }}</div>
        @if($payment->sale)
        <div class="meta-label" style="margin-top:6px">Vente liée</div>
        <div class="meta-value">{{ $payment->sale->code }}</div>
        @endif
    </div>
    <div class="meta-col" style="text-align:right">
        <div class="meta-label">Date</div>
        <div class="meta-value">{{ \Carbon\Carbon::parse($payment->document_date)->format('d/m/Y') }}</div>
        <div class="meta-label" style="margin-top:6px">Mode</div>
        <div class="meta-value" style="text-transform:capitalize">{{ $payment->mode->value ?? $payment->mode }}</div>
    </div>
</div>
<div class="amount-box">
    <div class="label">Montant reçu</div>
    <div class="value">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
</div>
<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
