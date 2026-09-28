{{--
    Ana şablon. Tüm sayfalar bu dosyayı genişletir.

    Kullanımı:
        @extends('layouts.app')
        @section('baslik', 'Sayfa başlığı')
        @section('icerik') ... @endsection
--}}
<!DOCTYPE html>
<html lang="tr">
@include('partials.head')

<body>

    @include('partials.preloader')

    @include('partials.navbar')

    @yield('icerik')

    @include('partials.footer')

    @include('partials.mobil-iletisim')

    @include('partials.scripts')

</body>
</html>
