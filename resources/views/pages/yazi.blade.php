@extends('layouts.app')

@section('baslik', $yazi['baslik'])
@section('aciklama', $yazi['ozet'])

@section('icerik')

    {{-- Yazının kendi görselini taşıyan üst başlık --}}
    <div class="banner-header valign bg-img bg-fixed" data-overlay-dark="6"
         data-background="{{ asset($yazi['gorsel'] ?: config('site.banner_gorsel')) }}">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mt-60">
                    <h2>{{ $yazi['baslik'] }}</h2>
                    <div class="post">
                        <div class="author">
                            <span>{{ config('site.ad') }}</span>
                        </div>
                        <div class="date-comment"> <i class="ti-calendar"></i> {{ $yazi['tarih'] }} </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="post section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">

                    @foreach ($yazi['govde'] as $index => $paragraf)
                        <p>{{ $paragraf }}</p>

                        {{-- İlk paragraftan sonra öne çıkan alıntı --}}
                        @if ($index === 0 && ! empty($yazi['alinti']))
                            <blockquote>
                                <p>{{ $yazi['alinti'] }}</p>
                                <cite>{{ config('site.ad') }}</cite>
                            </blockquote>
                        @endif
                    @endforeach

                    <p class="mt-30">
                        <em>
                            Bu yazı genel bilgilendirme amacı taşır ve hukuki görüş niteliğinde değildir.
                            Somut durumunuz için
                            <a href="{{ route('iletisim') }}" class="text-decoration-line-bottom">bizimle iletişime geçin</a>.
                        </em>
                    </p>

                </div>
            </div>

            {{-- Diğer yazılar --}}
            @if ($digerYazilar->isNotEmpty())
                <div class="row justify-content-center mt-60">
                    <div class="col-lg-10 col-md-12">
                        <div class="section-title mb-30">Diğer <span>Yazılar</span></div>
                        <div class="row">
                            @foreach ($digerYazilar as $diger)
                                <div class="col-lg-4 col-md-6">
                                    @include('partials.yazi-karti', ['yazi' => $diger])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    @include('partials.cta')

@endsection
