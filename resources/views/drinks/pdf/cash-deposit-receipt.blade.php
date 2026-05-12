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
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead th { background: #1a1a1a; color: #fff; padding: 6px 8px; font-size: 10px; text-align: left; }
tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
.text-right { text-align: right; }
.grand-total { display: table; width: 100%; margin-top: 12px; }
.grand-total-inner { display: table-cell; text-align: right; }
.grand-total-row { display: flex; justify-content: flex-end; gap: 40px; font-weight: 700; font-size: 13px; border-top: 2px solid #1a1a1a; padding-top: 6px; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'Reçu de dépôt de fonds'])
<div class="doc-title">{{ $cashDeposit->code }}</div>
<div class="meta-grid">
    <div class="meta-col">
        <div class="meta-label">Date</div>
        <div class="meta-value">{{ \Carbon\Carbon::parse($cashDeposit->document_date)->format('d/m/Y') }}</div>
        @if($cashDeposit->observation)
        <div class="meta-label" style="margin-top:6px">Observation</div>
        <div class="meta-value">{{ $cashDeposit->observation }}</div>
        @endif
    </div>
    <div class="meta-col" style="text-align:right">
        <div class="meta-label">Statut</div>
        <div class="meta-value">{{ $cashDeposit->status->value === 'validated' ? 'Validé' : 'Brouillon' }}</div>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>Mode</th>
            <th class="text-right">Montant</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Espèces</td>
            <td class="text-right">{{ number_format($cashDeposit->amount_cash, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>Chèques</td>
            <td class="text-right">{{ number_format($cashDeposit->amount_cheque, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr>
            <td>Autre</td>
            <td class="text-right">{{ number_format($cashDeposit->amount_other, 0, ',', ' ') }} FCFA</td>
        </tr>
    </tbody>
</table>
<div class="grand-total">
    <div class="grand-total-inner">
        <div class="grand-total-row">
            <span>Total versé</span>
            <span>{{ number_format($cashDeposit->total_amount, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>
</div>
<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
