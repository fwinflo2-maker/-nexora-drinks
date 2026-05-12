<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 20px; }
.pdf-header { display: table; width: 100%; padding: 14px 0 10px; border-bottom: 2px solid #1a1a1a; margin-bottom: 14px; }
.pdf-header-logo { display: table-cell; width: 70px; vertical-align: middle; padding-right: 14px; }
.logo-img { max-width: 60px; max-height: 60px; object-fit: contain; }
.pdf-header-info { display: table-cell; vertical-align: middle; }
.company-name { font-size: 18px; font-weight: 700; }
.company-meta { font-size: 9px; color: #555; margin-top: 2px; }
.doc-title { font-size: 16px; font-weight: 700; text-align: center; margin-bottom: 4px; text-transform: uppercase; color: #d97706; }
.period { text-align: center; color: #555; margin-bottom: 20px; font-size: 11px; }

.roadmap-stop { margin-bottom: 25px; border: 1px solid #e5e5e5; border-radius: 8px; overflow: hidden; page-break-inside: avoid; }
.stop-header { background: #f8fafc; padding: 10px 15px; border-bottom: 1px solid #e5e5e5; }
.client-name { font-size: 13px; font-weight: 700; color: #1e293b; }
.client-address { font-size: 10px; color: #64748b; margin-top: 2px; }
.client-phone { font-size: 10px; font-weight: 600; color: #10b981; margin-top: 2px; }

table { width: 100%; border-collapse: collapse; }
thead th { background: #f1f5f9; color: #475569; padding: 6px 12px; font-size: 9px; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; }
tbody td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
.text-right { text-align: right; }
.qty-col { width: 80px; font-weight: 700; }

.footer { margin-top: 30px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 10px; }
.summary-box { margin-bottom: 30px; background: #fffbeb; border: 1px solid #fef3c7; padding: 15px; border-radius: 8px; }
.summary-title { font-size: 11px; font-weight: 700; color: #92400e; margin-bottom: 8px; }
</style>
</head>
<body>
@include('drinks.pdf._header', ['team' => $team, 'docType' => 'Logistique'])

<div class="doc-title">Feuille de Route Chauffeur</div>
<div class="period">Date de livraison : {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>

<div class="summary-box">
    <div class="summary-title">RÉSUMÉ DE LA TOURNÉE</div>
    <p>Nombre de clients à livrer : <strong>{{ count($sales) }}</strong></p>
    <p>Total articles (unités) : <strong>{{ collect($sales)->flatMap->articleLines->sum('quantity') }}</strong></p>
</div>

@if(count($sales) > 0)
    @foreach($sales as $index => $sale)
    <div class="roadmap-stop">
        <div class="stop-header">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell;">
                    <div class="client-name">STOP #{{ $index + 1 }} : {{ $sale->client->name }}</div>
                    <div class="client-address">📍 {{ $sale->client->address ?? 'Pas d\'adresse renseignée' }}</div>
                    @if($sale->client->phone)
                        <div class="client-phone">📞 {{ $sale->client->phone }}</div>
                    @endif
                </div>
                <div style="display: table-cell; text-align: right; vertical-align: top;">
                    <div style="font-size: 9px; color: #94a3b8;">Bon N° {{ $sale->code }}</div>
                </div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Désignation Article</th>
                    <th class="text-right qty-col">Quantité</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->articleLines as $line)
                <tr>
                    <td>{{ $line->article->name }}</td>
                    <td class="text-right qty-col">{{ $line->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
@else
    <div style="text-align: center; padding: 40px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 12px;">
        Aucune livraison prévue pour cette date.
    </div>
@endif

<div class="footer">
    Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $team->name }}
    <br>
    Logiciel de gestion Nexora Drinks
</div>
</body>
</html>
