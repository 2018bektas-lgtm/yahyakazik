{{-- Sayfa sonundaki iletişim şeridi --}}
<section class="call">
    <div class="background bg-img bg-fixed section-padding"
         data-background="{{ asset(config('site.banner_gorsel')) }}" data-overlay-dark="7">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 col-md-12">
                    <div class="section-title text-white mb-0">
                        Herkes kanun önünde eşittir. Farkı yaratan, <span>doğru hukuki temsildir.</span>
                    </div>
                </div>
                <div class="col-lg-3 offset-lg-1 col-md-12">
                    <div class="call-center">
                        <div class="icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="text">
                            <p>Bize ulaşın</p>
                            <a href="tel:{{ config('site.telefon_link') }}">{{ config('site.telefon') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
