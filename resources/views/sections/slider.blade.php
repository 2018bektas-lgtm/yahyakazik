{{--
    Anasayfa hero'su (config/icerik.php > hero).

    Dönen slayt yerine tek ve sabit bir açılış kullanılıyor; gerekçe config
    dosyasında açıklandı. Başlık kelimeleri sırayla aşağıdan yukarı gelir
    (site.css içindeki .hero-satir animasyonu).
--}}
@php
    $hero = config('icerik.hero');
@endphp

<header class="header hero-tek bg-img valign" data-overlay-dark="5"
        data-background="{{ asset($hero['gorsel']) }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12 text-center">

                <h5 class="hero-satir" style="--sira: 1">
                    <div class="icon"><i class="flaticon-courthouse"></i></div>
                    {!! $hero['ust_baslik'] !!}
                </h5>

                <h1 class="hero-baslik">
                    <span class="hero-satir" style="--sira: 2">{{ $hero['baslik'] }}</span>
                    <span class="hero-satir vurgu" style="--sira: 3">{{ $hero['vurgu'] }}</span>
                </h1>

                <p class="hero-aciklama hero-satir" style="--sira: 4">{{ $hero['aciklama'] }}</p>

                <div class="hero-butonlar hero-satir" style="--sira: 5">
                    @foreach ($hero['butonlar'] as $buton)
                        <a href="{{ route($buton['rota']) }}"
                           class="{{ $buton['birincil'] ? 'button-3' : 'button-hero-ikincil' }}">
                            {{ $buton['metin'] }}@if ($buton['birincil'])<span></span>@endif
                        </a>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- Aşağı kaydırma işareti --}}
    <a href="#tanitim" class="hero-kaydir" aria-label="Aşağı kaydır">
        <span class="cizgi"></span>
    </a>
</header>
