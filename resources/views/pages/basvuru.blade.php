@extends('layouts.app')

@section('baslik', 'Başvuru ve Randevu')
@section('aciklama', 'Hukuki sorununuzu birkaç adımda anlatın ya da uygun gün ve saati seçerek doğrudan randevu alın.')

@push('stiller')
    <link rel="stylesheet" href="{{ asset('frontend/css/basvuru.css') }}">
@endpush

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Müvekkil başvurusu',
        'baslik'    => 'Başvuru ve',
        'vurgu'     => 'Randevu',
    ])

    <section class="section-padding basvuru">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    {{--
                        Adımlar basvuru.js ile tek tek gösterilir. JavaScript kapalıysa
                        tüm adımlar alt alta görünür ve form yine çalışır.
                    --}}
                    <form method="post" action="{{ route('basvuru.kaydet') }}" class="sihirbaz" id="sihirbaz"
                          data-saatler="{{ route('basvuru.saatler') }}" novalidate>
                        @csrf

                        <div class="gizli-alan" aria-hidden="true">
                            <label for="web_sitesi">Web siteniz</label>
                            <input type="text" name="web_sitesi" id="web_sitesi" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="ilerleme" aria-hidden="true"><span></span></div>

                        @if ($errors->any())
                            <div class="uyari" role="alert">
                                <strong>Lütfen şunları kontrol edin:</strong>
                                <ul>@foreach ($errors->all() as $hata)<li>{{ $hata }}</li>@endforeach</ul>
                            </div>
                        @endif

                        {{-- 1. Ne yapmak istiyor? --}}
                        <fieldset class="adim" data-adim="tip">
                            <legend><small>Adım <b class="adim-no">1</b></small> Size nasıl yardımcı olalım?</legend>
                            <div class="secenekler iki">
                                <label class="kart-secenek">
                                    <input type="radio" name="tip" value="basvuru" @checked(old('tip') === 'basvuru')>
                                    <span>
                                        <i class="fa-regular fa-file-lines"></i>
                                        <strong>Durumumu anlatmak istiyorum</strong>
                                        <em>Birkaç soruyla konunuzu yazın, dosyanızı inceleyip size dönelim.</em>
                                    </span>
                                </label>
                                <label class="kart-secenek">
                                    <input type="radio" name="tip" value="randevu" @checked(old('tip') === 'randevu')>
                                    <span>
                                        <i class="fa-regular fa-calendar-check"></i>
                                        <strong>Doğrudan randevu almak istiyorum</strong>
                                        <em>Uygun gün ve saati seçin, görüşmeyi hemen planlayalım.</em>
                                    </span>
                                </label>
                            </div>
                        </fieldset>

                        {{-- 2. Konu --}}
                        <fieldset class="adim" data-adim="konu">
                            <legend><small>Adım <b class="adim-no">2</b></small> Konunuz hangi alanla ilgili?</legend>
                            <p class="ipucu">Emin değilseniz "Diğer"i seçebilirsiniz.</p>
                            <div class="secenekler cipler">
                                @foreach ($konular as $konu)
                                    <label class="cip">
                                        <input type="radio" name="konu" value="{{ $konu }}" @checked(old('konu', request('konu')) === $konu)>
                                        <span>{{ $konu }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- 3. Kurum (yalnızca başvuru) --}}
                        <fieldset class="adim" data-adim="kurum" data-yol="basvuru">
                            <legend><small>Adım <b class="adim-no">3</b></small> Uyuşmazlık kiminle?</legend>
                            <p class="ipucu">İşvereniniz, bir kamu kurumu, şirket ya da kişi olabilir. Bilmiyorsanız boş bırakın.</p>
                            <label for="kurum" class="visually-hidden">Kurum veya kişi</label>
                            <input type="text" name="kurum" id="kurum" value="{{ old('kurum') }}" maxlength="255"
                                   placeholder="Örn. XYZ Lojistik A.Ş., SGK, Millî Savunma Bakanlığı">
                        </fieldset>

                        {{-- 4. Genel durum (yalnızca başvuru) --}}
                        <fieldset class="adim" data-adim="aciklama" data-yol="basvuru">
                            <legend><small>Adım <b class="adim-no">4</b></small> Kısaca ne oldu?</legend>
                            <p class="ipucu">Ne zaman başladı, şu an hangi aşamada, sizden ne talep ediliyor ya da siz ne talep ediyorsunuz? Kimlik numarası gibi hassas bilgileri yazmanıza gerek yok.</p>
                            <label for="aciklama" class="visually-hidden">Genel durum</label>
                            <textarea name="aciklama" id="aciklama" rows="7" maxlength="5000" data-en-az="20"
                                      placeholder="Örn. 6 yıldır çalıştığım işyerinden geçen ay haklı bir sebep gösterilmeden çıkarıldım, kıdem tazminatım ödenmedi...">{{ old('aciklama') }}</textarea>
                            <div class="sayac"><span id="aciklamaSayac">0</span> / 5000</div>
                        </fieldset>

                        {{-- 5. Gün ve saat --}}
                        <fieldset class="adim" data-adim="zaman">
                            <legend><small>Adım <b class="adim-no">5</b></small> <span data-yol-metin="basvuru">Görüşme için bir saat ayıralım mı?</span><span data-yol-metin="randevu">Size uygun gün ve saati seçin</span></legend>
                            <p class="ipucu" data-yol-metin="basvuru">İsterseniz şimdi bir gün ve saat seçin, istemezseniz bu adımı geçin; başvurunuzu inceledikten sonra sizi arayalım.</p>
                            <p class="ipucu" data-yol-metin="randevu">Görüşmeler {{ config('randevu.sure') }} dakikadır. Gösterilen saatlerin hepsi boştur.</p>

                            <div class="gunler" role="radiogroup" aria-label="Gün seçin">
                                @foreach ($gunler as $gun)
                                    <label class="gun">
                                        <input type="radio" name="tarih" value="{{ $gun->toDateString() }}" @checked(old('tarih') === $gun->toDateString())>
                                        <span>
                                            <small>{{ $gun->translatedFormat('D') }}</small>
                                            <b>{{ $gun->format('j') }}</b>
                                            <small>{{ $gun->translatedFormat('M') }}</small>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="saatler" id="saatler" aria-live="polite" data-eski="{{ old('saat') }}">
                                <p class="ipucu">Önce bir gün seçin.</p>
                            </div>
                            <noscript>
                                <label for="saat">Saat (ss:dd)</label>
                                <input type="text" name="saat" id="saat" value="{{ old('saat') }}" placeholder="10:30">
                            </noscript>

                            <button type="button" class="temizle" data-temizle-zaman data-yol="basvuru">Şimdilik randevu istemiyorum</button>
                        </fieldset>

                        {{-- 6. İletişim --}}
                        <fieldset class="adim" data-adim="iletisim">
                            <legend><small>Adım <b class="adim-no">6</b></small> Size nasıl ulaşalım?</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="ad_soyad">Ad Soyad</label>
                                    <input type="text" name="ad_soyad" id="ad_soyad" value="{{ old('ad_soyad') }}" required maxlength="120" autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefon">Telefon</label>
                                    <input type="tel" name="telefon" id="telefon" value="{{ old('telefon') }}" required maxlength="30" autocomplete="tel" placeholder="05xx xxx xx xx">
                                </div>
                                <div class="col-md-6">
                                    <label for="eposta">E-posta <small>(isteğe bağlı)</small></label>
                                    <input type="email" name="eposta" id="eposta" value="{{ old('eposta') }}" maxlength="255" autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="gorusme_sekli">Görüşme şekli</label>
                                    <select name="gorusme_sekli" id="gorusme_sekli">
                                        @foreach (\App\Models\Randevu::GORUSME_SEKILLERI as $deger => $etiket)
                                            <option value="{{ $deger }}" @selected(old('gorusme_sekli', 'yuz_yuze') === $deger)>{{ $etiket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label class="onay">
                                <input type="checkbox" name="kvkk" value="1" @checked(old('kvkk')) required>
                                <span>Paylaştığım bilgilerin yalnızca başvurumun değerlendirilmesi ve benimle iletişim kurulması amacıyla işlenmesini kabul ediyorum.</span>
                            </label>

                            <div class="ozet" id="ozet" hidden></div>
                        </fieldset>

                        <div class="sihirbaz-alt">
                            <button type="button" class="geri" id="geri">&larr; Geri</button>
                            <button type="button" class="button-4 ileri" id="ileri"><span>Devam</span></button>
                            <button type="submit" class="button-4 gonder" id="gonder"><span>Gönder</span></button>
                        </div>
                    </form>

                    <p class="alt-not">
                        Acil bir durumunuz mu var? <a href="tel:{{ config('site.telefon_link') }}">{{ config('site.telefon') }}</a> numarasından bize hemen ulaşabilirsiniz.
                    </p>

                </div>
            </div>
        </div>
    </section>

@endsection

@push('betikler')
    <script src="{{ asset('frontend/js/basvuru.js') }}"></script>
@endpush
