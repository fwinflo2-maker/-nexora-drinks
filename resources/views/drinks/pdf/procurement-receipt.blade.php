<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
.pdf-header { display: table; width: 100%; padding: 14px 0 12px; border-bottom: 2px solid #1a1a1a; margin-bottom: 16px; }
.pdf-header-logo { display: table-cell; width: 70px; vertical-align: middle; padding-right: 14px; }
.logo-img { max-width: 60px; max-height: 60px; object-fit: contain; }
.pdf-header-info { display: table-cell; vertical-align: middle; }
.company-name { font-size: 18px; font-weight: 700; }
.company-meta { font-size: 9px; color: #555; margin-top: 2px; }
.doc-subtitle { font-size: 10px; color: #666; margin-top: 4px; }
.doc-title { font-size: 15px; font-weight: 700; text-align: center; margin-bottom: 14px; text-transform: uppercase; }
.meta-grid { display: table; width: 100%; margin-bottom: 14px; }
.meta-col { display: table-cell; width: 50%; vertical-align: top; }
.meta-label { color: #666; font-size: 9px; text-transform: uppercase; }
.meta-value { font-weight: 600; margin-top: 1px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead th { background: #1a1a1a; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
tbody tr:last-child td { border-bottom: none; }
.text-right { text-align: right; }
.totals { margin-top: 12px; text-align: right; }
.totals .row { display: flex; justify-content: flex-end; gap: 40px; padding: 3px 0; }
.totals .row.grand { font-size: 13px; font-weight: 700; border-top: 2px solid #1a1a1a; padding-top: 6px; margin-top: 4px; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => "Bon d'approvisionnement"])
<div class="doc-title">{{ $procurement->code }}</div>
<div class="meta-grid">
    <div class="meta-col">
        <div class="meta-label">Fournisseur</div>
        <div class="meta-value">{{ $procurement->supplier?->name ?? '—' }}</div>
    </div>
    <div class="meta-col" style="text-align:right">
        <div class="meta-label">Date</div>
        <div class="meta-value">{{ $procurement->document_date?->format('d/m/Y') }}</div>
        <div class="meta-label" style="margin-top:6px">Statut</div>
        <div class="meta-value">{{ $procurement->status->value === 'validated' ? 'Validé' : 'Brouillon' }}</div>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>Article</th>
            <th class="text-right">Qté reçue</th>
            <th class="text-right">Prix unit.</th>
            <th class="text-right">Montant HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($procurement->articleLines as $line)
        <tr>
            <td>{{ $line->article?->name }}</td>
            <td class="text-right">{{ number_format($line->quantity_received, 0, ',', ' ') }}</td>
            <td class="text-right">{{ number_format($line->unit_price, 0, ',', ' ') }} FCFA</td>
            <td class="text-right">{{ number_format($line->amount, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="totals">
    <div class="row grand">
        <span>Total HT</span>
        <span>{{ number_format($procurement->total_ht, 0, ',', ' ') }} FCFA</span>
    </div>
</div>
<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
