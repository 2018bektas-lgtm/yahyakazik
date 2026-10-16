@extends('yonetim.layout')

@section('baslik', $etkinlik->exists ? $etkinlik->baslik : 'Yeni takvim kaydı')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.takvim') }}" class="dugme"><i class="fa-regular fa-arrow-left"></i> Takvime dön</a>
@endsection

@section('icerik')

    <div class="izgara form-izgara">
        <form method="post" class="kart cam form"
              action="{{ $etkinlik->exists ? route('yonetim.etkinlikler.update', $etkinlik) : route('yonetim.etkinlikler.store') }}">
            @csrf
            @if ($etkinlik->exists) @method('PUT') @endif

            <header><h2>Kayıt bilgileri</h2></header>

            <div class="tur-secici" role="radiogroup" aria-label="Tür">
                @foreach (\App\Models\Etkinlik::TURLER as $deger => $etiket)
                    <label class="tur-secenek">
                        <input type="radio" name="tur" value="{{ $deger }}" @checked(old('tur', $etkinlik->tur) === $deger)>
                        <span><i class="nokta tur-{{ $deger }}"></i> {{ $etiket }}</span>
                    </label>
                @endforeach
            </div>
            <p class="soluk kucuk">"Duruşma" ve "Müsait değil" kayıtları sitede müvekkillere gösterilen boş randevu saatlerini kapatır.</p>

            <div class="alanlar dort">
                <div class="alan tam">
                    <label for="baslik">Başlık</label>
                    <input type="text" id="baslik" name="baslik" value="{{ old('baslik', $etkinlik->baslik) }}" required
                           placeholder="Örn. Ahmet Y. işe iade davası duruşması">
                </div>
                <div class="alan">
                    <label for="tarih">Gün</label>
                    <input type="date" id="tarih" name="tarih" value="{{ old('tarih', $etkinlik->baslangic?->toDateString()) }}" required>
                </div>
                <div class="alan">
                    <label for="saat">Saat</label>
                    <input type="time" id="saat" name="saat" value="{{ old('saat', $etkinlik->tum_gun ? '' : $etkinlik->baslangic?->format('H:i')) }}">
                </div>
                <div class="alan">
                    <label for="bitis_tarih">Bitiş günü <small class="soluk">(isteğe bağlı)</small></label>
                    <input type="date" id="bitis_tarih" name="bitis_tarih" value="{{ old('bitis_tarih', $etkinlik->bitis?->toDateString()) }}">
                </div>
                <div class="alan">
                    <label for="bitis_saat">Bitiş saati</label>
                    <input type="time" id="bitis_saat" name="bitis_saat" value="{{ old('bitis_saat', $etkinlik->tum_gun ? '' : $etkinlik->bitis?->format('H:i')) }}">
                </div>
                <div class="alan tam">
                    <label class="kutu"><input type="hidden" name="tum_gun" value="0"><input type="checkbox" name="tum_gun" value="1" id="tum_gun" @checked(old('tum_gun', $etkinlik->tum_gun))> Tüm gün</label>
                </div>
                <div class="alan yarim">
                    <label for="yer">Yer</label>
                    <input type="text" id="yer" name="yer" value="{{ old('yer', $etkinlik->yer) }}" placeholder="Örn. Ankara 12. İş Mahkemesi">
                </div>
                <div class="alan yarim">
                    <label for="dosya_no">Dosya / esas no</label>
                    <input type="text" id="dosya_no" name="dosya_no" value="{{ old('dosya_no', $etkinlik->dosya_no) }}" placeholder="2026/123 E.">
                </div>
                <div class="alan tam">
                    <label for="aciklama">Notlar</label>
                    <textarea id="aciklama" name="aciklama" rows="4">{{ old('aciklama', $etkinlik->aciklama) }}</textarea>
                </div>
                <div class="alan tam">
                    <label class="kutu"><input type="hidden" name="tamamlandi" value="0"><input type="checkbox" name="tamamlandi" value="1" @checked(old('tamamlandi', $etkinlik->tamamlandi))> Tamamlandı</label>
                </div>
            </div>

            <footer>
                <button type="submit" class="dugme dugme-birincil"><i class="fa-regular fa-floppy-disk"></i> Kaydet</button>
            </footer>
        </form>

        @if ($etkinlik->exists)
            <aside class="kart cam yan">
                <header><h2>İşlemler</h2></header>
                <dl class="bilgi">
                    <dt>Tür</dt><dd>{{ $etkinlik->turEtiketi() }}</dd>
                    <dt>Eklendi</dt><dd>{{ $etkinlik->created_at->translatedFormat('j F Y H:i') }}</dd>
                </dl>
                <form method="post" action="{{ route('yonetim.etkinlikler.destroy', $etkinlik) }}"
                      onsubmit="return confirm('Bu takvim kaydı silinsin mi?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="dugme dugme-tehlike genis"><i class="fa-regular fa-trash-can"></i> Sil</button>
                </form>
            </aside>
        @endif
    </div>

    <script>
        (function () {
            var tumGun = document.getElementById('tum_gun');
            function guncelle() {
                ['saat', 'bitis_saat'].forEach(function (id) {
                    var alan = document.getElementById(id);
                    alan.disabled = tumGun.checked;
                    alan.closest('.alan').style.opacity = tumGun.checked ? .4 : 1;
                });
            }
            tumGun.addEventListener('change', guncelle);
            guncelle();
        })();
    </script>

@endsection
