{{-- Anasayfadaki son yazılar --}}
<section class="blog section-padding bg-lightbrown">
    <div class="container">

        <div class="row">
            <div class="col-md-12 text-center mb-20">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Bilgilendirme yazıları
                </div>
                <div class="section-title">Makale<span>lerimiz</span></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="owl-carousel owl-theme">
                    @foreach (config('icerik.yazilar') as $yazi)
                        @include('partials.yazi-karti', ['yazi' => $yazi])
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
