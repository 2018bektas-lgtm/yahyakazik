{{--
    Açılır-kapanır soru listesi. İki sütuna bölünerek gösterilir.

    İsteğe bağlı değişkenler:
        $adet    - gösterilecek soru sayısı (varsayılan: tümü)
        $baslik  - bölüm başlığı gösterilsin mi (varsayılan: evet)
--}}
@php
    $sorular = config('icerik.sss');

    if (isset($adet)) {
        $sorular = array_slice($sorular, 0, $adet);
    }

    // Soruları iki sütuna eşit dağıt
    $sutunlar = array_chunk($sorular, (int) ceil(count($sorular) / 2));
@endphp

<section class="section-padding">
    <div class="container">

        @if ($baslik ?? true)
            <div class="row">
                <div class="col-md-12 text-center mb-30">
                    <div class="section-subtitle">
                        <div class="icon"><i class="flaticon-courthouse"></i></div> Merak edilenler
                    </div>
                    <div class="section-title">Sıkça Sorulan <span>Sorular</span></div>
                </div>
            </div>
        @endif

        <div class="row">
            @foreach ($sutunlar as $sutun)
                <div class="col-lg-6 col-md-12">
                    <ul class="accordion-box clearfix">
                        @foreach ($sutun as $kayit)
                            <li class="accordion block">
                                <div class="acc-btn size-20">{{ $kayit['soru'] }}</div>
                                <div class="acc-content">
                                    <div class="content">
                                        <div class="text">{{ $kayit['cevap'] }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

    </div>
</section>
