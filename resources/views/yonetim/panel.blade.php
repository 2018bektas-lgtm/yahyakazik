@extends('yonetim.layout')

@section('baslik', 'Panel')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.takvim') }}" class="dugme"><i class="fa-regular fa-calendar-days"></i> Takvimi aç</a>
@endsection

@section('icerik')

    <div class="kutucuklar">
        <a href="{{ route('yonetim.randevular.index', ['durum' => 'beklemede']) }}" class="kutucuk cam {{ $bekleyenSayisi ? 'vurgulu' : '' }}">
            <span class="kutucuk-ikon"><i class="fa-regular fa-inbox"></i></span>
            <span class="kutucuk-metin"><b>{{ $bekleyenSayisi }}</b><small>Yanıt bekleyen talep</small></span>
        </a>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon mavi"><i class="fa-regular fa-clock"></i></span>
            <span class="kutucuk-metin"><b>{{ $bugunkuRandevular->count() }}</b><small>Bugünkü randevu</small></span>
        </div>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon yesil"><i class="fa-regular fa-calendar-week"></i></span>
            <span class="kutucuk-metin"><b>{{ $haftalikRandevu }}</b><small>7 gün içinde randevu</small></span>
        </div>
        <div class="kutucuk cam">
            <span class="kutucuk-ikon kirmizi"><i class="fa-regular fa-gavel"></i></span>
            <span class="kutucuk-metin"><b>{{ $haftalikDurusma }}</b><small>7 gün içinde duruşma</small></span>
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
        <div class="sutun">
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

        {{-- Duruşma ve randevular tek halka grafikte; dönem düğmeleri sayfayı yenilemeden değiştirir --}}
        <section class="kart cam dagilim" data-grafik='@json($grafik)'>
            <header>
                <h2>Duruşma ve randevular</h2>
                <div class="donem-secici" role="tablist" aria-label="Dönem">
                    @foreach ($grafik as $anahtar => $donem)
                        <button type="button" role="tab" data-donem="{{ $anahtar }}" aria-selected="{{ $anahtar === 'ay' ? 'true' : 'false' }}">{{ $donem['etiket'] }}</button>
                    @endforeach
                </div>
            </header>
            <div class="dagilim-govde">
                <svg viewBox="0 0 140 140" class="halka-grafik" role="img" aria-labelledby="dagilimOzet">
                    <circle class="iz" cx="70" cy="70" r="54"/>
                    <circle class="dilim randevu" cx="70" cy="70" r="54" pathLength="100"/>
                    <circle class="dilim durusma" cx="70" cy="70" r="54" pathLength="100"/>
                    <text x="70" y="68" class="toplam">{{ $grafik['ay']['randevu'] + $grafik['ay']['durusma'] }}</text>
                    <text x="70" y="88" class="toplam-etiket">toplam</text>
                </svg>
                <ul class="lejant">
                    <li class="randevu"><span class="nokta"></span><span>Randevu</span><b data-alan="randevu">{{ $grafik['ay']['randevu'] }}</b><small data-alan="randevu-oran"></small></li>
                    <li class="durusma"><span class="nokta"></span><span>Duruşma</span><b data-alan="durusma">{{ $grafik['ay']['durusma'] }}</b><small data-alan="durusma-oran"></small></li>
                </ul>
            </div>
            <p class="sr-yalniz" id="dagilimOzet">Bu ay {{ $grafik['ay']['randevu'] }} randevu, {{ $grafik['ay']['durusma'] }} duruşma.</p>
        </section>
        </div>

        <section class="kart cam yapilacaklar-ozet">
            <header>
                <div>
                    <h2>Bugün yapılacaklar</h2>
                    <p class="soluk kucuk ilerleme-metni">
                        @if ($yapilacaklar->isNotEmpty())
                            {{ $yapilacaklar->count() }} işten {{ $yapilacaklar->where('tamam', true)->count() }} tanesi tamamlandı
                        @else
                            {{ now()->translatedFormat('j F') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('yonetim.yapilacaklar') }}" class="soluk">Saatlik görünüm &rarr;</a>
            </header>

            <div class="yapilacak-listesi">
                @forelse ($yapilacaklar as $kalem)
                    @include('yonetim.partials.yapilacak', ['kalem' => $kalem])
                @empty
                    <p class="bos"><i class="fa-regular fa-mug-hot"></i> Bugün için randevu ya da görev yok.</p>
                @endforelse
            </div>

            <form method="post" action="{{ route('yonetim.gorevler.store') }}" class="hizli-gorev">
                @csrf
                <input type="hidden" name="tarih" value="{{ now()->toDateString() }}">
                <input type="text" name="baslik" required maxlength="255" placeholder="Bugüne görev ekle…" aria-label="Görev">
                <input type="time" name="saat" min="{{ now()->format('H:i') }}" aria-label="Saat (isteğe bağlı)">
                <button type="submit" class="dugme dugme-birincil" aria-label="Görevi ekle"><i class="fa-solid fa-plus"></i></button>
            </form>
        </section>
    </div>

    <div class="izgara iki">
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
    </div>

    <script>
        (function () {
            var kart = document.querySelector('.dagilim');
            if (!kart) return;
            var veri = JSON.parse(kart.dataset.grafik);
            var randevu = kart.querySelector('.dilim.randevu');
            var durusma = kart.querySelector('.dilim.durusma');
            var bosluk = 6; // iki dilim arasındaki aralık (yüzde; yuvarlak uçların payı dahil)

            function goster(anahtar) {
                var d = veri[anahtar], toplam = d.randevu + d.durusma;
                var r = toplam ? d.randevu / toplam * 100 : 0, s = toplam ? 100 - r : 0;
                var ara = r && s ? bosluk : 0;
                randevu.style.strokeDasharray = Math.max(r - ara, 0.01) + ' 100';
                durusma.style.strokeDasharray = Math.max(s - ara, 0.01) + ' 100';
                durusma.style.strokeDashoffset = -r;
                // Değeri sıfır olan dilim yuvarlak ucu yüzünden nokta gibi görünmesin
                randevu.style.opacity = r ? 1 : 0;
                durusma.style.opacity = s ? 1 : 0;
                kart.querySelector('.toplam').textContent = toplam;
                kart.querySelector('[data-alan=randevu]').textContent = d.randevu;
                kart.querySelector('[data-alan=durusma]').textContent = d.durusma;
                kart.querySelector('[data-alan=randevu-oran]').textContent = toplam ? '%' + Math.round(r) : '';
                kart.querySelector('[data-alan=durusma-oran]').textContent = toplam ? '%' + Math.round(s) : '';
                kart.querySelector('#dagilimOzet').textContent = d.etiket + ' ' + d.randevu + ' randevu, ' + d.durusma + ' duruşma.';
                kart.classList.toggle('bos-grafik', !toplam);
                kart.querySelectorAll('[data-donem]').forEach(function (b) {
                    b.setAttribute('aria-selected', b.dataset.donem === anahtar ? 'true' : 'false');
                });
            }

            kart.querySelectorAll('[data-donem]').forEach(function (b) {
                b.addEventListener('click', function () { goster(b.dataset.donem); });
            });
            goster('ay');
        })();
    </script>

@endsection
