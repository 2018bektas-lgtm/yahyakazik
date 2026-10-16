@extends('yonetim.layout')

@section('baslik', 'Yapılacaklar')

@section('ust_dugmeler')
    <nav class="gun-gezgini" aria-label="Gün seç">
        <a href="{{ route('yonetim.yapilacaklar', ['tarih' => $gun->copy()->subDay()->toDateString()]) }}" class="ikon-dugme" aria-label="Önceki gün"><i class="fa-regular fa-chevron-left"></i></a>
        <form method="get" action="{{ route('yonetim.yapilacaklar') }}">
            <input type="date" name="tarih" value="{{ $gun->toDateString() }}" onchange="this.form.submit()" aria-label="Tarih">
        </form>
        <a href="{{ route('yonetim.yapilacaklar', ['tarih' => $gun->copy()->addDay()->toDateString()]) }}" class="ikon-dugme" aria-label="Sonraki gün"><i class="fa-regular fa-chevron-right"></i></a>
        @unless ($gun->isToday())
            <a href="{{ route('yonetim.yapilacaklar') }}" class="dugme">Bugün</a>
        @endunless
    </nav>
@endsection

@section('icerik')

    <div class="izgara form-izgara yapilacaklar">
        <section class="kart cam">
            <header>
                <div>
                    <h2>{{ $gun->translatedFormat('j F Y, l') }}</h2>
                    <p class="soluk kucuk ilerleme-metni">
                        @if ($toplam)
                            {{ $toplam }} işten {{ $biten }} tanesi tamamlandı
                        @else
                            Bu gün için kayıt yok
                        @endif
                    </p>
                </div>
                @if ($toplam)
                    <div class="halka" style="--oran: {{ round($biten / $toplam * 100) }}" aria-hidden="true"><span>{{ round($biten / $toplam * 100) }}%</span></div>
                @endif
            </header>

            @if ($gunBoyu->isNotEmpty())
                <div class="saat-dilimi gun-boyu">
                    <div class="saat-etiketi">Gün boyu</div>
                    <div class="saat-icerik">
                        @foreach ($gunBoyu as $kalem)
                            @include('yonetim.partials.yapilacak', ['kalem' => $kalem])
                        @endforeach
                    </div>
                </div>
            @endif

            @foreach ($saatler as $saat => $liste)
                @php $saatMetni = sprintf('%02d:00', $saat); @endphp
                <div class="saat-dilimi {{ $simdiSaat === $saat ? 'simdi' : '' }} {{ $simdiSaat !== null && $saat < $simdiSaat ? 'gecmis' : '' }} {{ $liste->isEmpty() ? 'bos-dilim' : '' }}">
                    <div class="saat-etiketi">{{ $saatMetni }}</div>
                    <div class="saat-icerik">
                        @foreach ($liste as $kalem)
                            @include('yonetim.partials.yapilacak', ['kalem' => $kalem])
                        @endforeach
                        @if ($eklenebilir && ($simdiSaat === null || $saat >= $simdiSaat))
                        @php $dilimSaati = $enErkenSaat !== null && $saat === $simdiSaat ? $enErkenSaat : $saatMetni; @endphp
                        <a href="{{ route('yonetim.yapilacaklar', ['tarih' => $gun->toDateString(), 'saat' => $dilimSaati]) }}#gorevEkle"
                           class="dilime-ekle" data-saat="{{ $dilimSaati }}" aria-label="{{ $saatMetni }} için görev ekle">
                            <i class="fa-regular fa-plus"></i> <span>Görev ekle</span>
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </section>

        <aside class="yan">
            @if ($eklenebilir)
            <form method="post" action="{{ route('yonetim.gorevler.store') }}" class="kart cam form" id="gorevEkle">
                @csrf
                <header><h2>Görev ekle</h2></header>
                <input type="hidden" name="tarih" value="{{ $gun->toDateString() }}">
                <label for="gorevBaslik">Ne yapılacak?</label>
                <input type="text" id="gorevBaslik" name="baslik" value="{{ old('baslik') }}" required maxlength="255"
                       placeholder="Örn. Bilirkişi raporuna itiraz yaz">
                <label for="gorevSaat" class="ust-bosluk">Saat <small class="soluk">(boş bırakırsan gün boyu)</small></label>
                <input type="time" id="gorevSaat" name="saat" value="{{ old('saat', request('saat')) }}" @if ($enErkenSaat) min="{{ $enErkenSaat }}" @endif>
                <label for="gorevAciklama" class="ust-bosluk">Not <small class="soluk">(isteğe bağlı)</small></label>
                <textarea id="gorevAciklama" name="aciklama" rows="3" maxlength="2000">{{ old('aciklama') }}</textarea>
                <footer><button type="submit" class="dugme dugme-birincil genis"><i class="fa-solid fa-plus"></i> Listeye ekle</button></footer>
            </form>
            @else
            <div class="kart cam kucuk soluk aciklama-kart">
                <p><i class="fa-regular fa-clock-rotate-left"></i> Geçmiş bir gün; bu güne yeni görev eklenemez.</p>
            </div>
            @endif

            <div class="kart cam kucuk soluk aciklama-kart">
                <p><i class="fa-regular fa-circle-info"></i> Saati geçen randevular kendiliğinden <b>Tamamlandı</b> olur. Yanlışlıkla işaretlenirse onay kutusuna tekrar tıklayarak geri alabilirsin.</p>
            </div>
        </aside>
    </div>

    <script>
        // Bir saat diliminin "Görev ekle" bağlantısı sayfayı yenilemeden formu doldurur
        document.querySelectorAll('.dilime-ekle').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var saat = document.getElementById('gorevSaat');
                if (!saat) return;
                saat.value = a.dataset.saat;
                var baslik = document.getElementById('gorevBaslik');
                baslik.scrollIntoView({ behavior: 'smooth', block: 'center' });
                baslik.focus({ preventScroll: true });
            });
        });

        // "Alt başlık ekle" açılınca yazma alanına odaklan
        document.querySelectorAll('.alt-ekle').forEach(function (d) {
            d.addEventListener('toggle', function () {
                if (d.open) d.querySelector('input').focus();
            });
        });
    </script>

@endsection
