@extends('yonetim.layout')

@section('baslik', 'Panel')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.takvim') }}" class="dugme"><i class="fa-regular fa-calendar-days"></i> Takvimi aç</a>
@endsection

@section('icerik')

    <div class="kutucuklar">
        <a href="{{ route('yonetim.randevular.index', ['durum' => 'beklemede']) }}" class="kutucuk cam {{ $bekleyenSayisi ? 'vurgulu' : '' }}">
            <span class="kutucuk-ikon"><i class="fa-regular fa-inbox"></i></span>
            <b>{{ $bekleyenSayisi }}</b><small>Yanıt bekleyen talep</small>
        </a>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon mavi"><i class="fa-regular fa-clock"></i></span>
            <b>{{ $bugunkuRandevular->count() }}</b><small>Bugünkü randevu</small>
        </div>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon yesil"><i class="fa-regular fa-calendar-week"></i></span>
            <b>{{ $haftalikRandevu }}</b><small>7 gün içinde randevu</small>
        </div>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon kirmizi"><i class="fa-regular fa-gavel"></i></span>
            <b>{{ $haftalikDurusma }}</b><small>7 gün içinde duruşma</small>
        </div>
    </div>

    @if ($gecikenSureler->isNotEmpty())
        <div class="bildirim cam hata">
            <i class="fa-regular fa-alarm-exclamation"></i>
            <div>
                <b>Tarihi geçmiş ve tamamlanmamış {{ $gecikenSureler->count() }} süre var:</b>
                @foreach ($gecikenSureler as $sure)
                    <a href="{{ route('yonetim.etkinlikler.edit', $sure) }}">{{ $sure->baslik }} ({{ $sure->baslangic->format('d.m.Y') }})</a>@if (! $loop->last), @endif
                @endforeach
            </div>
        </div>
    @endif

    <div class="izgara iki">
        <section class="kart cam">
            <header><h2>Bugün</h2><span class="soluk">{{ now()->translatedFormat('j F') }}</span></header>
            @forelse ($bugunkuRandevular as $randevu)
                <div class="satir">
                    <time class="saat-rozet">{{ $randevu->baslangic->format('H:i') }}</time>
                    <a href="{{ route('yonetim.randevular.edit', $randevu) }}" class="satir-govde">
                        <b>{{ $randevu->ad_soyad }}</b>
                        <small>{{ $randevu->konu ?? $randevu->tipEtiketi() }} · {{ $randevu->gorusmeSekliEtiketi() }}</small>
                    </a>
                    <span class="etiket durum-{{ $randevu->durum }}">{{ $randevu->durumEtiketi() }}</span>
                    @include('yonetim.partials.durum-dugmeleri')
                </div>
            @empty
                <p class="bos"><i class="fa-regular fa-mug-hot"></i> Bugün için randevu yok.</p>
            @endforelse
        </section>

        <section class="kart cam">
            <header>
                <h2>Yanıt bekleyenler</h2>
                <a href="{{ route('yonetim.randevular.index', ['durum' => 'beklemede']) }}" class="soluk">Tümü &rarr;</a>
            </header>
            @forelse ($bekleyenler as $randevu)
                <div class="satir">
                    <span class="etiket tip-{{ $randevu->tip }}">{{ $randevu->tipEtiketi() }}</span>
                    <a href="{{ route('yonetim.randevular.edit', $randevu) }}" class="satir-govde">
                        <b>{{ $randevu->ad_soyad }}</b>
                        <small>
                            @if ($randevu->baslangic){{ $randevu->baslangic->translatedFormat('j M, H:i') }} · @endif
                            {{ $randevu->konu ?? 'Konu belirtilmedi' }} · {{ $randevu->created_at->diffForHumans() }}
                        </small>
                    </a>
                    @include('yonetim.partials.durum-dugmeleri')
                </div>
            @empty
                <p class="bos"><i class="fa-regular fa-circle-check"></i> Bekleyen talep yok.</p>
            @endforelse
        </section>
    </div>

    <section class="kart cam">
        <header>
            <h2>Önümüzdeki 14 gün</h2>
            <a href="{{ route('yonetim.etkinlikler.create') }}" class="soluk">+ Kayıt ekle</a>
        </header>
        @forelse ($yaklasanlar as $etkinlik)
            <div class="satir">
                <time class="tarih-rozet">
                    <b>{{ $etkinlik->baslangic->format('j') }}</b>
                    <small>{{ $etkinlik->baslangic->translatedFormat('M') }}</small>
                </time>
                <a href="{{ route('yonetim.etkinlikler.edit', $etkinlik) }}" class="satir-govde">
                    <b>{{ $etkinlik->baslik }}</b>
                    <small>
                        {{ $etkinlik->turEtiketi() }}
                        · {{ $etkinlik->tum_gun ? 'Tüm gün' : $etkinlik->baslangic->format('H:i') }}
                        @if ($etkinlik->yer) · {{ $etkinlik->yer }} @endif
                        @if ($etkinlik->dosya_no) · {{ $etkinlik->dosya_no }} @endif
                    </small>
                </a>
                <span class="nokta tur-{{ $etkinlik->tur }}"></span>
                <form method="post" action="{{ route('yonetim.etkinlikler.tamamla', $etkinlik) }}">
                    @csrf @method('PATCH')
                    <button class="ikon-dugme onaylandi" title="Tamamlandı" aria-label="Tamamlandı: {{ $etkinlik->baslik }}"><i class="fa-solid fa-check"></i></button>
                </form>
            </div>
        @empty
            <p class="bos"><i class="fa-regular fa-calendar"></i> Önümüzdeki iki hafta için duruşma, süre veya toplantı girilmemiş.</p>
        @endforelse
    </section>

@endsection
