@extends('yonetim.layout')

@section('baslik', 'Takip Takvimi')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.etkinlikler.create') }}" class="dugme dugme-birincil"><i class="fa-solid fa-plus"></i> Takvim kaydı</a>
@endsection

@section('icerik')

    <div class="lejant cam">
        <span><i class="nokta" style="background: {{ $renkler['randevu'] }}"></i> Randevu</span>
        @foreach (\App\Models\Etkinlik::TURLER as $tur => $etiket)
            <span><i class="nokta tur-{{ $tur }}"></i> {{ $etiket }}</span>
        @endforeach
        <span class="soluk kucuk lejant-not">Boş bir güne tıklayıp kayıt ekleyin, kayıtları sürükleyerek taşıyın.</span>
    </div>

    <section class="kart cam takvim-kart">
        <div id="takvim"
             data-veriler="{{ route('yonetim.takvim.veriler') }}"
             data-tasi="{{ route('yonetim.takvim.tasi') }}"
             data-yeni-etkinlik="{{ route('yonetim.etkinlikler.create') }}"
             data-yeni-randevu="{{ route('yonetim.randevular.create') }}"></div>
    </section>

    {{-- Boş bir zamana tıklanınca çıkan seçim kutusu --}}
    <div class="secim cam" id="secim" hidden>
        <p id="secimBaslik"></p>
        <a href="#" class="dugme dugme-birincil" data-ne="randevu"><i class="fa-regular fa-user"></i> Randevu ekle</a>
        <a href="#" class="dugme" data-ne="durusma"><i class="fa-regular fa-gavel"></i> Duruşma ekle</a>
        <a href="#" class="dugme" data-ne="sure"><i class="fa-regular fa-hourglass-half"></i> Süre ekle</a>
        <a href="#" class="dugme" data-ne="musait_degil"><i class="fa-regular fa-ban"></i> Müsait değilim</a>
        <button type="button" class="soluk kucuk" id="secimKapat">Vazgeç</button>
    </div>

@endsection

@push('betikler')
    <script src="{{ asset('frontend/yonetim/vendor/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('frontend/yonetim/vendor/fullcalendar/tr.global.min.js') }}"></script>
    <script src="{{ asset('frontend/yonetim/takvim.js') }}"></script>
@endpush
