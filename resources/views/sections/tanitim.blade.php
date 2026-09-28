{{-- Kısa tanıtım + kuruluş rozeti --}}
<section class="about section-padding" id="tanitim">
    <div class="container">
        <div class="row">

            <div class="col-lg-5 col-md-12 mb-30 animate-box" data-animate-effect="fadeInUp">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Bizi tanıyın
                </div>
                <div class="section-title">Hukukta saygın <span>çözüm ortağınız</span></div>
                <p>
                    {{ config('site.unvan') }} olarak {{ config('site.ad') }} önderliğinde,
                    Ankara ili başta olmak üzere Türkiye genelinde avukatlık, arabuluculuk ve
                    hukuki danışmanlık faaliyetlerimizi sürdürüyoruz. Her dosyayı kendi koşulları
                    içinde değerlendirir, gerçekçi bir yol haritası çizeriz.
                </p>

                <ul class="page-list list-unstyled mb-25">
                    @foreach ([
                        'Kamu kurumlarında edinilmiş köklü deneyim.',
                        'Özel hukuk ve kamu yönetiminde iki yüksek lisans.',
                        'Avukatlık ve arabuluculuk hizmetinin bir arada yürütülmesi.',
                    ] as $madde)
                        <li>
                            <div class="page-list-icon"> <span class="ti-check"></span> </div>
                            <div class="page-list-text">
                                <p>{{ $madde }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Hakkımızda sayfasındayken kendine bağlantı vermek yerine iletişime yönlendir --}}
                @if (request()->routeIs('hakkimizda'))
                    <a href="{{ route('iletisim') }}" class="button-2">Bize ulaşın<span></span></a>
                @else
                    <a href="{{ route('hakkimizda') }}" class="button-2">Hakkımızda dahası<span></span></a>
                @endif
            </div>

            <div class="col-lg-6 offset-lg-1 col-md-12 animate-box" data-animate-effect="fadeInUp">
                <div class="item">
                    <div class="year-box vert-move">
                        <div class="number">{{ config('site.meslek_baslangic') }}</div>
                        <div class="txt">yılından beri</div>
                        <div class="number-bg"></div>
                    </div>
                    <img src="{{ asset(config('site.ofis_gorsel')) }}" class="img-fluid"
                         alt="{{ config('site.unvan') }}">
                </div>
            </div>

        </div>
    </div>
</section>
