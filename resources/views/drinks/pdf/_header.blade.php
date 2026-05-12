{{--
    Shared PDF header partial.
    Variables expected:
      $team  – Team model (with logo_path and name)
      $docType – (optional) subtitle below name, e.g. "Facture de vente"
--}}
<div class="pdf-header">
    @if($team->logo_path && file_exists(storage_path('app/public/' . $team->logo_path)))
    <div class="pdf-header-logo">
        <img src="{{ storage_path('app/public/' . $team->logo_path) }}" alt="{{ $team->name }}" class="logo-img">
    </div>
    @endif
    <div class="pdf-header-info">
        <h1 class="company-name">{{ strtoupper($team->name) }}</h1>
        @if(!empty($team->settings_json['phone']))
            <p class="company-meta">Tél : {{ $team->settings_json['phone'] }}</p>
        @endif
        @if(!empty($team->settings_json['address']))
            <p class="company-meta">{{ $team->settings_json['address'] }}</p>
        @endif
        @if(!empty($team->settings_json['rccm']))
            <p class="company-meta">RCCM : {{ $team->settings_json['rccm'] }}</p>
        @endif
        @isset($docType)
            <p class="doc-subtitle">{{ $docType }}</p>
        @endisset
    </div>
</div>
