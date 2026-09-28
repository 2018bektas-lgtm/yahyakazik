{{--
    Tek bir makale kartı. Anasayfadaki kaydırmalı listede ve Makalelerimiz
    sayfasındaki ızgarada kullanılır.

    Kapak: 'gorsel' doluysa fotoğraf, boşsa konuya uygun ikonlu editoryal
    kapak basılır. Tema ile gelen stok fotoğraflar yazı konularıyla
    uyuşmadığı için şimdilik ikonlu kapak kullanılıyor.

    Beklenen değişken: $yazi (config/icerik.php > yazilar içindeki bir kayıt)
--}}
<div class="item mt-10 mb-30">

    @if (! empty($yazi['gorsel']))
        <div class="img">
            <img src="{{ asset($yazi['gorsel']) }}" class="img-fluid" alt="{{ $yazi['baslik'] }}">
            <div class="cat">{{ $yazi['kategori'] }}</div>
        </div>
    @else
        <div class="img yazi-kapak">
            <i class="{{ $yazi['ikon'] ?? 'flaticon-courthouse' }}" aria-hidden="true"></i>
            <div class="cat">{{ $yazi['kategori'] }}</div>
        </div>
    @endif

    <div class="cont">
        <h4><a href="{{ route('yazi', $yazi['slug']) }}">{{ $yazi['baslik'] }}</a></h4>
        <p>{{ $yazi['ozet'] }}</p>

        <div class="author">
            <div>
                <h5>{{ $yazi['tarih'] }}</h5>
                <h5>{{ config('site.ad') }}</h5>
            </div>
        </div>
    </div>

</div>
