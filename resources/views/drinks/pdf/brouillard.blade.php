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
.period { text-align: center; color: #555; margin-bottom: 12px; font-size: 10px; }
.kpi-grid { display: table; width: 100%; margin-bottom: 16px; border: 1px solid #e5e5e5; border-radius: 4px; }
.kpi-cell { display: table-cell; text-align: center; padding: 10px; border-right: 1px solid #e5e5e5; }
.kpi-cell:last-child { border-right: none; }
.kpi-label { font-size: 9px; color: #666; text-transform: uppercase; }
.kpi-value { font-size: 14px; font-weight: 700; margin-top: 4px; }
.kpi-value.positive { color: #16a34a; }
.kpi-value.negative { color: #dc2626; }
.section-title { font-size: 11px; font-weight: 700; border-bottom: 1px solid #e5e5e5; padding-bottom: 4px; margin: 14px 0 8px; text-transform: uppercase; }
.detail-row { display: flex; justify-content: space-between; padding: 3px 0; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'Journal de caisse (Brouillard)'])
<div class="doc-title">Brouillard</div>
<div class="period">Période : {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</div>

@php
    $solde = $data['sales_total'] + $data['cash_inputs']['amount'] - $data['expenses']['amount'] - $data['cash_deposits']['amount'];
@endphp

<div class="kpi-grid">
    <div class="kpi-cell">
        <div class="kpi-label">Ventes TTC</div>
        <div class="kpi-value positive">{{ number_format($data['sales_total'], 0, ',', ' ') }}</div>
    </div>
    <div class="kpi-cell">
        <div class="kpi-label">Apports</div>
        <div class="kpi-value positive">{{ number_format($data['cash_inputs']['amount'], 0, ',', ' ') }}</div>
    </div>
    <div class="kpi-cell">
        <div class="kpi-label">Charges</div>
        <div class="kpi-value negative">{{ number_format($data['expenses']['amount'], 0, ',', ' ') }}</div>
    </div>
    <div class="kpi-cell">
        <div class="kpi-label">Versements</div>
        <div class="kpi-value negative">{{ number_format($data['cash_deposits']['amount'], 0, ',', ' ') }}</div>
    </div>
    <div class="kpi-cell">
        <div class="kpi-label">Solde estimé</div>
        <div class="kpi-value {{ $solde >= 0 ? 'positive' : 'negative' }}">{{ number_format($solde, 0, ',', ' ') }}</div>
    </div>
</div>

<div class="section-title">Détail</div>
<div class="detail-row"><span>Ventes validées</span><span><strong>{{ number_format($data['sales_total'], 0, ',', ' ') }} FCFA</strong></span></div>
<div class="detail-row"><span>Règlements reçus ({{ $data['payments']['count'] }})</span><span>{{ number_format($data['payments']['amount'], 0, ',', ' ') }} FCFA</span></div>
<div class="detail-row"><span>Apports caisse ({{ $data['cash_inputs']['count'] }})</span><span>{{ number_format($data['cash_inputs']['amount'], 0, ',', ' ') }} FCFA</span></div>
<div class="detail-row"><span>Charges ({{ $data['expenses']['count'] }})</span><span>- {{ number_format($data['expenses']['amount'], 0, ',', ' ') }} FCFA</span></div>
<div class="detail-row"><span>Versements banque ({{ $data['cash_deposits']['count'] }})</span><span>- {{ number_format($data['cash_deposits']['amount'], 0, ',', ' ') }} FCFA</span></div>

<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
