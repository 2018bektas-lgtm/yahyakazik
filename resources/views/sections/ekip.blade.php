{{--
    Hakkımızda > Ekibimiz: büronun avukatları. Kayıtlar yönetim panelindeki
    "Avukatlar" bölümünden eklenir; sıralama oradaki "Sıra" alanına göredir.

    Beklenen değişken: $ekip (Collection<App\Models\Avukat>)
--}}
@if ($ekip->isNotEmpty())
<section class="ekip section-padding" id="ekibimiz">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 text-center mb-40 animate-box" data-animate-effect="fadeInUp">
                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Ekibimiz
                </div>
                <div class="section-title">Dosyanızla ilgilenecek <span>avukatlar</span></div>
                <p>
                    Her dosyanın baştan sona sorumlu bir avukatı vardır; müvekkilimiz kiminle
                    çalıştığını bilir ve doğrudan ona ulaşır. Uzmanlık gerektiren konularda
                    dosya, ekip içinde birlikte değerlendirilir.
                </p>
            </div>
        </div>

        <div class="row justify-content-center ekip-izgara">
            @foreach ($ekip as $avukat)
                <div class="col-lg-4 col-md-6 mb-30 animate-box" data-animate-effect="fadeInUp">
                    @include('partials.avukat-karti', ['avukat' => $avukat])
                </div>
            @endforeach

            <div class="col-lg-4 col-md-6 mb-30 animate-box" data-animate-effect="fadeInUp">
                <aside class="ekibe-katil">
                    <i class="flaticon-courthouse" aria-hidden="true"></i>
                    <h4>Ekibimize katılın</h4>
                    <p>Avukat ve stajyer avukat başvurularını her zaman değerlendiriyoruz. Özgeçmişinizi e-posta ile gönderebilirsiniz.</p>
                    <a href="mailto:{{ config('site.eposta') }}?subject={{ rawurlencode('Kariyer başvurusu') }}" class="avukat-bag">
                        {{ config('site.eposta') }} <i class="fa-regular fa-arrow-right"></i>
                    </a>
                </aside>
            </div>
        </div>

    </div>
</section>
@endif
