{{-- Kurucu avukatın tanıtım metni ve uzmanlık alanları --}}
<section class="about section-padding pt-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12 animate-box" data-animate-effect="fadeInUp">

                <div class="section-subtitle">
                    <div class="icon"><i class="flaticon-courthouse"></i></div> Kurucu avukat
                </div>
                <div class="section-title">{{ config('site.kisa_unvan') }},
                    <span>köklü bir deneyimle</span> kuruldu</div>

                <p>
                    {{ config('site.kisa_unvan') }}, kurucusu {{ config('site.ad') }}'ın kamu
                    kurumlarında edindiği köklü deneyim ve akademik birikimle kurulmuştur. Uzun
                    yıllar Türk Silahlı Kuvvetleri bünyesinde çeşitli idari görevlerde bulunmuş;
                    disiplin subaylığı ve Millî Savunma Bakanlığı müfettişliği gibi sorumluluklar
                    üstlenmiştir.
                </p>
                <p>
                    İstanbul Üniversitesi Hukuk Fakültesi mezunu olan Av. Kazık, özel hukuk ve
                    kamu yönetimi alanlarında iki ayrı yüksek lisans çalışması yapmıştır.
                    2017 yılından bu yana serbest avukat ve arabulucu olarak çalışmakta; her
                    müvekkile özel, etik değerlere bağlı ve çözüm odaklı hizmet sunmayı ilke
                    edinmektedir.
                </p>

                <div class="uzmanlik-listesi mt-30">
                    <h5>Uzmanlaştığı alanlar</h5>
                    <ul class="page-list list-unstyled">
                        @foreach (config('icerik.uzmanliklar') as $uzmanlik)
                            <li>
                                <div class="page-list-icon"> <span class="ti-check"></span> </div>
                                <div class="page-list-text">
                                    <p>{{ $uzmanlik }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</section>
