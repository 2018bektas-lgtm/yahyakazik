@extends('layouts.app')

@section('baslik', 'Faaliyet Alanlarımız')
@section('aciklama', 'İş, tazminat, aile, ceza, asker alma, memur, sağlık ve ticaret hukuku ile arabuluculuk alanlarında verdiğimiz hizmetler.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Neler yapıyoruz',
        'baslik'    => 'Faaliyet',
        'vurgu'     => 'Alanlarımız',
    ])

    <section class="alanlar section-padding">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12 text-center mb-50">
                    <p class="alanlar-giris">
                        Disiplin ve asker alma hukukundan iş ve ticaret hukukuna uzanan
                        geniş bir yelpazede avukatlık, arabuluculuk ve danışmanlık hizmeti
                        veriyoruz. Aşağıdaki başlıklardan dosyanıza en yakın olanı seçerek
                        kapsamı inceleyebilirsiniz.
                    </p>
                </div>
            </div>

            <div class="row">
                @foreach (config('icerik.faaliyet_alanlari') as $sira => $alan)
                    @php
                        // Detay metni yazılmış alanlar kendi sayfasına, yazılmamışlar
                        // doğrudan iletişime yönlendirilir.
                        $detayVar = array_key_exists($alan['slug'], config('icerik.faaliyet_detaylari', []));
                        $adres    = $detayVar ? route('faaliyet-alani', $alan['slug']) : route('iletisim');
                    @endphp

                    <div class="col-lg-4 col-md-6 animate-box" data-animate-effect="fadeInUp"
                         style="--gecikme: {{ ($sira % 3) * 90 }}ms">
                        <a href="{{ $adres }}" class="alan-kart">
                            <span class="ikon"><i class="{{ $alan['ikon'] }}"></i></span>
                            <h3>{{ $alan['ad'] }}</h3>
                            <p>{{ $alan['ozet'] }}</p>
                            <span class="devam">
                                {{ $detayVar ? 'Detaylı bilgi' : 'Bilgi alın' }}
                                <i class="fa-light fa-arrow-right"></i>
                            </span>
                            <span class="fon" aria-hidden="true"><i class="{{ $alan['ikon'] }}"></i></span>
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    @include('sections.surec')
    @include('sections.sss-listesi', ['adet' => 4])
    @include('partials.cta')

@endsection
