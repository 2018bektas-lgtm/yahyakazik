{{--
    Yönetim paneli ana şablonu (liquid glass tasarım, public/yonetim/yonetim.css).

    @extends('yonetim.layout')
    @section('baslik', 'Sayfa başlığı')
    @section('icerik') ... @endsection
--}}
@php
    $bekleyenSayisi = \App\Models\Randevu::where('durum', 'beklemede')->count();
    $menu = [
        ['yonetim.panel', 'yonetim.panel', 'fa-grid-2', 'Panel', null],
        ['yonetim.randevular.index', 'yonetim.randevular.*', 'fa-calendar-check', 'Randevular', $bekleyenSayisi ?: null],
        ['yonetim.yapilacaklar', 'yonetim.yapilacaklar', 'fa-list-check', 'Yapılacaklar', null],
        ['yonetim.takvim', 'yonetim.takvim*', 'fa-calendar-days', 'Takvim', null],
    ];
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('baslik', 'Panel') · {{ config('site.kisa_unvan') }} Yönetim</title>
    <link rel="icon" href="{{ asset('frontend/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jost:wght@300..700&family=Playfair+Display:wght@500;600&display=swap">
    <link rel="stylesheet" href="{{ asset('frontend/css/plugins/font-awesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/yonetim/yonetim.css') }}">
    @stack('stiller')
</head>
<body>
    <div class="arkaplan" aria-hidden="true"><span></span><span></span><span></span></div>

    <div class="kabuk">
        <aside class="kenar cam">
            <a href="{{ route('yonetim.panel') }}" class="marka">
                <span class="marka-ikon"><i class="fa-solid fa-scale-balanced"></i></span>
                <span><b>{{ config('site.kisa_unvan') }}</b><small>Yönetim</small></span>
            </a>

            <nav class="menu">
                @foreach ($menu as [$rota, $desen, $ikon, $etiket, $rozet])
                    <a href="{{ route($rota) }}" class="{{ request()->routeIs($desen) ? 'aktif' : '' }}">
                        <i class="fa-regular {{ $ikon }}"></i><span>{{ $etiket }}</span>
                        @if ($rozet)<em class="rozet">{{ $rozet }}</em>@endif
                    </a>
                @endforeach
            </nav>

            <div class="hizli">
                <a href="{{ route('yonetim.randevular.create') }}" class="dugme dugme-birincil"><i class="fa-solid fa-plus"></i> Randevu</a>
                <a href="{{ route('yonetim.etkinlikler.create') }}" class="dugme"><i class="fa-solid fa-plus"></i> Takvim kaydı</a>
            </div>

            <div class="kenar-alt">
                <a href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fa-regular fa-arrow-up-right-from-square"></i> Siteyi aç</a>
                <form method="post" action="{{ route('yonetim.cikis') }}">
                    @csrf
                    <button type="submit"><i class="fa-regular fa-arrow-right-from-bracket"></i> Çıkış <small>{{ auth()->user()->name }}</small></button>
                </form>
            </div>
        </aside>

        <main class="icerik">
            <header class="ust">
                <div>
                    <p class="ust-tarih">{{ now()->translatedFormat('j F Y, l') }}</p>
                    <h1>@yield('baslik', 'Panel')</h1>
                </div>
                <div class="ust-sag">@yield('ust_dugmeler')</div>
            </header>

            @if (session('bilgi'))
                <div class="bildirim cam" role="status"><i class="fa-regular fa-circle-check"></i> {{ session('bilgi') }}</div>
            @endif

            @if ($errors->any())
                <div class="bildirim cam hata" role="alert">
                    <i class="fa-regular fa-triangle-exclamation"></i>
                    <div>@foreach ($errors->all() as $hata)<div>{{ $hata }}</div>@endforeach</div>
                </div>
            @endif

            @yield('icerik')
        </main>
    </div>

    @stack('betikler')
</body>
</html>
