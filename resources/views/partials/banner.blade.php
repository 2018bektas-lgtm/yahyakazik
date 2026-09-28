{{--
    İç sayfaların üst görsel başlığı.

    Beklenen değişkenler:
        $ustBaslik  - küçük üst yazı
        $baslik     - ana başlık (ilk kelimeler)
        $vurgu      - başlığın renkli vurgulanan bölümü (isteğe bağlı)
        $gorsel     - arka plan görseli yolu (isteğe bağlı)
--}}
@php
    $gorsel = $gorsel ?? config('site.banner_gorsel');
@endphp

<div class="banner-header valign bg-img bg-fixed" data-overlay-dark="5"
     data-background="{{ asset($gorsel) }}">
    <div class="container">
        <div class="row">
            <div class="col-md-12 caption mt-60 text-center">
                <h6>
                    <div class="icon"><i class="flaticon-courthouse"></i></div> {{ $ustBaslik }}
                </h6>
                <h1>{{ $baslik }} @isset($vurgu)<span>{{ $vurgu }}</span>@endisset</h1>
            </div>
        </div>
    </div>
</div>
