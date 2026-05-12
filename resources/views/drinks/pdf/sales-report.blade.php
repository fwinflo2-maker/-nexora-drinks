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
.doc-title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 4px; text-transform: uppercase; }
.period { text-align: center; color: #555; margin-bottom: 12px; font-size: 10px; }
table { width: 100%; border-collapse: collapse; }
thead th { background: #1a1a1a; color: #fff; padding: 6px 8px; font-size: 10px; text-align: left; }
tbody td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
.text-right { text-align: right; }
tfoot td { font-weight: 700; border-top: 2px solid #1a1a1a; padding-top: 6px; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'Rapport des ventes'])
<div class="doc-title">État des ventes</div>
<div class="period">Période : {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</div>
<table>
    <thead>
        <tr>
            <th>Article</th>
            <th class="text-right">Qté totale</th>
            <th class="text-right">Montant HT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['article_name'] }}</td>
            <td class="text-right">{{ number_format($row['total_qty'], 0, ',', ' ') }}</td>
            <td class="text-right">{{ number_format($row['total_amount_ht'], 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td class="text-right">{{ number_format(array_sum(array_column($rows, 'total_qty')), 0, ',', ' ') }}</td>
            <td class="text-right">{{ number_format(array_sum(array_column($rows, 'total_amount_ht')), 0, ',', ' ') }} FCFA</td>
        </tr>
    </tfoot>
</table>
<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
