{{-- Çalışma süreci, dört adım (config/icerik.php > surec) --}}
<section class="surec section-padding">
    <div class="container">

        <div class="row">
            <div class="col-md-12 text-center mb-40">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Nasıl çalışıyoruz
                </div>
                <div class="section-title">Dosyanız <span>Adım Adım</span></div>
            </div>
        </div>

        <div class="row surec-satir">
            @foreach (config('icerik.surec') as $sira => $adim)
                <div class="col-lg-3 col-md-6 animate-box" data-animate-effect="fadeInUp"
                     style="--gecikme: {{ $sira * 120 }}ms">
                    <div class="surec-adim">
                        <div class="numara">{{ $adim['no'] }}</div>
                        <h5>{{ $adim['baslik'] }}</h5>
                        <p>{{ $adim['metin'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
