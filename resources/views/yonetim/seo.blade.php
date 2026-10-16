@extends('yonetim.layout')

@section('baslik', 'SEO ayarları')

@section('ust_dugmeler')
    <a href="{{ route('sitemap') }}" target="_blank" rel="noopener" class="dugme"><i class="fa-regular fa-sitemap"></i> sitemap.xml</a>
@endsection

@section('icerik')

    <form method="post" action="{{ route('yonetim.seo') }}" enctype="multipart/form-data" class="ayarlar">
        @csrf @method('PUT')

        <div class="izgara form-izgara">
            <div>
                {{-- Genel SEO --}}
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-magnifying-glass"></i> Arama motoru görünümü</h2></header>

                    <div class="alanlar">
                        <div class="alan tam">
                            <label for="siteAdi">Site adı <small class="soluk">(her sayfa başlığının sonuna eklenir)</small></label>
                            <input type="text" id="siteAdi" name="seo[site_adi]" maxlength="120" data-sayac="60"
                                   value="{{ old('seo.site_adi', $ayar->get('seo.site_adi')) }}" placeholder="{{ config('site.unvan') }}">
                        </div>
                        <div class="alan tam">
                            <label for="siteAciklama">Varsayılan açıklama <small class="soluk">(kendi açıklaması olmayan sayfalarda kullanılır)</small></label>
                            <textarea id="siteAciklama" name="seo[aciklama]" rows="3" maxlength="320" data-sayac="160"
                                      placeholder="{{ trim(preg_replace('/\s+/', ' ', config('site.aciklama'))) }}">{{ old('seo.aciklama', $ayar->get('seo.aciklama')) }}</textarea>
                        </div>
                        <div class="alan tam">
                            <label for="anahtarKelimeler">Anahtar kelimeler <small class="soluk">(virgülle ayırın)</small></label>
                            <input type="text" id="anahtarKelimeler" name="seo[anahtar_kelimeler]" maxlength="500"
                                   value="{{ old('seo.anahtar_kelimeler', $ayar->get('seo.anahtar_kelimeler')) }}"
                                   placeholder="ankara avukat, iş davası avukatı, arabulucu ankara">
                        </div>
                    </div>

                    <div class="google-onizleme" aria-hidden="true">
                        <small class="soluk">Google'da ana sayfa böyle görünür</small>
                        <div class="onizleme-kutu">
                            <span class="onizleme-adres">{{ preg_replace('#^https?://#', '', url('/')) }}</span>
                            <span class="onizleme-baslik" id="onizlemeBaslik"></span>
                            <span class="onizleme-aciklama" id="onizlemeAciklama"></span>
                        </div>
                    </div>
                </section>

                {{-- Sayfa bazlı --}}
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-file-lines"></i> Sayfa başlıkları ve açıklamaları</h2></header>
                    <p class="soluk kucuk">Boş bıraktığınız alanlarda sayfanın kendi başlığı ve açıklaması kullanılır. Başlık için 60, açıklama için 160 karakteri geçmemeye çalışın; Google fazlasını keser.</p>

                    @foreach ($sayfalar as $rota => $etiket)
                        <details class="sayfa-seo" @if ($errors->has("sayfa.{$rota}.*") || $ayar->get("sayfa.{$rota}.baslik") || $ayar->get("sayfa.{$rota}.aciklama")) open @endif>
                            <summary>
                                <span>{{ $etiket }}</span>
                                <small class="soluk">{{ preg_replace('#^https?://[^/]+#', '', route($rota)) ?: '/' }}</small>
                                @if ($ayar->get("sayfa.{$rota}.baslik") || $ayar->get("sayfa.{$rota}.aciklama"))
                                    <span class="etiket durum-onaylandi">Özel</span>
                                @endif
                            </summary>
                            <div class="alanlar">
                                <div class="alan tam">
                                    <label for="baslik-{{ $rota }}">Başlık</label>
                                    <input type="text" id="baslik-{{ $rota }}" name="sayfa[{{ $rota }}][baslik]" maxlength="120" data-sayac="60"
                                           value="{{ old("sayfa.{$rota}.baslik", $ayar->get("sayfa.{$rota}.baslik")) }}"
                                           placeholder="Sayfanın kendi başlığı kullanılır">
                                </div>
                                <div class="alan tam">
                                    <label for="aciklama-{{ $rota }}">Açıklama</label>
                                    <textarea id="aciklama-{{ $rota }}" name="sayfa[{{ $rota }}][aciklama]" rows="2" maxlength="320" data-sayac="160"
                                              placeholder="Sayfanın kendi açıklaması kullanılır">{{ old("sayfa.{$rota}.aciklama", $ayar->get("sayfa.{$rota}.aciklama")) }}</textarea>
                                </div>
                            </div>
                        </details>
                    @endforeach
                </section>
            </div>

            <aside class="yan">
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-robot"></i> Arama motorları</h2></header>

                    <label class="kutu anahtar">
                        <input type="hidden" name="seo[indeksle]" value="0">
                        <input type="checkbox" name="seo[indeksle]" value="1" @checked(old('seo.indeksle', $ayar->acikMi('seo.indeksle') ? '1' : '0') === '1')>
                        <span>Site arama motorlarında görünsün</span>
                    </label>
                    <p class="soluk kucuk">Kapatırsanız sayfalara "noindex" eklenir; site yayına hazır değilken işe yarar.</p>

                    <label for="googleDogrulama" class="ust-bosluk">Google Search Console doğrulama kodu</label>
                    <input type="text" id="googleDogrulama" name="seo[google_dogrulama]" maxlength="120"
                           value="{{ old('seo.google_dogrulama', $ayar->get('seo.google_dogrulama')) }}" placeholder="content=&quot;…&quot; içindeki kod">

                    <label for="bingDogrulama" class="ust-bosluk">Bing doğrulama kodu</label>
                    <input type="text" id="bingDogrulama" name="seo[bing_dogrulama]" maxlength="120"
                           value="{{ old('seo.bing_dogrulama', $ayar->get('seo.bing_dogrulama')) }}">

                    <label for="analytics" class="ust-bosluk">Google Analytics ölçüm kimliği</label>
                    <input type="text" id="analytics" name="seo[analytics_id]" maxlength="30"
                           value="{{ old('seo.analytics_id', $ayar->get('seo.analytics_id')) }}" placeholder="G-XXXXXXXXXX">

                    <p class="soluk kucuk ust-bosluk">Site haritası adresi (Search Console'a ekleyin):<br>
                        <a href="{{ route('sitemap') }}" target="_blank" rel="noopener" class="vurgu-link">{{ route('sitemap') }}</a></p>
                </section>

                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-image"></i> Paylaşım görseli</h2></header>
                    <p class="soluk kucuk">Site WhatsApp, LinkedIn ya da Facebook'ta paylaşıldığında görünen görsel. Önerilen boyut 1200×630.</p>
                    @if ($ayar->get('seo.og_gorsel'))
                        <img src="{{ asset($ayar->get('seo.og_gorsel')) }}" alt="Şu anki paylaşım görseli" class="og-onizleme">
                        <label class="kutu"><input type="checkbox" name="og_gorsel_kaldir" value="1"> Görseli kaldır</label>
                    @endif
                    <input type="file" name="og_gorsel" accept="image/jpeg,image/png,image/webp" class="dosya" aria-label="Paylaşım görseli yükle">
                </section>

                <button type="submit" class="dugme dugme-birincil genis kaydet"><i class="fa-regular fa-floppy-disk"></i> SEO ayarlarını kaydet</button>
            </aside>
        </div>
    </form>

    <script>
        (function () {
            // Karakter sayaçları
            document.querySelectorAll('[data-sayac]').forEach(function (alan) {
                var sinir = +alan.dataset.sayac;
                var sayac = document.createElement('small');
                sayac.className = 'karakter-sayaci';
                alan.insertAdjacentElement('afterend', sayac);
                function guncelle() {
                    var uzunluk = (alan.value || alan.placeholder).length;
                    sayac.textContent = uzunluk + ' / ' + sinir;
                    sayac.classList.toggle('asti', uzunluk > sinir);
                }
                alan.addEventListener('input', guncelle);
                guncelle();
            });

            // Google önizlemesi (ana sayfa)
            var siteAdi = document.getElementById('siteAdi');
            var anaBaslik = document.getElementById('baslik-home');
            var anaAciklama = document.getElementById('aciklama-home');
            var siteAciklama = document.getElementById('siteAciklama');
            function deger(a) { return a.value.trim() || a.placeholder; }
            function onizle() {
                var baslik = (anaBaslik.value.trim() || @json(config('site.slogan'))) + ' — ' + deger(siteAdi);
                var aciklama = anaAciklama.value.trim() || deger(siteAciklama);
                document.getElementById('onizlemeBaslik').textContent = baslik.length > 62 ? baslik.slice(0, 60) + '…' : baslik;
                document.getElementById('onizlemeAciklama').textContent = aciklama.length > 162 ? aciklama.slice(0, 160) + '…' : aciklama;
            }
            [siteAdi, anaBaslik, anaAciklama, siteAciklama].forEach(function (a) { a.addEventListener('input', onizle); });
            onizle();
        })();
    </script>

@endsection
