{{-- Büronun çalışma ilkeleri (config/icerik.php > ilkeler) --}}
<section class="case-study-box ilkeler">
    <div class="container">
        <div class="row">
            @foreach (config('icerik.ilkeler') as $sira => $ilke)
                <div class="col-lg-3 col-md-6 animate-box" data-animate-effect="fadeInUp"
                     style="--gecikme: {{ $sira * 90 }}ms">
                    <div class="item">
                        <i class="icon {{ $ilke['ikon'] }}"></i>
                        <div class="cont">
                            <h5>{{ $ilke['baslik'] }}</h5>
                            <p>{{ $ilke['metin'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
