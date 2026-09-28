@extends('layouts.app')

@section('baslik', 'Hakkımızda')
@section('aciklama', 'Otorite Hukuk ve Danışmanlık Bürosu ve kurucusu Av. Yahya Kazık: kamu deneyimi, akademik birikim, çalışma ilkeleri ve süreç.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Bizi tanıyın',
        'baslik'    => 'Otorite',
        'vurgu'     => 'Hukuk',
    ])

    {{-- Büro tanıtımı --}}
    @include('sections.tanitim')

    {{-- Kurucu avukat: özet, koyu tanıtım şeridi ve özgeçmiş --}}
    @include('sections.avukat-ozeti')
    @include('sections.kurucu')
    @include('sections.ozgecmis')

    {{-- Çalışma biçimi --}}
    @include('sections.ilkeler')
    @include('sections.surec')
    @include('sections.sayilar')

    @include('partials.cta')

@endsection
