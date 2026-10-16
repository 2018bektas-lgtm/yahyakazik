<?php

namespace Tests\Feature;

use App\Models\Etkinlik;
use App\Models\Randevu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BasvuruTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pazartesi sabahı; en erken randevu 2 saat sonrası (10:00)
        Carbon::setTestNow(Carbon::parse('2026-10-19 08:00', 'Europe/Istanbul'));
    }

    private function gecerli(array $ek = []): array
    {
        return array_merge([
            'tip'           => 'randevu',
            'konu'          => 'İş Hukuku',
            'tarih'         => '2026-10-20',
            'saat'          => '10:30',
            'ad_soyad'      => 'Ayşe Yılmaz',
            'telefon'       => '0532 111 22 33',
            'gorusme_sekli' => 'yuz_yuze',
            'kvkk'          => '1',
        ], $ek);
    }

    public function test_sayfa_acilir(): void
    {
        $this->get('/basvuru')->assertOk()->assertSee('Size nasıl yardımcı olalım?')->assertSee('İş Hukuku');
    }

    public function test_saatler_30_dakikalik_dilimlerle_ogle_arasi_haric(): void
    {
        $saatler = $this->getJson('/basvuru/saatler?tarih=2026-10-20')->assertOk()->json('saatler');

        $this->assertSame('09:00', $saatler[0]);
        $this->assertContains('09:30', $saatler);
        $this->assertNotContains('12:00', $saatler);
        $this->assertNotContains('12:30', $saatler);
        $this->assertSame('17:30', end($saatler));
    }

    public function test_bugun_icin_en_erken_iki_saat_sonrasi_ve_hafta_sonu_kapali(): void
    {
        $this->assertSame('10:00', $this->getJson('/basvuru/saatler?tarih=2026-10-19')->json('saatler.0'));
        $this->assertSame([], $this->getJson('/basvuru/saatler?tarih=2026-10-24')->json('saatler'));
    }

    public function test_randevu_alinir_ve_saat_dolar(): void
    {
        $this->post('/basvuru', $this->gecerli())->assertRedirect('/basvuru/tamam');

        $randevu = Randevu::sole();
        $this->assertSame('beklemede', $randevu->durum);
        $this->assertSame('2026-10-20 10:30', $randevu->baslangic->format('Y-m-d H:i'));
        $this->assertSame(30, $randevu->sure);

        $this->assertNotContains('10:30', $this->getJson('/basvuru/saatler?tarih=2026-10-20')->json('saatler'));
        $this->post('/basvuru', $this->gecerli(['ad_soyad' => 'Başka Biri']))->assertSessionHasErrors('saat');
        $this->assertSame(1, Randevu::count());
    }

    public function test_durusma_ve_musait_degil_saatleri_kapatir(): void
    {
        Etkinlik::create(['baslik' => 'Duruşma', 'tur' => 'durusma', 'baslangic' => '2026-10-20 14:00', 'bitis' => '2026-10-20 15:00']);
        Etkinlik::create(['baslik' => 'İzin', 'tur' => 'musait_degil', 'baslangic' => '2026-10-21 00:00', 'tum_gun' => true]);

        $sali = $this->getJson('/basvuru/saatler?tarih=2026-10-20')->json('saatler');
        $this->assertNotContains('14:00', $sali);
        $this->assertNotContains('14:30', $sali);
        $this->assertContains('15:00', $sali);

        $this->assertSame([], $this->getJson('/basvuru/saatler?tarih=2026-10-21')->json('saatler'));
    }

    public function test_basvuru_randevusuz_gonderilebilir_ama_aciklama_ister(): void
    {
        $basvuru = $this->gecerli(['tip' => 'basvuru', 'tarih' => null, 'saat' => null, 'kurum' => 'XYZ Lojistik A.Ş.']);

        $this->post('/basvuru', $basvuru)->assertSessionHasErrors('aciklama');

        $this->post('/basvuru', $basvuru + ['aciklama' => 'Altı yıl çalıştığım işyerinden haksız yere çıkarıldım.'])
            ->assertRedirect('/basvuru/tamam');

        $kayit = Randevu::sole();
        $this->assertSame('basvuru', $kayit->tip);
        $this->assertNull($kayit->baslangic);
        $this->assertSame('XYZ Lojistik A.Ş.', $kayit->kurum);
    }

    public function test_kvkk_onayi_zorunlu_ve_bot_alani_kayit_acmaz(): void
    {
        $this->post('/basvuru', $this->gecerli(['kvkk' => null]))->assertSessionHasErrors('kvkk');
        $this->post('/basvuru', $this->gecerli(['web_sitesi' => 'spam']))->assertRedirect('/basvuru/tamam');
        $this->assertSame(0, Randevu::count());
    }

    public function test_iletisim_formu_mesaj_olarak_kaydedilir(): void
    {
        $this->from('/iletisim')->post('/iletisim', [
            'ad' => 'Ali Veli', 'eposta' => 'ali@example.com', 'mesaj' => 'Merhaba, bilgi almak istiyorum.',
        ])->assertRedirect('/iletisim')->assertSessionHas('mesaj_gonderildi');

        $this->assertSame('mesaj', Randevu::sole()->tip);
    }
}
