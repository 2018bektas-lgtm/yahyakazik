@extends('yonetim.layout')

@section('baslik', 'Site ayarları')

@section('ust_dugmeler')
    <a href="{{ route('yonetim.seo') }}" class="dugme"><i class="fa-regular fa-magnifying-glass-chart"></i> SEO ayarları</a>
@endsection

@php
    // Boş alanlarda config/site.php içindeki asıl değer yer tutucu olarak görünür
    $varsayilan = fn (string $alan) => \Illuminate\Support\Arr::get(require config_path('site.php'), $alan);
    $alan = fn (string $ad) => old("site.{$ad}", $ayar->get("site.{$ad}"));
@endphp

@section('icerik')

    <form method="post" action="{{ route('yonetim.ayarlar') }}" class="ayarlar">
        @csrf @method('PUT')

        <div class="izgara form-izgara">
            <div>
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-building-columns"></i> Büro bilgileri</h2></header>
                    <p class="soluk kucuk">Boş bıraktığınız alanlarda soluk yazılı mevcut değer kullanılır. Değişiklikler sitenin her yerinde (üst menü, alt bilgi, iletişim sayfası) güncellenir.</p>

                    <div class="alanlar iki">
                        <div class="alan tam">
                            <label for="unvan">Büro unvanı</label>
                            <input type="text" id="unvan" name="site[unvan]" maxlength="150" value="{{ $alan('unvan') }}" placeholder="{{ $varsayilan('unvan') }}">
                        </div>
                        <div class="alan">
                            <label for="kisaUnvan">Kısa ad</label>
                            <input type="text" id="kisaUnvan" name="site[kisa_unvan]" maxlength="60" value="{{ $alan('kisa_unvan') }}" placeholder="{{ $varsayilan('kisa_unvan') }}">
                        </div>
                        <div class="alan">
                            <label for="avukatAd">Avukat adı</label>
                            <input type="text" id="avukatAd" name="site[ad]" maxlength="80" value="{{ $alan('ad') }}" placeholder="{{ $varsayilan('ad') }}">
                        </div>
                        <div class="alan tam">
                            <label for="slogan">Slogan</label>
                            <input type="text" id="slogan" name="site[slogan]" maxlength="150" value="{{ $alan('slogan') }}" placeholder="{{ $varsayilan('slogan') }}">
                        </div>
                    </div>
                </section>

                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-address-card"></i> İletişim</h2></header>

                    <div class="alanlar iki">
                        <div class="alan">
                            <label for="telefon">Telefon</label>
                            <input type="tel" id="telefon" name="site[telefon]" maxlength="30" value="{{ $alan('telefon') }}" placeholder="{{ $varsayilan('telefon') }}">
                        </div>
                        <div class="alan">
                            <label for="whatsapp">WhatsApp numarası</label>
                            <input type="tel" id="whatsapp" name="site[whatsapp]" maxlength="30" value="{{ $alan('whatsapp') }}" placeholder="+{{ $varsayilan('whatsapp') }}">
                        </div>
                        <div class="alan tam">
                            <label for="eposta">E-posta</label>
                            <input type="email" id="eposta" name="site[eposta]" maxlength="150" value="{{ $alan('eposta') }}" placeholder="{{ $varsayilan('eposta') }}">
                        </div>
                        <div class="alan">
                            <label for="adres">Adres</label>
                            <input type="text" id="adres" name="site[adres]" maxlength="200" value="{{ $alan('adres') }}" placeholder="{{ $varsayilan('adres') }}">
                        </div>
                        <div class="alan">
                            <label for="adres2">İlçe / il</label>
                            <input type="text" id="adres2" name="site[adres_2]" maxlength="120" value="{{ $alan('adres_2') }}" placeholder="{{ $varsayilan('adres_2') }}">
                        </div>
                        <div class="alan tam">
                            <label for="calisma">Çalışma saatleri</label>
                            <input type="text" id="calisma" name="site[calisma_saatleri]" maxlength="120" value="{{ $alan('calisma_saatleri') }}" placeholder="{{ $varsayilan('calisma_saatleri') }}">
                        </div>
                    </div>
                </section>
            </div>

            <aside class="yan">
                <section class="kart cam form">
                    <header><h2><i class="fa-regular fa-share-nodes"></i> Sosyal medya</h2></header>
                    <p class="soluk kucuk">Boş bırakılan hesabın ikonu sitede gösterilmez.</p>

                    @foreach (['linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'facebook' => 'Facebook'] as $hesap => $etiket)
                        <label for="sosyal-{{ $hesap }}" class="{{ $loop->first ? '' : 'ust-bosluk' }}"><i class="fa-brands fa-{{ $hesap === 'twitter' ? 'x-twitter' : $hesap }}"></i> {{ $etiket }}</label>
                        <input type="url" id="sosyal-{{ $hesap }}" name="site[sosyal][{{ $hesap }}]" maxlength="255"
                               value="{{ $alan("sosyal.{$hesap}") }}" placeholder="https://{{ $hesap === 'twitter' ? 'x' : $hesap }}.com/…">
                    @endforeach
                </section>

                <div class="kart cam kucuk soluk aciklama-kart">
                    <p><i class="fa-regular fa-circle-info"></i> Sayfa başlıkları, Google açıklamaları ve doğrulama kodları <a href="{{ route('yonetim.seo') }}" class="vurgu-link">SEO ayarları</a> sayfasında.</p>
                </div>

                <button type="submit" class="dugme dugme-birincil genis kaydet"><i class="fa-regular fa-floppy-disk"></i> Ayarları kaydet</button>
            </aside>
        </div>
    </form>

@endsection
