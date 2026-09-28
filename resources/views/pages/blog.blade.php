@extends('layouts.app')

@section('baslik', 'Makalelerimiz')
@section('aciklama', 'Disiplin hukuku, asker alma, memur hukuku, iş hukuku ve arabuluculuk üzerine bilgilendirici yazılar.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Bilgilendirme yazıları',
        'baslik'    => 'Hukuki',
        'vurgu'     => 'Makaleler',
    ])

    <section class="blog section-padding bg-lightbrown">
        <div class="container">
            <div class="row">
                @foreach (config('icerik.yazilar') as $yazi)
                    <div class="col-lg-4 col-md-12">
                        @include('partials.yazi-karti', ['yazi' => $yazi])
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.cta')

@endsection
