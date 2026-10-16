@extends('layouts.app')

@section('baslik', 'Hakkımızda')
@section('aciklama', 'Otorite Hukuk ve Danışmanlık Bürosu, kurucusu Av. Yahya Kazık ve avukat ekibi: deneyim, uzmanlık alanları, çalışma ilkeleri ve süreç.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Bizi tanıyın',
        'baslik'    => 'Otorite',
        'vurgu'     => 'Hukuk',
    ])

    {{-- Büro tanıtımı --}}
    @include('sections.tanitim')

    {{-- Kurucu avukat (koyu şerit) ve tüm ekip; her avukatın ayrıntılı
         özgeçmişi kendi profil sayfasındadır (/ekibimiz/{slug}) --}}
    @include('sections.kurucu')
    @include('sections.ekip', ['ekip' => $ekip])

    {{-- Çalışma biçimi --}}
    @include('sections.ilkeler')
    @include('sections.surec')
    @include('sections.sayilar')

    @include('partials.cta')

@endsection
