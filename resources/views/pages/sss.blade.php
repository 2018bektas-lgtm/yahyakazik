@extends('layouts.app')

@section('baslik', 'Sıkça Sorulan Sorular')
@section('aciklama', 'Vekâletname, avukatlık ücreti, dava süreleri ve süreç takibi hakkında sık sorulan sorular.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Merak edilenler',
        'baslik'    => 'Sıkça Sorulan',
        'vurgu'     => 'Sorular',
    ])

    @include('sections.sss-listesi', ['baslik' => false])

    @include('partials.cta')

@endsection
