{{--
    Kurucu avukatı tanıtan koyu zeminli bölüm.

    config/site.php > avukat_gorsel doldurulmuşsa iki sütunlu (fotoğraf +
    metin), boşsa tek sütunlu ortalanmış olarak görünür. Boş bırakmak
    bilinçli bir tercihtir: buraya stok fotoğraf konulamaz.
--}}
@php
    $portre = config('site.avukat_gorsel');
@endphp

<section class="about section-padding bg-darkbrown">
    <div class="container">
        <div class="row justify-content-center align-items-center">

            @if ($portre)
                <div class="col-lg-5 col-md-12 animate-box" data-animate-effect="fadeInLeft">
                    <img class="img" src="{{ asset($portre) }}" alt="{{ config('site.ad') }}">
                </div>
            @endif

            <div class="{{ $portre ? 'col-lg-5 offset-lg-1' : 'col-lg-8 text-center' }} col-md-12 animate-box"
                 data-animate-effect="{{ $portre ? 'fadeInRight' : 'fadeInUp' }}">

                <div class="section-subtitle text-white">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Kurucu avukat
                </div>
                <div class="section-title white">Disiplin hukukundan <span>gelen bir bakış</span></div>

                <p>
                    Türk Silahlı Kuvvetleri'nde disiplin subaylığı ve Millî Savunma Bakanlığı
                    müfettişliği görevlerinde bulunan {{ config('site.ad') }}, kamu tarafındaki
                    işleyişi içeriden bilerek avukatlık yapıyor. Bu deneyim; özellikle disiplin
                    hukuku, asker alma hukuku ve memur hukuku dosyalarında sürecin nasıl
                    yürüdüğünü baştan görebilmeyi sağlıyor.
                </p>

                <div class="about-bottom mt-30 {{ $portre ? '' : 'justify-content-center' }}">
                    <img src="{{ asset('frontend/img/signature.svg') }}" alt="" class="image about-signature">
                    <div class="about-name-wrapper">
                        <div class="about-rol">Kurucu Avukat &amp; Arabulucu</div>
                        <div class="about-name">{{ config('site.ad') }}</div>
                    </div>
                </div>

                {{-- Profil sayfası avukatlar tablosundan gelir; kayıt yoksa (migrate öncesi) Hakkımızda'ya gider --}}
                @php $kurucuProfil = \App\Models\Avukat::ekip()->first(); @endphp
                <a href="{{ $kurucuProfil ? route('avukat', $kurucuProfil) : route('hakkimizda') }}" class="button-2 mt-30">
                    Özgeçmişi görün<span></span>
                </a>
            </div>

        </div>
    </div>
</section>
