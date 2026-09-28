@extends('layouts.app')

@section('baslik', $alan['ad'])
@section('aciklama', $alan['ozet'])

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => $detay['ust_baslik'],
        'baslik'    => $alan['ad'],
    ])

    <section class="alan-detay section-padding">
        <div class="container">
            <div class="row">

                {{-- Ana içerik --}}
                <div class="col-lg-8 col-md-12">

                    <div class="section-subtitle">
                        <div class="icon"><i class="{{ $alan['ikon'] }}"></i></div> Kapsam
                    </div>
                    <div class="section-title">{{ $alan['ad'] }}</div>

                    @foreach ($detay['giris'] as $paragraf)
                        <p>{{ $paragraf }}</p>
                    @endforeach

                    {{-- Hangi durumlarda --}}
                    <h3 class="alt-baslik">Hangi durumlarda destek veriyoruz?</h3>
                    <ul class="page-list list-unstyled durum-listesi">
                        @foreach ($detay['durumlar'] as $durum)
                            <li>
                                <div class="page-list-icon"> <span class="ti-check"></span> </div>
                                <div class="page-list-text">
                                    <p>{{ $durum }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Süreç --}}
                    <h3 class="alt-baslik">Süreç nasıl işliyor?</h3>
                    <div class="alan-surec">
                        @foreach ($detay['surec'] as $adim)
                            <div class="adim">
                                <div class="numara">{{ $adim['no'] }}</div>
                                <div class="govde">
                                    <h4>{{ $adim['baslik'] }}</h4>
                                    <p>{{ $adim['metin'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Alana özel sorular --}}
                    <h3 class="alt-baslik">Sıkça sorulanlar</h3>
                    <ul class="accordion-box clearfix">
                        @foreach ($detay['sss'] as $kayit)
                            <li class="accordion block">
                                <div class="acc-btn size-20">{{ $kayit['soru'] }}</div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">{{ $kayit['cevap'] }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @isset($ilgiliMakale)
                        <div class="ilgili-makale">
                            <span class="etiket">İlgili yazı</span>
                            <h4><a href="{{ route('yazi', $ilgiliMakale['slug']) }}">{{ $ilgiliMakale['baslik'] }}</a></h4>
                            <p>{{ $ilgiliMakale['ozet'] }}</p>
                        </div>
                    @endisset

                </div>

                {{-- Yan sütun --}}
                <aside class="col-lg-4 col-md-12">
                    <div class="yan-sutun">

                        <div class="kutu alanlar-kutusu">
                            <h4>Diğer faaliyet alanları</h4>
                            <ul>
                                @foreach (config('icerik.faaliyet_alanlari') as $diger)
                                    @continue($diger['slug'] === $alan['slug'])
                                    @php
                                        $detayVar = array_key_exists($diger['slug'], config('icerik.faaliyet_detaylari', []));
                                    @endphp
                                    <li>
                                        <a href="{{ $detayVar ? route('faaliyet-alani', $diger['slug']) : route('faaliyet-alanlari') }}">
                                            <i class="{{ $diger['ikon'] }}"></i> {{ $diger['ad'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="kutu iletisim-kutusu">
                            <h4>Dosyanızı konuşalım</h4>
                            <p>
                                Durumunuza özel değerlendirme için ön görüşme talep edebilirsiniz.
                                {{ config('site.calisma_saatleri') }}.
                            </p>
                            <a href="tel:{{ config('site.telefon_link') }}" class="telefon">
                                <i class="fa-solid fa-phone"></i> {{ config('site.telefon') }}
                            </a>
                            <a href="{{ route('iletisim') }}" class="button-3">
                                Randevu oluşturun<span></span>
                            </a>
                        </div>

                    </div>
                </aside>

            </div>
        </div>
    </section>

    @include('partials.cta')

@endsection
