@extends('layouts.app')

@section('baslik', 'Sayfa bulunamadı')

@section('icerik')

    <section class="notfound section-padding text-center">
        <div class="v-middle">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-12">
                        <h1>404</h1>
                        <h2>Sayfa Bulunamadı<span>!</span></h2>
                        <p>
                            Aradığınız sayfa taşınmış, adı değişmiş ya da hiç var olmamış olabilir.
                        </p>
                        <a href="{{ route('home') }}" class="button-3">Anasayfaya dön<span></span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
