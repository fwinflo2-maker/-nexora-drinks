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
.badge { display: inline-block; padding: 1px 6px; border-radius: 999px; font-size: 9px; }
.badge-ok { background: #dcfce7; color: #166534; }
.badge-low { background: #fef9c3; color: #854d0e; }
.badge-zero { background: #fee2e2; color: #991b1b; }
tfoot td { font-weight: 700; border-top: 2px solid #1a1a1a; padding-top: 6px; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'État des stocks'])
<div class="doc-title">État des stocks</div>
<div class="period">{{ $date ? 'Au '.\Carbon\Carbon::parse($date)->format('d/m/Y') : 'Stocks en temps réel' }}</div>
<table>
    <thead>
        <tr>
            <th>Article</th>
            <th class="text-right">Stock</th>
            <th class="text-right">Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['article_name'] }}</td>
            <td class="text-right">{{ number_format($row['stock_qty'], 0, ',', ' ') }}</td>
            <td class="text-right">
                @if($row['stock_qty'] <= 0)
                    <span class="badge badge-zero">Rupture</span>
                @elseif($row['stock_qty'] <= 10)
                    <span class="badge badge-low">Faible</span>
                @else
                    <span class="badge badge-ok">OK</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL ARTICLES</td>
            <td class="text-right">{{ count($rows) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
<div class="footer">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}</div>
</body>
</html>
