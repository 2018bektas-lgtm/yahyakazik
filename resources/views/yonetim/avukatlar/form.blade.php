@extends('yonetim.layout')

@section('baslik', $avukat->exists ? $avukat->ad : 'Yeni avukat')

@section('ust_dugmeler')
    @if ($avukat->exists && $avukat->yayinda)
        <a href="{{ route('avukat', $avukat) }}" target="_blank" rel="noopener" class="dugme"><i class="fa-regular fa-arrow-up-right-from-square"></i> Profili gör</a>
    @endif
    <a href="{{ route('yonetim.avukatlar.index') }}" class="dugme"><i class="fa-regular fa-arrow-left"></i> Avukatlar</a>
@endsection

@php
    $ozgecmis = old('ozgecmis', $avukat->ozgecmis ?: []);
    // Yeni satır eklemek için her zaman bir boş satır bulunur
    $ozgecmis[] = ['donem' => '', 'baslik' => '', 'metin' => ''];
@endphp

@section('icerik')

    <form method="post" enctype="multipart/form-data"
          action="{{ $avukat->exists ? route('yonetim.avukatlar.update', $avukat) : route('yonetim.avukatlar.store') }}">
        @csrf
        @if ($avukat->exists) @method('PUT') @endif

        <div class="izgara form-izgara">
            <div>
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-id-card"></i> Kimlik</h2></header>
                    <div class="alanlar iki">
                        <div class="alan">
                            <label for="ad">Ad soyad</label>
                            <input type="text" id="ad" name="ad" value="{{ old('ad', $avukat->ad) }}" required maxlength="120" placeholder="Av. Ayşe Yılmaz">
                        </div>
                        <div class="alan">
                            <label for="unvan">Unvan</label>
                            <input type="text" id="unvan" name="unvan" value="{{ old('unvan', $avukat->unvan) }}" maxlength="120" placeholder="Avukat, Kıdemli Avukat, Arabulucu…">
                        </div>
                        <div class="alan">
                            <label for="baro">Baro</label>
                            <input type="text" id="baro" name="baro" value="{{ old('baro', $avukat->baro) }}" maxlength="120" placeholder="Ankara Barosu">
                        </div>
                        <div class="alan">
                            <label for="slug">Sayfa adresi <small class="soluk">(boş bırakılırsa addan oluşur)</small></label>
                            <div class="adres-alani">
                                <span class="soluk">/ekibimiz/</span>
                                <input type="text" id="slug" name="slug" value="{{ old('slug', $avukat->slug) }}" maxlength="120" placeholder="ayse-yilmaz">
                            </div>
                        </div>
                        <div class="alan tam">
                            <label for="ozet">Kısa tanıtım <small class="soluk">(ekip kartında görünür, 1-2 cümle)</small></label>
                            <textarea id="ozet" name="ozet" rows="2" maxlength="400">{{ old('ozet', $avukat->ozet) }}</textarea>
                        </div>
                        <div class="alan tam">
                            <label for="biyografi">Biyografi <small class="soluk">(paragrafları boş satırla ayırın)</small></label>
                            <textarea id="biyografi" name="biyografi" rows="8">{{ old('biyografi', $avukat->biyografi) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-timeline"></i> Eğitim ve deneyim</h2></header>
                    <p class="soluk kucuk">Profil sayfasındaki zaman çizelgesi. Sıralama buradaki sıradır; satırı silmek için başlığını ve açıklamasını boşaltın.</p>

                    <div class="ozgecmis-satirlari" id="ozgecmisSatirlari">
                        @foreach ($ozgecmis as $i => $kayit)
                            <div class="ozgecmis-satiri">
                                <input type="text" name="ozgecmis[{{ $i }}][donem]" value="{{ $kayit['donem'] ?? '' }}" maxlength="60" placeholder="Dönem (2017, Eğitim…)" aria-label="Dönem">
                                <input type="text" name="ozgecmis[{{ $i }}][baslik]" value="{{ $kayit['baslik'] ?? '' }}" maxlength="150" placeholder="Başlık" aria-label="Başlık">
                                <textarea name="ozgecmis[{{ $i }}][metin]" rows="2" maxlength="600" placeholder="Kısa açıklama" aria-label="Açıklama">{{ $kayit['metin'] ?? '' }}</textarea>
                                <button type="button" class="ikon-dugme satir-sil" aria-label="Satırı sil" title="Satırı sil"><i class="fa-regular fa-xmark"></i></button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="dugme ust-bosluk" id="ozgecmisEkle"><i class="fa-regular fa-plus"></i> Satır ekle</button>
                </section>
            </div>

            <aside class="yan">
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-image-user"></i> Fotoğraf</h2></header>
                    <div class="avukat-foto buyuk">
                        @if ($avukat->fotograf)
                            <img src="{{ asset($avukat->fotograf) }}" alt="">
                        @else
                            {{ $avukat->ad ? $avukat->basHarfler() : '?' }}
                        @endif
                    </div>
                    <p class="soluk kucuk">Fotoğraf yoksa sitede baş harfler gösterilir. Yalnızca avukatın kendi fotoğrafını yükleyin. Dik çekilmiş (4:5) bir portre en iyi sonucu verir.</p>
                    @if ($avukat->fotograf)
                        <label class="kutu"><input type="checkbox" name="fotograf_kaldir" value="1"> Fotoğrafı kaldır</label>
                    @endif
                    <input type="file" name="fotograf" accept="image/jpeg,image/png,image/webp" class="dosya" aria-label="Fotoğraf yükle">
                </section>

                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-briefcase"></i> Çalıştığı alanlar</h2></header>
                    <label for="uzmanliklar" class="soluk kucuk">Her satıra bir alan yazın. Faaliyet alanlarıyla aynı adı taşıyanlar o sayfaya bağlanır.</label>
                    <textarea id="uzmanliklar" name="uzmanliklar" rows="6" placeholder="İş hukuku&#10;Aile hukuku">{{ old('uzmanliklar', implode("\n", $avukat->uzmanliklar ?? [])) }}</textarea>
                </section>

                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-address-book"></i> İletişim ve görünüm</h2></header>
                    <label for="eposta">E-posta</label>
                    <input type="email" id="eposta" name="eposta" value="{{ old('eposta', $avukat->eposta) }}" maxlength="150">
                    <label for="linkedin" class="ust-bosluk">LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin', $avukat->linkedin) }}" maxlength="255" placeholder="https://linkedin.com/in/…">
                    <label for="sira" class="ust-bosluk">Sıra <small class="soluk">(küçük olan önce)</small></label>
                    <input type="number" id="sira" name="sira" value="{{ old('sira', $avukat->sira) }}" min="0" max="999">
                    <label class="kutu anahtar ust-bosluk">
                        <input type="hidden" name="yayinda" value="0">
                        <input type="checkbox" name="yayinda" value="1" @checked(old('yayinda', $avukat->yayinda ?? true))>
                        <span>Sitede yayında</span>
                    </label>
                </section>

                <button type="submit" class="dugme dugme-birincil genis kaydet"><i class="fa-regular fa-floppy-disk"></i> Kaydet</button>
            </aside>
        </div>
    </form>

    @if ($avukat->exists)
        <form method="post" action="{{ route('yonetim.avukatlar.destroy', $avukat) }}" class="sil-formu"
              onsubmit="return confirm('{{ $avukat->ad }} silinsin mi? Profil sayfası da kaldırılır.')">
            @csrf @method('DELETE')
            <button type="submit" class="dugme dugme-tehlike"><i class="fa-regular fa-trash-can"></i> Avukatı sil</button>
        </form>
    @endif

    <script>
        (function () {
            var kutu = document.getElementById('ozgecmisSatirlari');

            function yenidenNumarala() {
                kutu.querySelectorAll('.ozgecmis-satiri').forEach(function (satir, i) {
                    satir.querySelectorAll('[name]').forEach(function (alan) {
                        alan.name = alan.name.replace(/ozgecmis\[\d+\]/, 'ozgecmis[' + i + ']');
                    });
                });
            }

            document.getElementById('ozgecmisEkle').addEventListener('click', function () {
                var yeni = kutu.lastElementChild.cloneNode(true);
                yeni.querySelectorAll('input, textarea').forEach(function (a) { a.value = ''; });
                kutu.appendChild(yeni);
                yenidenNumarala();
                yeni.querySelector('input').focus();
            });

            kutu.addEventListener('click', function (e) {
                var dugme = e.target.closest('.satir-sil');
                if (!dugme) return;
                var satir = dugme.closest('.ozgecmis-satiri');
                if (kutu.children.length > 1) { satir.remove(); yenidenNumarala(); }
                else { satir.querySelectorAll('input, textarea').forEach(function (a) { a.value = ''; }); }
            });
        })();
    </script>

@endsection
