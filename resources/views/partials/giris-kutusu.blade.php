{{--
    Üst menüdeki küçük yönetici girişi kutusu.

    Simgeye tıklanınca e-posta ve şifre isteyen bir kutu açılır; form
    yönetim panelinin giriş adresine gönderilir. Giriş hatalıysa kullanıcı
    aynı sayfaya döner ve kutu hata mesajıyla açık gelir. Giriş yapılmışsa
    kutu panele ve çıkışa bağlantı gösterir.
--}}
@php $hatalar = $errors->getBag('giris'); @endphp

<div class="giris-kutusu {{ $hatalar->any() ? 'acik' : '' }}" id="girisKutusu">
    <button type="button" class="giris-dugmesi" id="girisDugmesi"
            aria-expanded="{{ $hatalar->any() ? 'true' : 'false' }}" aria-controls="girisPaneli"
            aria-label="{{ auth()->check() ? 'Yönetim paneli' : 'Yönetici girişi' }}">
        <i class="fa-regular {{ auth()->check() ? 'fa-user-check' : 'fa-user' }}"></i>
        <span class="giris-dugmesi-metin">{{ auth()->check() ? 'Panel' : 'Giriş' }}</span>
    </button>

    <div class="giris-paneli" id="girisPaneli" role="dialog" aria-label="Yönetici girişi">
        @auth
            <p class="giris-baslik">Merhaba, {{ auth()->user()->name }}</p>
            <a href="{{ route('yonetim.panel') }}" class="giris-gonder">Yönetim paneline git</a>
            <form method="post" action="{{ route('yonetim.cikis') }}">
                @csrf
                <button type="submit" class="giris-cikis">Çıkış yap</button>
            </form>
        @else
            <form method="post" action="{{ route('yonetim.giris') }}">
                @csrf
                <p class="giris-baslik">Yönetici girişi</p>

                @if ($hatalar->any())
                    <p class="giris-hata" role="alert">{{ $hatalar->first() }}</p>
                @endif

                <label for="girisEposta" class="visually-hidden">E-posta</label>
                <input type="email" name="email" id="girisEposta" placeholder="E-posta"
                       value="{{ $hatalar->any() ? old('email') : '' }}" required autocomplete="username">

                <label for="girisSifre" class="visually-hidden">Şifre</label>
                <input type="password" name="password" id="girisSifre" placeholder="Şifre"
                       required autocomplete="current-password">

                <label class="giris-hatirla">
                    <input type="checkbox" name="hatirla" value="1"> Beni hatırla
                </label>

                <button type="submit" class="giris-gonder">Giriş yap</button>
            </form>
        @endauth
    </div>
</div>
