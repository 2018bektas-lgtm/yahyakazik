{{--
    Ekip kartı (Hakkımızda > Ekibimiz ve profil sayfasının alt kısmı).
    Fotoğraf yüklenmemişse baş harfler gösterilir; stok fotoğraf kullanılmaz.

    Beklenen değişken: $avukat (App\Models\Avukat)
--}}
<article class="avukat-karti">
    <a href="{{ route('avukat', $avukat) }}" class="avukat-gorsel" tabindex="-1" aria-hidden="true">
        @if ($avukat->fotograf)
            <img src="{{ asset($avukat->fotograf) }}" alt="" loading="lazy">
        @else
            <span class="monogram">{{ $avukat->basHarfler() }}</span>
        @endif
    </a>
    <div class="avukat-govde">
        @if ($avukat->unvan)<span class="avukat-unvan">{{ $avukat->unvan }}</span>@endif
        <h4><a href="{{ route('avukat', $avukat) }}">{{ $avukat->ad }}</a></h4>
        @if ($avukat->baro)<span class="avukat-baro"><i class="fa-regular fa-scale-balanced"></i> {{ $avukat->baro }}</span>@endif
        @if ($avukat->ozet)<p>{{ $avukat->ozet }}</p>@endif
        @if ($avukat->uzmanliklar)
            <ul class="avukat-alanlar">
                @foreach (array_slice($avukat->uzmanliklar, 0, 4) as $alan)
                    <li>{{ $alan }}</li>
                @endforeach
            </ul>
        @endif
        <a href="{{ route('avukat', $avukat) }}" class="avukat-bag">Profili inceleyin <i class="fa-regular fa-arrow-right"></i></a>
    </div>
</article>
