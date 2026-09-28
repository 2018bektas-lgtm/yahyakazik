<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('baslik', config('site.unvan')) &mdash; {{ config('site.unvan') }}</title>
    <meta name="description" content="@yield('aciklama', config('site.aciklama'))">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('site.unvan') }}">
    <meta property="og:title" content="@yield('baslik', config('site.unvan'))">
    <meta property="og:description" content="@yield('aciklama', config('site.aciklama'))">
    <meta property="og:url" content="{{ url()->current() }}">

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
