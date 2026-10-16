{{--
    Anasayfa hero'su (config/icerik.php > hero).

    Dönen slayt yerine tek ve sabit bir açılış kullanılıyor; gerekçe config
    dosyasında açıklandı. Başlık kelimeleri sırayla aşağıdan yukarı gelir
    (site.css içindeki .hero-satir animasyonu).

    Soldaki cam (liquid glass) kart, müvekkillerin dava konularını bir bakışta
    görmesi içindir. Her madde o konudaki makaleye gider; makalesi yoksa
    faaliyet alanı sayfasına, o da yoksa başvuru sayfasına (konu seçili) gider.
    Liste adları config/icerik.php > faaliyet_alanlari > 'dava' alanındadır.
--}}
@php
    $hero = config('icerik.hero');
    $yazilar = collect(config('icerik.yazilar', []));
    $detayliKonular = array_keys(config('icerik.faaliyet_detaylari', []));

    $konular = collect(config('icerik.faaliyet_alanlari', []))->map(function ($konu) use ($yazilar, $detayliKonular) {
        $yazi = $yazilar->firstWhere('kategori', $konu['ad']);

        $konu['link'] = match (true) {
            $yazi !== null                                  => route('yazi', $yazi['slug']),
            in_array($konu['slug'], $detayliKonular, true) => route('faaliyet-alani', $konu['slug']),
            default                                         => route('basvuru', ['konu' => $konu['ad']]),
        };
        $konu['ipucu'] = $yazi['baslik'] ?? $konu['ozet'];

        return $konu;
    });
@endphp

<header class="header hero-tek bg-img valign" data-overlay-dark="5"
        data-background="{{ asset($hero['gorsel']) }}">
    <div class="container">
        <div class="row align-items-center hero-izgara">
            <div class="col-lg-5 col-md-12 order-2 order-lg-1">
                <aside class="dava-konulari hero-satir" style="--sira: 6" aria-labelledby="davaKonulariBaslik">
                    <p class="dava-konulari-ust">Dava konularımız</p>
                    <h2 id="davaKonulariBaslik">Hangi konuda yardıma ihtiyacınız var?</h2>
                    <ul>
                        @foreach ($konular as $konu)
                            <li>
                                <a href="{{ $konu['link'] }}" title="{{ $konu['ipucu'] }}">
                                    <i class="{{ $konu['ikon'] }}" aria-hidden="true"></i>
                                    <span>{{ $konu['dava'] ?? $konu['ad'] }}</span>
                                    <i class="fa-regular fa-arrow-right ok" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('basvuru') }}" class="dava-konulari-alt">
                        Konunuzu göremediniz mi? <b>Durumunuzu anlatın</b>
                    </a>
                </aside>
            </div>

            <div class="col-lg-7 col-md-12 order-1 order-lg-2 hero-metin">

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
