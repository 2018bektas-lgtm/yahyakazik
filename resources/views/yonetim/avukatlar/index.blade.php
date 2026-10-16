@extends('yonetim.layout')

@section('baslik', 'Avukatlar')

@section('ust_dugmeler')
    <a href="{{ route('hakkimizda') }}#ekibimiz" target="_blank" rel="noopener" class="dugme"><i class="fa-regular fa-arrow-up-right-from-square"></i> Sitede gör</a>
    <a href="{{ route('yonetim.avukatlar.create') }}" class="dugme dugme-birincil"><i class="fa-solid fa-plus"></i> Avukat ekle</a>
@endsection

@section('icerik')

    <p class="soluk ust-aciklama">Buradaki avukatlar Hakkımızda sayfasındaki <b>Ekibimiz</b> bölümünde, "Sıra" değerine göre listelenir. Her avukatın kendi profil sayfası olur.</p>

    <div class="avukat-izgara">
        @foreach ($avukatlar as $avukat)
            <a href="{{ route('yonetim.avukatlar.edit', $avukat) }}" class="kart cam avukat-ozet {{ $avukat->yayinda ? '' : 'pasif' }}">
                <span class="avukat-foto">
                    @if ($avukat->fotograf)
                        <img src="{{ asset($avukat->fotograf) }}" alt="">
                    @else
                        {{ $avukat->basHarfler() }}
                    @endif
                </span>
                <span class="avukat-bilgi">
                    <b>{{ $avukat->ad }}</b>
                    <small>{{ $avukat->unvan ?: 'Unvan girilmemiş' }}</small>
                    <span class="avukat-etiketler">
                        <span class="etiket {{ $avukat->yayinda ? 'durum-onaylandi' : 'durum-iptal' }}">{{ $avukat->yayinda ? 'Yayında' : 'Gizli' }}</span>
                        <span class="etiket">Sıra {{ $avukat->sira }}</span>
                        @if ($avukat->uzmanliklar)<span class="etiket">{{ count($avukat->uzmanliklar) }} alan</span>@endif
                    </span>
                </span>
                <i class="fa-regular fa-pen-to-square duzenle-ikon" aria-hidden="true"></i>
            </a>
        @endforeach

        <a href="{{ route('yonetim.avukatlar.create') }}" class="kart cam avukat-ozet yeni">
            <span class="avukat-foto"><i class="fa-regular fa-user-plus"></i></span>
            <span class="avukat-bilgi"><b>Yeni avukat ekle</b><small>Ekibe katılan avukatın profilini oluşturun</small></span>
        </a>
    </div>

@endsection
