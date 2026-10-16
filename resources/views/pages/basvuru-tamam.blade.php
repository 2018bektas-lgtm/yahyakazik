@extends('layouts.app')

@section('baslik', 'Başvurunuz alındı')

@push('stiller')
    <link rel="stylesheet" href="{{ asset('frontend/css/basvuru.css') }}">
@endpush

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Teşekkürler',
        'baslik'    => 'Başvurunuz',
        'vurgu'     => 'alındı',
    ])

    <section class="section-padding basvuru">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="sihirbaz tamam">
                        <div class="tamam-ikon"><i class="fa-solid fa-check"></i></div>
                        @if ($randevu?->baslangic)
                            <h3>Randevu talebiniz kaydedildi</h3>
                            <p>
                                <strong>{{ $randevu->baslangic->translatedFormat('j F Y, l') }}</strong>
                                saat <strong>{{ $randevu->baslangic->format('H:i') }}</strong>
                                için {{ mb_strtolower($randevu->gorusmeSekliEtiketi()) }} görüşme talebinizi aldık.
                                Randevunuzu teyit etmek için sizi kısa süre içinde arayacağız.
                            </p>
                        @else
                            <h3>Başvurunuzu aldık</h3>
                            <p>Konunuzu inceleyip en kısa sürede verdiğiniz telefon numarasından size döneceğiz.</p>
                        @endif
                        <p class="ipucu">Çalışma saatlerimiz: {{ config('site.calisma_saatleri') }}</p>
                        <a href="{{ route('home') }}" class="button-4"><span>Ana sayfaya dön</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
