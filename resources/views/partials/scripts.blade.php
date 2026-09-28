{{-- Tema betikleri. Sıralama önemlidir; custom.js en sonda kalmalıdır. --}}
@foreach ([
    'jquery-3.6.3.min.js',
    'jquery-migrate-3.0.0.min.js',
    'modernizr-2.6.2.min.js',
    'imagesloaded.pkgd.min.js',
    'jquery.isotope.v3.0.2.js',
    'popper.min.js',
    'bootstrap.min.js',
    'scrollIt.min.js',
    'jquery.waypoints.min.js',
    'owl.carousel.min.js',
    'jquery.stellar.min.js',
    'jquery.magnific-popup.js',
    'YouTubePopUp.js',
    'smooth-scroll.min.js',
    'custom.js',
    'site.js', // projeye özel hareketler; tema betiklerinden sonra gelmeli
] as $betik)
    <script src="{{ asset('frontend/js/' . $betik) }}"></script>
@endforeach

@stack('betikler')
