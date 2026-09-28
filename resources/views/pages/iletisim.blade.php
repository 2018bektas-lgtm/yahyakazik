@extends('layouts.app')

@section('baslik', 'İletişim')
@section('aciklama', 'Randevu ve hukuki danışmanlık için büromuzun adres, telefon ve e-posta bilgileri.')

@section('icerik')

    @include('partials.banner', [
        'ustBaslik' => 'Bize ulaşın',
        'baslik'    => 'İletişim',
        'vurgu'     => 'Bilgileri',
    ])

    <section class="info-box section-padding">
        <div class="container">
            <div class="row">

                <div class="col-md-6">
                    <div class="section-subtitle">
                        <div class="icon"><i class="flaticon-courthouse"></i></div> Bize ulaşın
                    </div>
                    <div class="section-title">Desteğe mi ihtiyacınız var? <span>Hemen arayın!</span></div>

                    <div class="item">
                        <i class="icon fa-regular fa-location-dot"></i>
                        <div class="cont">
                            <h5>Adres</h5>
                            <p>{{ config('site.adres') }}<br>{{ config('site.adres_2') }}</p>
                        </div>
                    </div>

                    <div class="item">
                        <i class="icon fa-solid fa-phone"></i>
                        <div class="cont">
                            <h5>Telefon</h5>
                            <p><a href="tel:{{ config('site.telefon_link') }}">{{ config('site.telefon') }}</a></p>
                        </div>
                    </div>

                    <div class="item">
                        <i class="icon fa-regular fa-envelope"></i>
                        <div class="cont">
                            <h5>E-posta</h5>
                            <p><a href="mailto:{{ config('site.eposta') }}">{{ config('site.eposta') }}</a></p>
                        </div>
                    </div>

                    <div class="item">
                        <i class="icon fa-regular fa-clock"></i>
                        <div class="cont">
                            <h5>Çalışma Saatleri</h5>
                            <p>{{ config('site.calisma_saatleri') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 offset-md-1">
                    {{--
                        Form şu an yalnızca görseldir; herhangi bir adrese gönderim yapmaz.
                        Çalışır hâle getirmek için bir POST rotası + doğrulama + Mail gönderimi
                        eklenmeli ve aşağıdaki action alanı o rotaya yönlendirilmelidir.
                    --}}
                    <div class="contact-form">
                        <form action="#" method="post">
                            <h3>Bize yazın <span>size dönelim!</span></h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="ad" class="visually-hidden">Ad Soyad</label>
                                    <input type="text" name="ad" id="ad" placeholder="Ad Soyad" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="eposta" class="visually-hidden">E-posta</label>
                                    <input type="email" name="eposta" id="eposta" placeholder="E-posta Adresiniz" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="telefon" class="visually-hidden">Telefon</label>
                                    <input type="tel" name="telefon" id="telefon" placeholder="Telefon Numaranız">
                                </div>
                                <div class="col-md-12">
                                    <label for="mesaj" class="visually-hidden">Mesajınız</label>
                                    <textarea name="mesaj" id="mesaj" cols="40" rows="4" placeholder="Mesajınız" required></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="button-3"><span>Mesajı Gönder</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Harita --}}
    <div class="contact-map">
        <iframe src="{{ config('site.harita_embed') }}"
                width="100%" height="450" style="border:0; display:block;"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                title="{{ config('site.unvan') }} konumu"></iframe>
    </div>

    @include('partials.cta')

@endsection
