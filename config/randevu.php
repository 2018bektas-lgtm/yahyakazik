<?php

/*
|--------------------------------------------------------------------------
| Randevu Ayarları
|--------------------------------------------------------------------------
| Sitedeki "Başvuru / Randevu" sayfasında müvekkillere gösterilen boş
| saatler buradan hesaplanır. Dolu saatler (bekleyen veya onaylı
| randevular ve takvimde "Müsait değil" olarak işaretlenmiş zamanlar)
| otomatik olarak listeden çıkarılır.
*/

return [

    // Randevu verilen günler (1 = Pazartesi ... 7 = Pazar)
    'gunler' => [1, 2, 3, 4, 5],

    // İlk ve son randevu saatleri; son randevu bitiş saatinden önce biter
    'baslangic' => '09:00',
    'bitis'     => '18:00',

    // Her randevunun süresi (dakika)
    'sure' => 30,

    // Randevu verilmeyen saat aralıkları (öğle arası vb.)
    'kapali_araliklar' => [
        ['12:00', '13:00'],
    ],

    // Kaç gün sonrasına kadar randevu alınabilir
    'ileri_gun' => 30,

    // Bugün için en az kaç saat sonrasına randevu alınabilir
    'en_erken_saat' => 2,

    // Başvuru formunda sunulan konu seçenekleri; faaliyet alanlarına ek
    'ek_konular' => ['Kira / Gayrimenkul', 'İcra ve Alacak', 'Diğer'],

];
