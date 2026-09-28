{{-- Anasayfadaki faaliyet alanları özeti (ilk 6 kayıt) --}}
<section class="practice-areas section-padding">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-4 col-md-12 mb-30">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Uzmanlık alanlarımız
                </div>
                <div class="section-title">Faaliyet <span>Alanları</span></div>
                <p>
                    Disiplin ve asker alma hukukundan iş ve ticaret hukukuna uzanan geniş
                    bir yelpazede avukatlık, arabuluculuk ve danışmanlık hizmeti veriyoruz.
                </p>
                <a href="{{ route('faaliyet-alanlari') }}" class="button-2">Tümünü görün<span></span></a>
            </div>

            <div class="col-lg-7 offset-lg-1 col-md-12">
                <div class="row">
                    @foreach (array_slice(config('icerik.faaliyet_alanlari'), 0, 6) as $alan)
                        @php
                            // Detay metni yazılmış alan kendi sayfasına gider
                            $detayVar = array_key_exists($alan['slug'], config('icerik.faaliyet_detaylari', []));
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="item">
                                <a href="{{ $detayVar ? route('faaliyet-alani', $alan['slug']) : route('faaliyet-alanlari') }}">
                                    <i class="{{ $alan['ikon'] }}"></i>
                                    <h5>{{ $alan['ad'] }}</h5>
                                    <div class="shape"> <i class="{{ $alan['ikon'] }}"></i> </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
