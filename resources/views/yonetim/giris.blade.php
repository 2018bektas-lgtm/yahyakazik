<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Giriş · {{ config('site.kisa_unvan') }} Yönetim</title>
    <link rel="icon" href="{{ asset('frontend/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Jost:wght@300..700&family=Playfair+Display:wght@500;600&display=swap">
    <link rel="stylesheet" href="{{ asset('frontend/css/plugins/font-awesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/yonetim/yonetim.css') }}">
</head>
<body class="giris-sayfasi">
    <div class="arkaplan" aria-hidden="true"><span></span><span></span><span></span></div>

    <form method="post" action="{{ route('yonetim.giris') }}" class="giris cam">
        @csrf
        <span class="marka-ikon buyuk"><i class="fa-solid fa-scale-balanced"></i></span>
        <h1>{{ config('site.kisa_unvan') }}</h1>
        <p class="soluk">Yönetim paneline giriş</p>

        @if ($errors->giris->any())
            <div class="bildirim hata" role="alert">{{ $errors->giris->first() }}</div>
        @endif

        <label for="email">E-posta</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username">

        <label for="password">Şifre</label>
        <input type="password" name="password" id="password" required autocomplete="current-password">

        <label class="kutu"><input type="checkbox" name="hatirla" value="1"> Beni hatırla</label>

        <button type="submit" class="dugme dugme-birincil genis">Giriş yap</button>
        <a href="{{ route('home') }}" class="soluk kucuk geri-don">&larr; Siteye dön</a>
    </form>
</body>
</html>
