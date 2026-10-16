{{--
    Bir kaydın durumunu tek tıkla değiştiren küçük düğmeler. $randevu beklenir.
    Zamanı belli olmayan başvuru ve mesajlar onaylanmaz; "İşlendi" olarak kapatılır.
--}}
@php
    $secenekler = $randevu->baslangic
        ? ['onaylandi' => ['fa-check', 'Onayla'], 'tamamlandi' => ['fa-flag-checkered', 'Tamamlandı'], 'iptal' => ['fa-xmark', 'İptal']]
        : ['tamamlandi' => ['fa-check-double', 'İşlendi'], 'iptal' => ['fa-xmark', 'İptal']];

    if ($randevu->baslangic && $randevu->durum !== 'onaylandi') {
        unset($secenekler['tamamlandi']);
    }
    unset($secenekler[$randevu->durum]);
@endphp
<div class="durum-dugmeleri">
    @foreach ($secenekler as $durum => [$ikon, $etiket])
        <form method="post" action="{{ route('yonetim.randevular.durum', $randevu) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="durum" value="{{ $durum }}">
            <button type="submit" class="ikon-dugme {{ $durum }}" title="{{ $etiket }}" aria-label="{{ $etiket }}: {{ $randevu->ad_soyad }}">
                <i class="fa-solid {{ $ikon }}"></i>
            </button>
        </form>
    @endforeach
</div>
