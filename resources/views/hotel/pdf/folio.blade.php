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
.doc-title { font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px; }
.guest-block { display: table; width: 100%; margin-bottom: 14px; border: 1px solid #e5e5e5; border-radius: 4px; padding: 10px 14px; }
.guest-col { display: table-cell; width: 50%; vertical-align: top; }
.guest-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
.guest-value { font-size: 11px; font-weight: 600; margin-top: 2px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
thead th { background: #1a1a1a; color: #fff; padding: 6px 8px; font-size: 10px; text-align: left; }
tbody td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
.text-right { text-align: right; }
.text-green { color: #16a34a; }
.text-orange { color: #ea580c; }
.text-blue { color: #2563eb; }
.cat-room { color: #2563eb; }
.cat-restaurant { color: #ea580c; }
.cat-service { color: #7c3aed; }
.cat-extra { color: #d97706; }
.cat-discount { color: #16a34a; }
.cat-payment { color: #059669; }
.summary-box { border: 2px solid #1a1a1a; border-radius: 4px; padding: 12px 14px; margin-top: 14px; }
.summary-row { display: table; width: 100%; padding: 3px 0; }
.summary-label { display: table-cell; font-size: 11px; }
.summary-amount { display: table-cell; text-align: right; font-size: 11px; font-weight: 600; }
.summary-divider { border-top: 1px solid #e5e5e5; margin: 4px 0; }
.summary-total { font-size: 13px; font-weight: 700; }
.summary-balance-due { font-size: 14px; font-weight: 700; color: #dc2626; }
.summary-balance-zero { font-size: 14px; font-weight: 700; color: #16a34a; }
.footer { margin-top: 30px; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #e5e5e5; padding-top: 10px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #555; margin: 10px 0 4px; }
</style>
</head>
<body>
@include('hotel.pdf._header', ['team' => $team, 'docType' => 'Folio client'])

<div class="doc-title">Folio de séjour — {{ $reservation->reference }}</div>

<div class="guest-block">
    <div class="guest-col">
        <div class="guest-label">Client</div>
        <div class="guest-value">{{ $reservation->guest?->name ?? '—' }}</div>
        @if($reservation->guest?->email)
        <div style="font-size:9px;color:#666;margin-top:2px;">{{ $reservation->guest->email }}</div>
        @endif
        @if($reservation->guest?->phone)
        <div style="font-size:9px;color:#666;">Tél : {{ $reservation->guest->phone }}</div>
        @endif
    </div>
    <div class="guest-col">
        <div class="guest-label">Chambre</div>
        <div class="guest-value">
            N° {{ $reservation->room?->number ?? '—' }}
            @if($reservation->room?->roomType?->name) — {{ $reservation->room->roomType->name }}@endif
        </div>
        <div style="font-size:9px;color:#666;margin-top:2px;">
            Arrivée : {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }}
            — Départ : {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}
            ({{ $reservation->nights }} nuit{{ $reservation->nights > 1 ? 's' : '' }})
        </div>
    </div>
</div>

<div class="section-title">Détail des prestations</div>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Catégorie</th>
            <th>Libellé</th>
            <th class="text-right">Montant</th>
        </tr>
    </thead>
    <tbody>
        @php
        $typeLabels = ['room'=>'Hébergement','service'=>'Service','extra'=>'Extra','discount'=>'Remise','restaurant'=>'Restaurant','payment'=>'Paiement'];
        $typeCss = ['room'=>'cat-room','service'=>'cat-service','extra'=>'cat-extra','discount'=>'cat-discount','restaurant'=>'cat-restaurant','payment'=>'cat-payment'];
        @endphp
        @foreach($folios as $folio)
        <tr>
            <td>{{ \Carbon\Carbon::parse($folio->created_at)->format('d/m/Y') }}</td>
            <td class="{{ $typeCss[$folio->type->value ?? $folio->type] ?? '' }}">
                {{ $typeLabels[$folio->type->value ?? $folio->type] ?? ($folio->type->value ?? $folio->type) }}
            </td>
            <td>{{ $folio->label }}</td>
            <td class="text-right {{ in_array($folio->type->value ?? $folio->type, ['discount','payment']) ? 'text-green' : '' }}">
                @if(in_array($folio->type->value ?? $folio->type, ['discount','payment']))−@endif{{ number_format((float)$folio->amount, 0, ',', ' ') }} FCFA
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($fnbOrders->isNotEmpty())
<div class="section-title">Commandes restaurant liées</div>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Référence</th>
            <th>Articles</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fnbOrders as $order)
        <tr>
            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
            <td>{{ $order->reference }}</td>
            <td style="font-size:9px;color:#666;">
                @foreach($order->items as $item)
                {{ $item->quantity }}× {{ $item->menuItem?->name ?? '—' }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td class="text-right cat-restaurant">{{ number_format((float)$order->total, 0, ',', ' ') }} FCFA</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="summary-box">
    <div class="summary-row">
        <span class="summary-label">Hébergement ({{ $reservation->nights }} nuit{{ $reservation->nights > 1 ? 's' : '' }})</span>
        <span class="summary-amount">{{ number_format($balance['room'], 0, ',', ' ') }} FCFA</span>
    </div>
    @if($balance['restaurant'] > 0)
    <div class="summary-row">
        <span class="summary-label cat-restaurant">Restaurant</span>
        <span class="summary-amount cat-restaurant">{{ number_format($balance['restaurant'], 0, ',', ' ') }} FCFA</span>
    </div>
    @endif
    @if($balance['extras'] > 0)
    <div class="summary-row">
        <span class="summary-label">Services & extras</span>
        <span class="summary-amount">{{ number_format($balance['extras'], 0, ',', ' ') }} FCFA</span>
    </div>
    @endif
    @if($balance['discounts'] > 0)
    <div class="summary-row">
        <span class="summary-label text-green">Remises</span>
        <span class="summary-amount text-green">−{{ number_format($balance['discounts'], 0, ',', ' ') }} FCFA</span>
    </div>
    @endif
    <div class="summary-divider"></div>
    <div class="summary-row summary-total">
        <span class="summary-label">Total séjour</span>
        <span class="summary-amount">{{ number_format($balance['total'], 0, ',', ' ') }} FCFA</span>
    </div>
    @if($balance['paid'] > 0)
    <div class="summary-row">
        <span class="summary-label text-green">Déjà payé</span>
        <span class="summary-amount text-green">−{{ number_format($balance['paid'], 0, ',', ' ') }} FCFA</span>
    </div>
    @endif
    <div class="summary-divider" style="border-top:2px solid #1a1a1a;"></div>
    <div class="summary-row">
        <span class="summary-label {{ $balance['balance'] > 0 ? 'summary-balance-due' : 'summary-balance-zero' }}">Solde à régler</span>
        <span class="summary-amount {{ $balance['balance'] > 0 ? 'summary-balance-due' : 'summary-balance-zero' }}">{{ number_format($balance['balance'], 0, ',', ' ') }} FCFA</span>
    </div>
</div>

<div class="footer">
    Folio généré le {{ now()->format('d/m/Y à H:i') }} — {{ config('app.name') }}<br>
    {{ $team->name }} · Ce document est votre reçu officiel de séjour.
</div>
</body>
</html>
