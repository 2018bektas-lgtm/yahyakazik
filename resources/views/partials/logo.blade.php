{{--
    Logo. Hem üst menüde hem alt bilgide kullanılır.

    config/site.php içinde 'logo' bir dosya yoluna ayarlanmışsa o görsel basılır;
    değilse tema tipografisiyle uyumlu metin logosu kullanılır.

    Beklenen değişken:
        $konum - 'navbar' veya 'footer' (varsayılan: navbar)
--}}
@php
    $konum = $konum ?? 'navbar';
@endphp

@if (config('site.logo'))
    <img src="{{ asset(config('site.logo')) }}"
         class="{{ $konum === 'navbar' ? 'logo-img' : '' }}"
         alt="{{ config('site.unvan') }}">
@else
    <h2>{{ config('site.logo_ad') }} <i>{{ config('site.logo_vurgu') }}</i></h2>
    <span>{{ config('site.logo_alt') }}</span>
@endif
