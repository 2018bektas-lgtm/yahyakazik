{{--
    Sayfa başlığı ve açıklaması: yönetim panelinde (Ayarlar > SEO) bu sayfa
    için yazılmış değer varsa o, yoksa sayfanın kendi @section değeri kullanılır.
--}}
@php
    $seo = app(\App\Support\SiteAyarlari::class);
    $rota = request()->route()?->getName();
    $siteAdi = $seo->get('seo.site_adi', config('site.unvan'));

    // @section('baslik', '...') değerleri kaçışlı gelir; bir kez çözüp aşağıda yeniden kaçışlıyoruz
    $sayfaBasligi = $seo->get("sayfa.{$rota}.baslik")
        ?? html_entity_decode($__env->yieldContent('baslik', $siteAdi), ENT_QUOTES);
    $sayfaAciklamasi = $seo->get("sayfa.{$rota}.aciklama")
        ?? html_entity_decode($__env->yieldContent('aciklama', $seo->get('seo.aciklama', config('site.aciklama'))), ENT_QUOTES);
    $sayfaAciklamasi = trim(preg_replace('/\s+/', ' ', $sayfaAciklamasi));

    $paylasimGorseli = $seo->get('seo.og_gorsel');
    $analytics = $seo->get('seo.analytics_id');
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $sayfaBasligi }} &mdash; {{ $siteAdi }}</title>
    <meta name="description" content="{{ $sayfaAciklamasi }}">
    @if ($seo->get('seo.anahtar_kelimeler'))
        <meta name="keywords" content="{{ $seo->get('seo.anahtar_kelimeler') }}">
    @endif
    <meta name="robots" content="{{ $seo->acikMi('seo.indeksle') ? 'index, follow' : 'noindex, nofollow' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    @if ($seo->get('seo.google_dogrulama'))
        <meta name="google-site-verification" content="{{ $seo->get('seo.google_dogrulama') }}">
    @endif
    @if ($seo->get('seo.bing_dogrulama'))
        <meta name="msvalidate.01" content="{{ $seo->get('seo.bing_dogrulama') }}">
    @endif

    <meta property="og:type" content="website">
    <meta property="og:locale" content="tr_TR">
    <meta property="og:site_name" content="{{ $siteAdi }}">
    <meta property="og:title" content="{{ $sayfaBasligi }}">
    <meta property="og:description" content="{{ $sayfaAciklamasi }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($paylasimGorseli)
        <meta property="og:image" content="{{ asset($paylasimGorseli) }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    @if ($analytics)
        {{-- Google Analytics (ölçüm kimliği SEO ayarlarından) --}}
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($analytics));
        </script>
    @endif

    <link rel="icon" href="{{ asset('frontend/img/favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap">

    <link rel="stylesheet" href="{{ asset('frontend/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    {{-- Projeye özel düzenlemeler; tema stillerinden sonra yüklenmeli --}}
    <link rel="stylesheet" href="{{ asset('frontend/css/site.css') }}">

    {{-- Arama motorları için yapılandırılmış veri (Google'da zengin sonuç) --}}
    @php
        $yapilandirilmisVeri = [
            '@context'      => 'https://schema.org',
            '@type'         => 'LegalService',
            'name'          => config('site.unvan'),
            'alternateName' => config('site.kisa_unvan'),
            'description'   => trim(preg_replace('/\s+/', ' ', config('site.aciklama'))),
            'url'           => config('app.url'),
            'telephone'     => config('site.telefon'),
            'email'         => config('site.eposta'),
            'address'       => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => config('site.adres'),
                'addressLocality' => 'Çankaya',
                'addressRegion'   => 'Ankara',
                'addressCountry'  => 'TR',
            ],
            'areaServed'    => ['@type' => 'Country', 'name' => 'Türkiye'],
            'founder'       => [
                '@type'    => 'Person',
                'name'     => config('site.ad'),
                'jobTitle' => 'Avukat ve Arabulucu',
            ],
            'knowsAbout'    => array_column(config('icerik.faaliyet_alanlari'), 'ad'),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($yapilandirilmisVeri, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    @stack('stiller')
</head>
