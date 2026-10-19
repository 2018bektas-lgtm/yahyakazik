<?php

namespace Tests\Feature;

use App\Models\Etkinlik;
use App\Models\Gorev;
use App\Models\Randevu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class YonetimTest extends TestCase
{
    use RefreshDatabase;

    private User $yonetici;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-10-19 08:00', 'Europe/Istanbul'));
        $this->yonetici = User::factory()->create(['email' => 'av@example.com', 'password' => 'gizli-sifre-123']);
    }

    public function test_giris_yapmadan_panel_acilmaz(): void
    {
        $this->get('/yonetim')->assertRedirect('/yonetim/giris');
        $this->get('/yonetim/takvim/veriler?start=2026-10-01&end=2026-11-01')->assertRedirect('/yonetim/giris');
    }

    public function test_giris_ve_cikis(): void
    {
        $this->post('/yonetim/giris', ['email' => 'av@example.com', 'password' => 'yanlis'])->assertSessionHasErrorsIn('giris', 'email');
        $this->assertGuest();

        $this->post('/yonetim/giris', ['email' => 'av@example.com', 'password' => 'gizli-sifre-123'])->assertRedirect('/yonetim');
        $this->assertAuthenticated();

        $this->post('/yonetim/cikis')->assertRedirect('/yonetim/giris');
        $this->assertGuest();
    }

    public function test_ust_menudeki_giris_kutusu(): void
    {
        $this->get('/hakkimizda')->assertOk()->assertSee('Yönetici girişi')->assertSee('name="password"', false);

        // Hatalı girişte aynı sayfaya dönülür ve kutu hata mesajıyla açık gelir
        $this->from('/hakkimizda')->post('/yonetim/giris', ['email' => 'av@example.com', 'password' => 'yanlis'])
            ->assertRedirect('/hakkimizda');
        $this->get('/hakkimizda')->assertSee('giris-kutusu acik', false)->assertSee('E-posta adresi veya şifre hatalı.');

        $this->from('/hakkimizda')->post('/yonetim/giris', ['email' => 'av@example.com', 'password' => 'gizli-sifre-123'])
            ->assertRedirect('/yonetim');
        $this->get('/hakkimizda')->assertSee('Yönetim paneline git')->assertDontSee('name="password"', false);
    }

    public function test_panel_sayfalari_acilir(): void
    {
        $randevu = Randevu::create(['ad_soyad' => 'Ayşe Yılmaz', 'telefon' => '05321112233', 'baslangic' => '2026-10-19 15:00', 'konu' => 'İş Hukuku']);
        $etkinlik = Etkinlik::create(['baslik' => 'İşe iade duruşması', 'tur' => 'durusma', 'baslangic' => '2026-10-22 10:00']);

        $this->actingAs($this->yonetici);
        $this->get('/yonetim')->assertOk()->assertSee('Ayşe Yılmaz')->assertSee('İşe iade duruşması')->assertSee('Bugün yapılacaklar')
            ->assertSee('Duruşma ve randevular')
            ->assertSee('"hafta":{"etiket":"Bu hafta","randevu":1,"durusma":1}', false);

        // Panelden hızlı görev eklenince panelde kalınır ve görev listede görünür
        $this->from('/yonetim')->post('/yonetim/gorevler', ['baslik' => 'Harç makbuzunu tara', 'tarih' => '2026-10-19'])
            ->assertRedirect('/yonetim');
        $this->get('/yonetim')->assertSeeInOrder(['Bugün yapılacaklar', 'Harç makbuzunu tara', 'Ayşe Yılmaz']);
        $this->get('/yonetim/randevular')->assertOk()->assertSee('Ayşe Yılmaz');
        $this->get('/yonetim/randevular?durum=iptal')->assertOk()->assertDontSee('05321112233');
        $this->get("/yonetim/randevular/{$randevu->id}/edit")->assertOk();
        $this->get('/yonetim/randevular/create?tarih=2026-10-20 10:00')->assertOk();
        $this->get('/yonetim/takvim')->assertOk();
        $this->get("/yonetim/etkinlikler/{$etkinlik->id}/edit")->assertOk();
        $this->get('/yonetim/etkinlikler/create?tur=sure&tarih=2026-10-20&tum_gun=1')->assertOk();
    }

    public function test_durum_degisir_ve_saatsiz_basvuru_onaylanamaz(): void
    {
        $randevu = Randevu::create(['ad_soyad' => 'A', 'baslangic' => '2026-10-20 10:00']);
        $basvuru = Randevu::create(['ad_soyad' => 'B', 'tip' => 'basvuru']);

        $this->actingAs($this->yonetici);
        $this->patch("/yonetim/randevular/{$randevu->id}/durum", ['durum' => 'onaylandi']);
        $this->assertSame('onaylandi', $randevu->fresh()->durum);

        $this->patch("/yonetim/randevular/{$basvuru->id}/durum", ['durum' => 'onaylandi'])
            ->assertRedirect("/yonetim/randevular/{$basvuru->id}/edit");
        $this->assertSame('beklemede', $basvuru->fresh()->durum);
    }

    public function test_randevu_ve_etkinlik_eklenir_takvimde_gorunur_ve_tasinir(): void
    {
        $this->actingAs($this->yonetici);

        $this->post('/yonetim/randevular', [
            'tip' => 'randevu', 'ad_soyad' => 'Mehmet Kaya', 'tarih' => '2026-10-21', 'saat' => '11:00',
            'sure' => 30, 'gorusme_sekli' => 'telefon', 'durum' => 'onaylandi',
        ])->assertSessionHasNoErrors();

        $this->post('/yonetim/etkinlikler', [
            'baslik' => 'İstinaf süresi', 'tur' => 'sure', 'tarih' => '2026-10-23', 'tum_gun' => '1',
        ])->assertRedirect('/yonetim/takvim');

        $olaylar = collect($this->getJson('/yonetim/takvim/veriler?start=2026-10-01&end=2026-11-01')->assertOk()->json());
        $this->assertCount(2, $olaylar);
        $sure = $olaylar->firstWhere('title', 'İstinaf süresi');
        $this->assertTrue($sure['allDay']);
        $this->assertSame('2026-10-24', $sure['end']);

        $randevu = Randevu::sole();
        $this->patchJson('/yonetim/takvim/tasi', [
            'id' => 'r'.$randevu->id, 'start' => '2026-10-22T11:00:00.000Z', 'end' => '2026-10-22T12:00:00.000Z', 'allDay' => false,
        ])->assertOk();

        $randevu->refresh();
        $this->assertSame('2026-10-22 14:00', $randevu->baslangic->format('Y-m-d H:i'));
        $this->assertSame(60, $randevu->sure);
    }

    public function test_tamamlanmamis_gecmis_sure_panelde_uyari_verir(): void
    {
        Etkinlik::create(['baslik' => 'Cevap dilekçesi', 'tur' => 'sure', 'baslangic' => '2026-10-15 00:00', 'tum_gun' => true]);

        $this->actingAs($this->yonetici)->get('/yonetim')->assertSee('Tarihi geçmiş')->assertSee('Cevap dilekçesi');
    }

    public function test_yapilacaklar_listesi_saat_saat_ve_gorev_eklenir(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-19 11:20', 'Europe/Istanbul'));
        Randevu::create(['ad_soyad' => 'Ayşe Yılmaz', 'baslangic' => '2026-10-19 14:00', 'durum' => 'onaylandi']);
        Randevu::create(['ad_soyad' => 'İptal Eden', 'baslangic' => '2026-10-19 15:00', 'durum' => 'iptal']);
        Etkinlik::create(['baslik' => 'İstinaf son gün', 'tur' => 'sure', 'baslangic' => '2026-10-19 00:00', 'tum_gun' => true]);

        $this->actingAs($this->yonetici);
        $this->post('/yonetim/gorevler', ['baslik' => 'Dilekçeyi yaz', 'tarih' => '2026-10-19', 'saat' => '16:30'])
            ->assertRedirect('/yonetim/yapilacaklar?tarih=2026-10-19');
        $this->post('/yonetim/gorevler', ['baslik' => 'Müvekkili ara', 'tarih' => '2026-10-19']);

        $sayfa = $this->get('/yonetim/yapilacaklar')->assertOk()
            ->assertSeeInOrder(['Gün boyu', 'İstinaf son gün', 'Müvekkili ara', '14:00', 'Ayşe Yılmaz', '16:00', 'Dilekçeyi yaz'])
            ->assertDontSee('İptal Eden')
            ->assertSee('4 işten 0 tanesi tamamlandı');

        $gorev = Gorev::where('baslik', 'Dilekçeyi yaz')->sole();
        $this->patch("/yonetim/gorevler/{$gorev->id}/tamamla");
        $this->assertTrue($gorev->fresh()->tamamlandiMi());
        $this->patch("/yonetim/gorevler/{$gorev->id}/tamamla");
        $this->assertFalse($gorev->fresh()->tamamlandiMi());

        $this->delete("/yonetim/gorevler/{$gorev->id}");
        $this->assertSame(1, Gorev::count());
    }

    public function test_gecmis_saate_ve_gecmis_gune_gorev_eklenmez(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-19 11:20', 'Europe/Istanbul'));
        $this->actingAs($this->yonetici);

        $this->post('/yonetim/gorevler', ['baslik' => 'Geç kaldım', 'tarih' => '2026-10-19', 'saat' => '10:30'])
            ->assertSessionHasErrors(['saat' => 'Geçmiş bir saate görev eklenemez. Şu an saat 11:20.']);
        $this->post('/yonetim/gorevler', ['baslik' => 'Dün', 'tarih' => '2026-10-18'])
            ->assertSessionHasErrors(['saat' => 'Geçmiş bir güne görev eklenemez.']);
        $this->assertSame(0, Gorev::count());

        // Bugün saatsiz ve şu anki dakikaya görev eklenebilir
        $this->post('/yonetim/gorevler', ['baslik' => 'Gün boyu', 'tarih' => '2026-10-19'])->assertSessionHasNoErrors();
        $this->post('/yonetim/gorevler', ['baslik' => 'Şimdi', 'tarih' => '2026-10-19', 'saat' => '11:20'])->assertSessionHasNoErrors();
        $this->assertSame(2, Gorev::count());

        $this->get('/yonetim/yapilacaklar')->assertSee('min="11:20"', false)->assertDontSee('saat=10%3A00');
        $this->get('/yonetim/yapilacaklar?tarih=2026-10-18')->assertSee('bu güne yeni görev eklenemez')->assertDontSee('id="gorevEkle"', false);
    }

    public function test_saati_gecen_randevu_kendiliginden_tamamlanir(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-18 12:00', 'Europe/Istanbul'));   // randevular bir gün önce alınmış
        $gecen = Randevu::create(['ad_soyad' => 'A', 'baslangic' => '2026-10-19 07:00', 'durum' => 'onaylandi']);
        $suren = Randevu::create(['ad_soyad' => 'B', 'baslangic' => '2026-10-19 07:45', 'sure' => 30, 'durum' => 'beklemede']);
        $iptal = Randevu::create(['ad_soyad' => 'C', 'baslangic' => '2026-10-19 06:00', 'durum' => 'iptal']);
        Carbon::setTestNow(Carbon::parse('2026-10-19 08:00', 'Europe/Istanbul'));

        $this->actingAs($this->yonetici)->get('/yonetim/yapilacaklar')->assertOk();

        $this->assertSame('tamamlandi', $gecen->fresh()->durum);
        $this->assertSame('beklemede', $suren->fresh()->durum);   // 08:15'e kadar sürüyor
        $this->assertSame('iptal', $iptal->fresh()->durum);

        Carbon::setTestNow(Carbon::parse('2026-10-19 08:20', 'Europe/Istanbul'));
        $this->artisan('randevular:tamamla')->assertSuccessful();
        $this->assertSame('tamamlandi', $suren->fresh()->durum);

        // Elle geri alınabilir ve bir daha kendiliğinden tamamlanmaz
        $this->patch("/yonetim/randevular/{$gecen->id}/tamamla");
        $this->assertSame('onaylandi', $gecen->fresh()->durum);
        $this->get('/yonetim/yapilacaklar')->assertOk();
        $this->assertSame('onaylandi', $gecen->fresh()->durum);
    }

    public function test_gorevlere_alt_baslik_eklenir_ve_birlikte_tamamlanir(): void
    {
        $this->actingAs($this->yonetici);
        $gorev = Gorev::create(['baslik' => 'İstinaf dilekçesi', 'tarih' => '2026-10-19', 'saat' => '13:00']);

        $this->post("/yonetim/gorevler/{$gorev->id}/alt", ['baslik' => 'Kararı oku']);
        $this->post("/yonetim/gorevler/{$gorev->id}/alt", ['baslik' => 'Taslağı yaz']);
        [$oku, $yaz] = $gorev->altGorevler()->get()->all();

        // Alt başlığın altına eklenen madde üst göreve bağlanır
        $this->post("/yonetim/gorevler/{$oku->id}/alt", ['baslik' => 'Ekleri tarat']);
        $this->assertSame(3, $gorev->altGorevler()->count());

        $this->get('/yonetim/yapilacaklar?tarih=2026-10-19')->assertOk()
            ->assertSeeInOrder(['İstinaf dilekçesi', '0/3', 'Kararı oku', 'Taslağı yaz', 'Ekleri tarat', 'Alt başlık ekle'])
            ->assertSee('1 işten 0 tanesi tamamlandı');

        // Hepsi bitince üst görev de tamamlanır
        foreach ($gorev->altGorevler()->get() as $alt) {
            $this->patch("/yonetim/gorevler/{$alt->id}/tamamla");
        }
        $this->assertTrue($gorev->fresh()->tamamlandiMi());

        // Biri geri alınınca üst görev yeniden açılır
        $this->patch("/yonetim/gorevler/{$yaz->id}/tamamla");
        $this->assertFalse($gorev->fresh()->tamamlandiMi());

        // Üst görev işaretlenince alt başlıklar da tamamlanır; silinince alt başlıklar da gider
        $this->patch("/yonetim/gorevler/{$gorev->id}/tamamla");
        $this->assertSame(0, Gorev::whereNull('tamamlandi_at')->count());
        $this->delete("/yonetim/gorevler/{$gorev->id}");
        $this->assertSame(0, Gorev::count());
    }
}
