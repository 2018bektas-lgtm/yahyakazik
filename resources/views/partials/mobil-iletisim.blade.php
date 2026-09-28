{{--
    Mobilde ekranın altına sabitlenen ara / WhatsApp çubuğu.
    Masaüstünde gizlenir (site.css içinde).
--}}
<div class="mobil-iletisim" role="navigation" aria-label="Hızlı iletişim">
    <a href="tel:{{ config('site.telefon_link') }}" class="ara">
        <i class="fa-solid fa-phone"></i>
        <span>Hemen Ara</span>
    </a>

    @if (config('site.whatsapp'))
        <a href="https://wa.me/{{ config('site.whatsapp') }}" class="whatsapp"
           target="_blank" rel="noopener">
            <i class="fa-brands fa-whatsapp"></i>
            <span>WhatsApp</span>
        </a>
    @endif
</div>
