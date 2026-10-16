<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteAyarlariTest extends TestCase
{
    use RefreshDatabase;

    public function test_ayarlar_sayfasi_yalnizca_yoneticiye_acik(): void
    {
        $this->get('/yonetim/ayarlar')->assertRedirect('/yonetim/giris');
        $this->get('/yonetim/ayarlar/seo')->assertRedirect('/yonetim/giris');
        $this->actingAs(User::factory()->create());
        $this->get('/yonetim/ayarlar')->assertOk()->assertSee('Büro bilgileri')->assertSee('SEO');
        $this->get('/yonetim/ayarlar/seo')->assertOk()->assertSee('Arama motoru görünümü');
    }

    public function test_buro_bilgileri_sitenin_her_yerinde_guncellenir(): void
    {
        $this->actingAs(User::factory()->create())->put('/yonetim/ayarlar', [
            'site' => [
                'telefon' => '0312 555 44 33', 'whatsapp' => '0532 111 22 33', 'eposta' => 'iletisim@otorite.av.tr',
                'sosyal' => ['instagram' => 'https://instagram.com/otoritehukuk'],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->get('/iletisim')
            ->assertSee('tel:+903125554433', false)
            ->assertSee('0312 555 44 33')
            ->assertSee('iletisim@otorite.av.tr')
            ->assertSee('https://wa.me/905321112233', false)
            ->assertSee('https://instagram.com/otoritehukuk', false);

        // Boş bırakılan alan config dosyasındaki değerde kalır
        $this->assertSame('Çankaya / Ankara', config('site.adres_2'));

        $this->put('/yonetim/ayarlar', ['site' => ['eposta' => 'gecersiz', 'sosyal' => ['linkedin' => 'linkedin']]])
            ->assertSessionHasErrors(['site.eposta', 'site.sosyal.linkedin']);
    }

    public function test_kaydedilen_seo_ayarlari_sayfalara_yansir(): void
    {
        $this->actingAs(User::factory()->create())->put('/yonetim/ayarlar/seo', [
            'seo'   => [
                'site_adi' => 'Otorite Hukuk Ankara', 'aciklama' => 'Varsayılan açıklama',
                'anahtar_kelimeler' => 'ankara avukat', 'indeksle' => '1',
                'google_dogrulama' => 'abc123_XYZ', 'analytics_id' => 'G-ABC12345',
            ],
            'sayfa' => ['hakkimizda' => ['baslik' => 'Av. Yahya Kazık kimdir?', 'aciklama' => 'Özel hakkımızda açıklaması']],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->get('/hakkimizda')
            ->assertSee('<title>Av. Yahya Kazık kimdir? &mdash; Otorite Hukuk Ankara</title>', false)
            ->assertSee('content="Özel hakkımızda açıklaması"', false)
            ->assertSee('<meta name="keywords" content="ankara avukat">', false)
            ->assertSee('<meta name="google-site-verification" content="abc123_XYZ">', false)
            ->assertSee('gtag/js?id=G-ABC12345', false)
            ->assertSee('content="index, follow"', false);

        // Özel değeri olmayan sayfa kendi başlığını kullanır
        $this->get('/sss')->assertSee('<title>Sıkça Sorulan Sorular &mdash; Otorite Hukuk Ankara</title>', false);
    }

    public function test_indeksleme_kapatilabilir_ve_hatali_kodlar_reddedilir(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/yonetim/ayarlar/seo', ['seo' => ['analytics_id' => 'UA-123', 'google_dogrulama' => '<meta name="x">']])
            ->assertSessionHasErrors(['seo.analytics_id', 'seo.google_dogrulama']);

        $this->put('/yonetim/ayarlar/seo', ['seo' => ['indeksle' => '0']])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('content="noindex, nofollow"', false);
    }

    public function test_site_haritasi(): void
    {
        $this->get('/sitemap.xml')->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('hakkimizda').'</loc>', false)
            ->assertSee(route('yazi', 'ise-iade-davasinda-surec-ve-sureler'), false)
            ->assertDontSee('yonetim');
    }
}
