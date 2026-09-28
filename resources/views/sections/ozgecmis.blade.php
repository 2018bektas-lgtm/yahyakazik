{{--
    Özgeçmiş zaman çizelgesi (config/icerik.php > ozgecmis).

    Sol taraftaki dikey çizgi, bölüm görünür alana girdikçe yukarıdan aşağı
    doğru çizilir; maddeler sırayla belirir. Hareket site.js içinde yönetilir.
--}}
<section class="ozgecmis section-padding" id="ozgecmis">
    <div class="container">

        <div class="row">
            <div class="col-md-12 text-center mb-40">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Kısa özgeçmiş
                </div>
                <div class="section-title">Kamu Deneyimi <span>ve</span> Akademik Birikim</div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12">
                <div class="zaman-cizelgesi">
                    <span class="cizgi" aria-hidden="true"><span class="dolgu"></span></span>

                    @foreach (config('icerik.ozgecmis') as $kayit)
                        <div class="madde">
                            <span class="nokta" aria-hidden="true"></span>
                            <div class="donem">{{ $kayit['donem'] }}</div>
                            <h4>{{ $kayit['baslik'] }}</h4>
                            <p>{{ $kayit['metin'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
