@extends('yonetim.layout')

@section('baslik', $randevu->exists ? $randevu->ad_soyad : 'Yeni randevu')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.randevular.index') }}" class="dugme"><i class="fa-regular fa-arrow-left"></i> Listeye dön</a>
@endsection

@section('icerik')

    <div class="izgara form-izgara">
        <form method="post" class="kart cam form"
              action="{{ $randevu->exists ? route('yonetim.randevular.update', $randevu) : route('yonetim.randevular.store') }}">
            @csrf
            @if ($randevu->exists) @method('PUT') @endif

            <header>
                <h2>{{ $randevu->exists ? 'Kaydı düzenle' : 'Randevu bilgileri' }}</h2>
                @if ($randevu->exists)
                    <span class="etiket durum-{{ $randevu->durum }}">{{ $randevu->durumEtiketi() }}</span>
                @endif
            </header>

            <div class="alanlar">
                <div class="alan">
                    <label for="ad_soyad">Ad Soyad</label>
                    <input type="text" id="ad_soyad" name="ad_soyad" value="{{ old('ad_soyad', $randevu->ad_soyad) }}" required>
                </div>
                <div class="alan">
                    <label for="telefon">Telefon</label>
                    <input type="tel" id="telefon" name="telefon" value="{{ old('telefon', $randevu->telefon) }}">
                </div>
                <div class="alan">
                    <label for="eposta">E-posta</label>
                    <input type="email" id="eposta" name="eposta" value="{{ old('eposta', $randevu->eposta) }}">
                </div>
                <div class="alan">
                    <label for="tip">Tür</label>
                    <select id="tip" name="tip">
                        @foreach (\App\Models\Randevu::TIPLER as $deger => $etiket)
                            <option value="{{ $deger }}" @selected(old('tip', $randevu->tip) === $deger)>{{ $etiket }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="alan">
                    <label for="konu">Konu</label>
                    <input type="text" id="konu" name="konu" list="konular" value="{{ old('konu', $randevu->konu) }}">
                    <datalist id="konular">
                        @foreach (\App\Http\Controllers\BasvuruController::konular() as $konu)<option value="{{ $konu }}">@endforeach
                    </datalist>
                </div>
                <div class="alan">
                    <label for="kurum">Kurum / karşı taraf</label>
                    <input type="text" id="kurum" name="kurum" value="{{ old('kurum', $randevu->kurum) }}">
                </div>

                <div class="alan">
                    <label for="tarih">Gün</label>
                    <input type="date" id="tarih" name="tarih" value="{{ old('tarih', $randevu->baslangic?->toDateString()) }}">
                </div>
                <div class="alan">
                    <label for="saat">Saat</label>
                    <input type="time" id="saat" name="saat" step="1800" value="{{ old('saat', $randevu->baslangic?->format('H:i')) }}">
                </div>
                <div class="alan">
                    <label for="sure">Süre (dk)</label>
                    <input type="number" id="sure" name="sure" min="5" max="600" step="5" value="{{ old('sure', $randevu->sure) }}">
                </div>
                <div class="alan">
                    <label for="gorusme_sekli">Görüşme şekli</label>
                    <select id="gorusme_sekli" name="gorusme_sekli">
                        @foreach (\App\Models\Randevu::GORUSME_SEKILLERI as $deger => $etiket)
                            <option value="{{ $deger }}" @selected(old('gorusme_sekli', $randevu->gorusme_sekli) === $deger)>{{ $etiket }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="alan">
                    <label for="durum">Durum</label>
                    <select id="durum" name="durum">
                        @foreach (\App\Models\Randevu::DURUMLAR as $deger => $etiket)
                            <option value="{{ $deger }}" @selected(old('durum', $randevu->durum) === $deger)>{{ $etiket }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="alan tam">
                    <label for="aciklama">Müvekkilin anlattığı durum</label>
                    <textarea id="aciklama" name="aciklama" rows="5">{{ old('aciklama', $randevu->aciklama) }}</textarea>
                </div>
                <div class="alan tam">
                    <label for="notlar">Büro notları <small class="soluk">(müvekkil görmez)</small></label>
                    <textarea id="notlar" name="notlar" rows="4">{{ old('notlar', $randevu->notlar) }}</textarea>
                </div>
            </div>

            <footer>
                <button type="submit" class="dugme dugme-birincil"><i class="fa-regular fa-floppy-disk"></i> Kaydet</button>
            </footer>
        </form>

        @if ($randevu->exists)
            <aside class="kart cam yan">
                <header><h2>Hızlı işlemler</h2></header>
                @include('yonetim.partials.durum-dugmeleri')
                <dl class="bilgi">
                    <dt>Geldiği yer</dt><dd>{{ $randevu->kaynak === 'site' ? 'Web sitesi' : 'Panelden eklendi' }}</dd>
                    <dt>Oluşturuldu</dt><dd>{{ $randevu->created_at->translatedFormat('j F Y H:i') }}</dd>
                    @if ($randevu->telefon)
                        <dt>Ara</dt><dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $randevu->telefon) }}">{{ $randevu->telefon }}</a></dd>
                        <dt>WhatsApp</dt><dd><a href="https://wa.me/{{ preg_replace('/^0/', '90', preg_replace('/\D/', '', $randevu->telefon)) }}" target="_blank" rel="noopener">Mesaj gönder</a></dd>
                    @endif
                    @if ($randevu->eposta)
                        <dt>E-posta</dt><dd><a href="mailto:{{ $randevu->eposta }}">{{ $randevu->eposta }}</a></dd>
                    @endif
                </dl>
                <form method="post" action="{{ route('yonetim.randevular.destroy', $randevu) }}"
                      onsubmit="return confirm('Bu kayıt kalıcı olarak silinsin mi?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="dugme dugme-tehlike genis"><i class="fa-regular fa-trash-can"></i> Kaydı sil</button>
                </form>
            </aside>
        @endif
    </div>

@endsection
