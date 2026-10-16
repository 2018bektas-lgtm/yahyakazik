{{--
    Üst menü.

    Masaüstünde tema düzeni kullanılır. 991px altında menü, site.js
    tarafından açılıp kapatılan tam ekran bir panele dönüşür (Bootstrap'in
    collapse betiği devrede değildir; .show sınıfını biz yönetiriz).
--}}
@php
    $menu = [
        'home'              => 'Ana Sayfa',
        'hakkimizda'        => 'Hakkımızda',
        'faaliyet-alanlari' => 'Faaliyet Alanlarımız',
        'blog'              => 'Makalelerimiz',
        'sss'               => 'S.S.S.',
        'iletisim'          => 'İletişim',
    ];
@endphp

<nav class="navbar navbar-expand-lg">
    <div class="container">

        {{-- Logo --}}
        <div class="logo-wrapper">
            <a class="logo" href="{{ route('home') }}" aria-label="{{ config('site.unvan') }} — ana sayfa">
                @include('partials.logo', ['konum' => 'navbar'])
            </a>
        </div>

        {{-- Mobil menü düğmesi (açıkken çarpıya dönüşür) --}}
        <button class="navbar-toggler menu-dugmesi" type="button" id="menuDugmesi"
                aria-controls="navbar" aria-expanded="false" aria-label="Menüyü aç">
            <span class="cizgiler" aria-hidden="true">
                <span></span><span></span><span></span>
            </span>
        </button>

        {{-- Menü --}}
        <div class="collapse navbar-collapse ana-menu" id="navbar">

            <ul class="navbar-nav mx-auto">
                @foreach ($menu as $rota => $etiket)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs($rota) ? 'active' : '' }}"
                           href="{{ route($rota) }}">{{ $etiket }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="navbar-right">
                @include('partials.giris-kutusu')
                <div class="button"><a href="{{ route('basvuru') }}">Başvuru / Randevu</a></div>
            </div>

            {{-- Yalnızca mobil panelde görünür --}}
            <div class="menu-iletisim">
                <a href="tel:{{ config('site.telefon_link') }}" class="telefon">
                    <i class="fa-solid fa-phone"></i> {{ config('site.telefon') }}
                </a>
                <a href="mailto:{{ config('site.eposta') }}" class="eposta">
                    <i class="fa-regular fa-envelope"></i> {{ config('site.eposta') }}
                </a>
                <p class="adres">
                    <i class="fa-regular fa-location-dot"></i>
                    {{ config('site.adres') }}, {{ config('site.adres_2') }}
                </p>
            </div>

        </div>

    </div>
</nav>
