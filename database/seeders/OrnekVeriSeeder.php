<?php

namespace Database\Seeders;

use App\Models\Avukat;
use App\Models\Etkinlik;
use App\Models\Gorev;
use App\Models\Randevu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Yönetim panelini denemek ve tanıtmak için örnek kayıtlar.
 *
 *   php artisan db:seed --class=OrnekVeriSeeder
 *
 * Tarihler çalıştırıldığı güne göre hesaplanır. Kişi adları ve telefonlar
 * uydurmadır; canlı sitede çalıştırmayın.
 */
class OrnekVeriSeeder extends Seeder
{
    public function run(): void
    {
        $bugun = Carbon::today();
        $gun = fn (int $fark, string $saat) => $bugun->copy()->addDays($fark)->setTimeFromTimeString($saat);
        // Hafta sonuna denk gelen randevuları pazartesiye kaydır
        $isGunu = function (int $fark, string $saat) use ($gun) {
            $t = $gun($fark, $saat);
            while ($t->isWeekend()) {
                $t->addDay();
            }

            return $t;
        };

        Model::unguarded(function () use ($gun, $isGunu, $bugun) {
            $this->randevular($gun, $isGunu);
            $this->etkinlikler($gun, $isGunu);
            $this->gorevler($bugun);
            $this->avukatlar();
        });
    }

    private function randevular(callable $gun, callable $isGunu): void
    {
        $kayitlar = [
            // Geçmiş, tamamlanmış görüşmeler
            ['ad_soyad' => 'Murat Aksoy', 'konu' => 'İş Hukuku', 'baslangic' => $isGunu(-9, '10:00'), 'durum' => 'tamamlandi', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0532 410 22 18'],
            ['ad_soyad' => 'Elif Şahin', 'konu' => 'Aile Hukuku', 'baslangic' => $isGunu(-6, '14:30'), 'durum' => 'tamamlandi', 'gorusme_sekli' => 'online', 'telefon' => '0541 377 90 12'],
            ['ad_soyad' => 'Hakan Yıldız', 'konu' => 'Ticaret Hukuku', 'baslangic' => $isGunu(-3, '11:00'), 'durum' => 'tamamlandi', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0505 218 44 60'],
            ['ad_soyad' => 'Sevgi Kılıç', 'konu' => 'Tazminat Hukuku', 'baslangic' => $isGunu(-2, '15:30'), 'durum' => 'iptal', 'gorusme_sekli' => 'telefon', 'telefon' => '0533 902 11 47'],

            // Bugün
            ['ad_soyad' => 'Mehmet Kaya', 'konu' => 'İş Hukuku', 'kurum' => 'Özel sektör, lojistik firması', 'baslangic' => $gun(0, '17:00'), 'durum' => 'onaylandi', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0533 444 55 66', 'aciklama' => 'Haklı nedenle fesih ve kıdem tazminatı talebi.'],
            ['ad_soyad' => 'Ayşe Polat', 'konu' => 'Memur Hukuku', 'kurum' => 'Milli Eğitim Bakanlığı', 'baslangic' => $gun(0, '17:30'), 'durum' => 'beklemede', 'gorusme_sekli' => 'online', 'telefon' => '0546 120 33 81', 'eposta' => 'ayse.polat@example.com', 'aciklama' => 'Disiplin cezasına itiraz süreci hakkında görüşmek istiyorum.'],

            // Önümüzdeki günler
            ['ad_soyad' => 'Ali Demir', 'konu' => 'İş Hukuku', 'baslangic' => $isGunu(1, '10:30'), 'durum' => 'onaylandi', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0532 655 70 09'],
            ['ad_soyad' => 'Zehra Arslan', 'konu' => 'Aile Hukuku', 'baslangic' => $isGunu(2, '14:00'), 'durum' => 'onaylandi', 'gorusme_sekli' => 'telefon', 'telefon' => '0542 813 26 54', 'aciklama' => 'Anlaşmalı boşanma protokolü.'],
            ['ad_soyad' => 'Burak Öztürk', 'konu' => 'Ceza Hukuku', 'baslangic' => $isGunu(3, '09:30'), 'durum' => 'beklemede', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0507 390 18 25'],
            ['ad_soyad' => 'Fatma Koç', 'konu' => 'Sağlık Hukuku', 'kurum' => 'Devlet hastanesi', 'baslangic' => $isGunu(6, '11:30'), 'durum' => 'onaylandi', 'gorusme_sekli' => 'online', 'telefon' => '0535 271 64 90'],
            ['ad_soyad' => 'Emre Çelik', 'konu' => 'Asker Alma Hukuku', 'baslangic' => $isGunu(9, '15:00'), 'durum' => 'onaylandi', 'gorusme_sekli' => 'yuz_yuze', 'telefon' => '0539 118 47 03'],

            // Saatsiz başvurular ve mesaj (yanıt bekleyenler)
            ['tip' => 'basvuru', 'ad_soyad' => 'Zeynep Demir', 'konu' => 'Memur Hukuku', 'kurum' => 'Sağlık Bakanlığı', 'baslangic' => null, 'durum' => 'beklemede', 'telefon' => '0544 260 71 38', 'eposta' => 'zeynep.demir@example.com', 'aciklama' => 'Görev yerim tebligatsız değiştirildi, iptal davası açmak istiyorum.', 'created_at' => now()->subMinutes(25)],
            ['tip' => 'basvuru', 'ad_soyad' => 'Kemal Aydın', 'konu' => 'İş Hukuku', 'kurum' => 'Tekstil fabrikası', 'baslangic' => null, 'durum' => 'beklemede', 'telefon' => '0530 744 09 61', 'aciklama' => '3 aydır maaşım ödenmiyor, işten ayrılmak istiyorum.', 'created_at' => now()->subHours(3)],
            ['tip' => 'mesaj', 'ad_soyad' => 'Selin Yurt', 'konu' => 'Genel bilgi', 'baslangic' => null, 'durum' => 'beklemede', 'eposta' => 'selin.yurt@example.com', 'aciklama' => 'Danışmanlık ücretleriniz hakkında bilgi alabilir miyim?', 'created_at' => now()->subDay()],
        ];

        foreach ($kayitlar as $kayit) {
            Randevu::create($kayit + ['tip' => 'randevu', 'kaynak' => 'site', 'sure' => 30]);
        }
    }

    private function etkinlikler(callable $gun, callable $isGunu): void
    {
        $kayitlar = [
            ['baslik' => 'Ankara 5. İş Mahkemesi, işe iade duruşması', 'tur' => 'durusma', 'baslangic' => $isGunu(1, '09:40'), 'yer' => 'Ankara Adliyesi, C Blok', 'dosya_no' => '2026/412 E.'],
            ['baslik' => 'Ankara 3. Aile Mahkemesi, boşanma duruşması', 'tur' => 'durusma', 'baslangic' => $isGunu(4, '11:15'), 'yer' => 'Ankara Adliyesi, B Blok', 'dosya_no' => '2026/1187 E.'],
            ['baslik' => 'Ankara 2. İdare Mahkemesi, atama iptali duruşması', 'tur' => 'durusma', 'baslangic' => $isGunu(8, '10:00'), 'yer' => 'Ankara Bölge İdare Mahkemesi', 'dosya_no' => '2026/655 E.'],
            ['baslik' => 'Asliye Ceza Mahkemesi duruşması', 'tur' => 'durusma', 'baslangic' => $isGunu(12, '14:00'), 'yer' => 'Sıhhiye Adliyesi', 'dosya_no' => '2025/2290 E.'],
            ['baslik' => 'Tüketici Mahkemesi, bilirkişi incelemesi', 'tur' => 'durusma', 'baslangic' => $isGunu(-4, '13:30'), 'yer' => 'Ankara Adliyesi', 'tamamlandi' => true],
            ['baslik' => 'İstinaf dilekçesi son gün', 'tur' => 'sure', 'baslangic' => $gun(0, '00:00'), 'tum_gun' => true, 'dosya_no' => '2025/980 E.'],
            ['baslik' => 'Bilirkişi raporuna itiraz süresi doluyor', 'tur' => 'sure', 'baslangic' => $gun(5, '00:00'), 'tum_gun' => true, 'dosya_no' => '2026/412 E.'],
            ['baslik' => 'Arabuluculuk toplantısı (işçi alacağı)', 'tur' => 'toplanti', 'baslangic' => $isGunu(2, '10:00'), 'bitis' => $isGunu(2, '11:30'), 'yer' => 'Büro'],
            ['baslik' => 'Baro eğitim semineri', 'tur' => 'musait_degil', 'baslangic' => $isGunu(7, '13:00'), 'bitis' => $isGunu(7, '17:00'), 'yer' => 'Ankara Barosu'],
        ];

        foreach ($kayitlar as $kayit) {
            Etkinlik::create($kayit);
        }
    }

    /** Ekip bölümünü denemek için uydurma iki avukat (kurucu kaydı migration ile gelir). */
    private function avukatlar(): void
    {
        Avukat::updateOrCreate(['slug' => 'ornek-elif-arslan'], [
            'ad' => 'Av. Elif Arslan', 'unvan' => 'Avukat', 'baro' => 'Ankara Barosu', 'sira' => 1,
            'ozet' => 'İş ve sosyal güvenlik hukuku dosyalarında işçi ve işveren vekilliği yürütüyor.',
            'biyografi' => "ÖRNEK KAYIT: Bu profil paneli denemek için eklenmiştir.\n\nAnkara Üniversitesi Hukuk Fakültesi mezunu. İşe iade, kıdem ve ihbar tazminatı ile iş kazası davalarında çalışıyor.",
            'uzmanliklar' => ['İş Hukuku', 'Tazminat Hukuku', 'Arabuluculuk'],
            'ozgecmis' => [
                ['donem' => 'Eğitim', 'baslik' => 'Ankara Üniversitesi Hukuk Fakültesi', 'metin' => 'Lisans eğitimini tamamladı.'],
                ['donem' => '2022', 'baslik' => 'Otorite Hukuk', 'metin' => 'Büronun iş hukuku dosyalarında görev alıyor.'],
            ],
        ]);

        Avukat::updateOrCreate(['slug' => 'ornek-can-yilmaz'], [
            'ad' => 'Stj. Av. Can Yılmaz', 'unvan' => 'Stajyer Avukat', 'baro' => 'Ankara Barosu', 'sira' => 2,
            'ozet' => 'Ceza ve memur hukuku dosyalarında dosya hazırlığı ve duruşma takibi.',
            'biyografi' => 'ÖRNEK KAYIT: Bu profil paneli denemek için eklenmiştir.',
            'uzmanliklar' => ['Ceza Hukuku', 'Memur Hukuku'],
        ]);
    }

    private function gorevler(Carbon $bugun): void
    {
        $dilekce = Gorev::create(['baslik' => 'Bilirkişi raporuna itiraz yaz', 'tarih' => $bugun->toDateString(), 'saat' => '15:00', 'aciklama' => '2026/412 E. dosyası']);
        $dilekce->altGorevler()->create(['baslik' => 'Raporu oku', 'tarih' => $bugun->toDateString(), 'tamamlandi_at' => now()]);
        $dilekce->altGorevler()->create(['baslik' => 'Emsal kararları bul', 'tarih' => $bugun->toDateString()]);
        $dilekce->altGorevler()->create(['baslik' => 'Dilekçeyi UYAP\'tan gönder', 'tarih' => $bugun->toDateString()]);

        Gorev::create(['baslik' => 'Müvekkil Zeynep Hanımı ara', 'tarih' => $bugun->toDateString()]);
        Gorev::create(['baslik' => 'Harç makbuzlarını tara', 'tarih' => $bugun->toDateString(), 'saat' => '10:00', 'tamamlandi_at' => now()]);

        $durusma = Gorev::create(['baslik' => 'Yarınki duruşma için hazırlık', 'tarih' => $bugun->copy()->addDay()->toDateString(), 'saat' => '09:00']);
        $durusma->altGorevler()->create(['baslik' => 'Tanık listesini kontrol et', 'tarih' => $bugun->copy()->addDay()->toDateString()]);
        $durusma->altGorevler()->create(['baslik' => 'Dosya fotokopilerini al', 'tarih' => $bugun->copy()->addDay()->toDateString()]);
    }
}
