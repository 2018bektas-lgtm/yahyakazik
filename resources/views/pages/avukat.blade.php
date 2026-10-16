@extends('layouts.app')

@section('baslik', $avukat->ad)
@section('aciklama', \Illuminate\Support\Str::limit($avukat->ozet ?: ($avukat->paragraflar()[0] ?? ''), 160))

@php
    // Uzmanlık adı bir faaliyet alanıyla eşleşiyorsa o alanın sayfasına bağlanır
    $alanlar = collect(config('icerik.faaliyet_alanlari', []));
    $detayli = array_keys(config('icerik.faaliyet_detaylari', []));
    $alanBagi = function (string $uzmanlik) use ($alanlar, $detayli) {
        $alan = $alanlar->first(fn ($a) => mb_strtolower($a['ad']) === mb_strtolower($uzmanlik));

        return $alan && in_array($alan['slug'], $detayli, true) ? route('faaliyet-alani', $alan['slug']) : null;
    };
@endphp

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => $avukat->unvan ?: 'Ekibimiz',
        'baslik'    => $avukat->ad,
    ])

    <section class="avukat-profil section-padding">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-12 mb-40">
                    <aside class="profil-kart">
                        <div class="avukat-gorsel">
                            @if ($avukat->fotograf)
                                <img src="{{ asset($avukat->fotograf) }}" alt="{{ $avukat->ad }}">
                            @else
                                <span class="monogram" aria-hidden="true">{{ $avukat->basHarfler() }}</span>
                            @endif
                        </div>
                        <div class="profil-govde">
                            @if ($avukat->unvan)<span class="avukat-unvan">{{ $avukat->unvan }}</span>@endif
                            <h3>{{ $avukat->ad }}</h3>
                            @if ($avukat->baro)<span class="avukat-baro"><i class="fa-regular fa-scale-balanced"></i> {{ $avukat->baro }}</span>@endif

                            <ul class="profil-iletisim">
                                @if ($avukat->eposta)
                                    <li><i class="fa-regular fa-envelope"></i><a href="mailto:{{ $avukat->eposta }}">{{ $avukat->eposta }}</a></li>
                                @endif
                                <li><i class="fa-regular fa-phone"></i><a href="tel:{{ config('site.telefon_link') }}">{{ config('site.telefon') }}</a></li>
                                @if ($avukat->linkedin)
                                    <li><i class="fa-brands fa-linkedin-in"></i><a href="{{ $avukat->linkedin }}" target="_blank" rel="noopener">LinkedIn profili</a></li>
                                @endif
                            </ul>

                            <a href="{{ route('basvuru') }}" class="profil-buton"><i class="fa-regular fa-calendar-check"></i> Randevu alın</a>
                        </div>
                    </aside>
                </div>

                <div class="col-lg-7 offset-lg-1 col-md-12">
                    <div class="section-subtitle">
                        <div class="icon"><i class="flaticon-courthouse"></i></div> Hakkında
                    </div>
                    <div class="section-title">{{ $avukat->ad }}</div>

                    @forelse ($avukat->paragraflar() as $paragraf)
                        <p>{{ $paragraf }}</p>
                    @empty
                        @if ($avukat->ozet)<p>{{ $avukat->ozet }}</p>@endif
                    @endforelse

                    @if ($avukat->uzmanliklar)
                        <div class="profil-alanlar mt-30">
                            <h5>Çalıştığı alanlar</h5>
                            <ul>
                                @foreach ($avukat->uzmanliklar as $uzmanlik)
                                    <li>
                                        @if ($bag = $alanBagi($uzmanlik))
                                            <a href="{{ $bag }}">{{ $uzmanlik }} <i class="fa-regular fa-arrow-right"></i></a>
                                        @else
                                            <span>{{ $uzmanlik }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    @include('sections.ozgecmis', [
        'kayitlar'   => $avukat->ozgecmis ?? [],
        'baslikHtml' => 'Eğitim <span>ve</span> Deneyim',
    ])

    @if ($digerleri->isNotEmpty())
        <section class="ekip section-padding pt-0">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center mb-40">
                        <div class="section-subtitle">
                            <div class="icon"><i class="flaticon-courthouse"></i></div> Ekibimiz
                        </div>
                        <div class="section-title">Diğer <span>avukatlarımız</span></div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    @foreach ($digerleri as $diger)
                        <div class="col-lg-4 col-md-6 mb-30">
                            @include('partials.avukat-karti', ['avukat' => $diger])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('partials.cta')

@endsection
