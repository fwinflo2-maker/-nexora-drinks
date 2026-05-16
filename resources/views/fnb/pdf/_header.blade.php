{{--
    Shared F&B PDF header partial.
    Variables expected:
      $team    – Team model
      $docType – (optional) subtitle
--}}
<div class="pdf-header">
    @php
        $logoSrc = null;
        if ($team->logo_path) {
            $logoFullPath = storage_path('app/public/' . $team->logo_path);
            $logoExt = strtolower(pathinfo($logoFullPath, PATHINFO_EXTENSION));
            if (file_exists($logoFullPath) && $logoExt !== 'webp') {
                $logoSrc = 'file:///' . str_replace('\\', '/', $logoFullPath);
            }
        }
    @endphp
    @if($logoSrc)
    <div class="pdf-header-logo">
        <img src="{{ $logoSrc }}" alt="{{ $team->name }}" class="logo-img">
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
        @isset($docType)
            <p class="doc-subtitle">{{ $docType }}</p>
        @endisset
    </div>
</div>
