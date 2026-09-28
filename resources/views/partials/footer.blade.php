{{-- Alt bilgi --}}
@php
    // config/site.php içindeki sosyal medya anahtarlarının ikon karşılıkları
    $sosyalIkonlar = [
        'linkedin'  => 'fa-brands fa-linkedin-in',
        'instagram' => 'fa-brands fa-instagram',
        'twitter'   => 'fa-brands fa-x-twitter',
        'facebook'  => 'fa-brands fa-facebook-f',
    ];
    $sosyal = array_filter(config('site.sosyal', []));
@endphp

<footer class="footer">

    {{-- Üst bölüm --}}
    <div class="top">
        <div class="container">
            <div class="row">

                <div class="col-md-4 mb-30">
                    <div class="item">
                        <div class="logo footer-logo">
                            @include('partials.logo', ['konum' => 'footer'])
                        </div>
                        <p>{{ config('site.aciklama') }}</p>

                        @if ($sosyal)
                            <div class="social-icons">
                                <ul class="list-inline">
                                    @foreach ($sosyal as $ag => $baglanti)
                                        @isset($sosyalIkonlar[$ag])
                                            <li>
                                                <a href="{{ $baglanti }}" target="_blank" rel="noopener"
                                                   aria-label="{{ ucfirst($ag) }}">
                                                    <i class="{{ $sosyalIkonlar[$ag] }}"></i>
                                                </a>
                                            </li>
                                        @endisset
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-3 offset-md-1 mb-30">
                    <div class="item">
                        <h3>İletişim</h3>
                        <p>
                            {{ config('site.adres') }}
                            <br>{{ config('site.adres_2') }}
                        </p>
                        <div class="phone mb-0">
                            <a href="tel:{{ config('site.telefon_link') }}">{{ config('site.telefon') }}</a>
                        </div>
                        <div class="mail">
                            <a href="mailto:{{ config('site.eposta') }}">{{ config('site.eposta') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-30">
                    <div class="item">
                        <h3>Çalışma Saatleri</h3>
                        <p>
                            {{ config('site.calisma_saatleri') }}.
                            Randevu almak için bizi arayabilir veya iletişim formunu doldurabilirsiniz.
                        </p>
                        <a href="{{ route('iletisim') }}" class="button-2">Randevu talep edin<span></span></a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Alt bölüm --}}
    <div class="bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="links">
                        <ul>
                            @foreach ([
                                'home'              => 'Ana Sayfa',
                                'hakkimizda'        => 'Hakkımızda',
                                'faaliyet-alanlari' => 'Faaliyet Alanlarımız',
                                'blog'              => 'Makalelerimiz',
                            ] as $rota => $etiket)
                                <li><a href="{{ route($rota) }}">{{ $etiket }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 text-end">
                    <p>&copy; {{ date('Y') }} {{ config('site.unvan') }}. Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </div>

</footer>
