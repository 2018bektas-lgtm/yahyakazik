@extends('layouts.app')

@section('baslik', config('site.slogan'))
@section('aciklama', 'Ankara Çankaya merkezli Otorite Hukuk ve Danışmanlık Bürosu; iş, tazminat, aile, ceza, asker alma, memur, sağlık ve ticaret hukuku alanlarında avukatlık ve arabuluculuk hizmeti sunar.')

@section('icerik')

    @include('sections.slider')
    @include('sections.tanitim')
    @include('sections.ilkeler')
    @include('sections.sayilar')
    @include('sections.faaliyet-alanlari')
    @include('sections.kurucu')
    @include('sections.surec')
    @include('sections.sss-listesi', ['adet' => 4])
    @include('sections.blog')
    @include('partials.cta')

@endsection
