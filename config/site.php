<?php

/*
|--------------------------------------------------------------------------
| Büro Bilgileri
|--------------------------------------------------------------------------
| Sitenin her yerinde kullanılan kurumsal bilgiler. Buradaki bir alanı
| değiştirdiğinizde tüm sayfalarda güncellenir.
|
| Bilgiler avyahyakazik.com.tr üzerindeki mevcut siteden alınmıştır.
*/

return [

    // Kurumsal kimlik
    'ad'         => 'Av. Yahya Kazık',
    'unvan'      => 'Otorite Hukuk ve Danışmanlık Bürosu',
    'kisa_unvan' => 'Otorite Hukuk',
    'slogan'     => 'Hukukta saygın çözüm ortağınız',
    'aciklama'   => 'Otorite Hukuk ve Danışmanlık Bürosu olarak Av. Yahya Kazık
                     önderliğinde Ankara ili başta olmak üzere Türkiye genelinde
                     avukatlık, arabuluculuk ve hukuki danışmanlık hizmeti veriyoruz.',

    // Serbest avukatlık ve arabuluculuğa başlanan yıl (sayaç bölümü bunu kullanır)
    'meslek_baslangic' => 2017,

    /*
     | Fotoğraflar
     |
     | 'avukat_gorsel' Av. Kazık'ın portresidir. DOLDURULMADAN BIRAKILMALIDIR:
     | boş olduğunda "Kurucu avukat" bölümü fotoğrafsız, ortalanmış olarak
     | görünür. Buraya stok fotoğraf konulması, gerçek bir kişiyi başkasının
     | fotoğrafıyla tanıtmak anlamına gelir.
     | TODO: Profesyonel portre çekimi yapıldığında doldurun.
     |
     | 'ofis_gorsel' tanıtım bölümündeki görseldir. Şu an tema ile gelen
     | kişisiz bir adliye fotoğrafı kullanılıyor.
     | TODO: Büro fotoğrafı çekildiğinde değiştirin.
     */
    'avukat_gorsel' => null,
    'ofis_gorsel'   => 'frontend/img/case/6.jpg',

    // İç sayfa başlıkları ve sayfa sonu şeridi için kişisiz mimari görsel
    'banner_gorsel' => 'frontend/img/case/5.jpg',

    /*
     | Logo
     |
     | 'logo' null bırakıldığında tema tipografisiyle uyumlu metin logosu
     | kullanılır. Hazır logo görseli geldiğinde dosyayı public/frontend/img/
     | altına koyup yolunu buraya yazmanız yeterli, örn:
     | 'logo' => 'frontend/img/logo-otorite.png'
     */
    'logo'       => null,
    'logo_ad'    => 'Otorite',        // normal renkte
    'logo_vurgu' => 'Hukuk',          // bakır vurgulu
    'logo_alt'   => 'Av. Yahya Kazık',// altta harf aralıklı küçük yazı

    // İletişim
    'telefon'          => '+90 542 682 55 16',
    'telefon_link'     => '+905426825516',
    'whatsapp'         => '905426825516',
    'eposta'           => 'otoritehukuk@gmail.com',
    'adres'            => 'Korkutreis Mahallesi, Cihan Sokak No: 7/2',
    'adres_2'          => 'Çankaya / Ankara',
    'calisma_saatleri' => 'Pazartesi - Cuma, 09:00 - 18:00',

    // Google Haritalar yerleştirme adresi (iletişim sayfası)
    'harita_embed' => 'https://maps.google.com/maps?q=Korkutreis+Mahallesi+Cihan+Sokak+7+%C3%87ankaya+Ankara&t=&z=16&ie=UTF8&iwloc=&output=embed',

    // Sosyal medya (boş bırakılan ikon gösterilmez)
    // TODO: Gerçek hesap adresleri geldiğinde doldurun.
    'sosyal' => [
        'linkedin'  => '',
        'instagram' => '',
        'twitter'   => '',
        'facebook'  => '',
    ],

    // Anasayfadaki tanıtım videosu. Boş bırakılırsa video bölümü gizlenir.
    // TODO: Büronun kendi tanıtım videosu çekilene kadar boş kalmalı.
    'video_url' => '',

];
