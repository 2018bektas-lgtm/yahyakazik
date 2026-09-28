{{--
    Sayaç şeridi (config/icerik.php > sayilar).

    'sayac' => true olan kayıtlar görünür alana girince sıfırdan sayarak artar;
    false olanlar (yıl gibi) olduğu gibi yazılır. Sayma işi site.js içinde.
--}}
<section class="sayilar section-padding bg-darkbrown">
    <div class="container">
        <div class="row">
            @foreach (config('icerik.sayilar') as $sira => $kayit)
                <div class="col-lg-3 col-md-6 animate-box" data-animate-effect="fadeInUp"
                     style="--gecikme: {{ $sira * 90 }}ms">
                    <div class="sayi-kutu">
                        <div class="sayi"
                             @if ($kayit['sayac']) data-sayac="{{ $kayit['deger'] }}" @endif>
                            @if ($kayit['sayac']) 0 @else {{ $kayit['deger'] }} @endif
                        </div>
                        <div class="etiket">{{ $kayit['etiket'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
