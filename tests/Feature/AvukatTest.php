<?php

namespace Tests\Feature;

use App\Models\Avukat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AvukatTest extends TestCase
{
    use RefreshDatabase;

    public function test_kurucu_avukat_ekipte_ve_profil_sayfasinda(): void
    {
        $this->get('/hakkimizda')->assertOk()
            ->assertSee('Dosyanızla ilgilenecek')
            ->assertSee('Av. Yahya Kazık')
            ->assertSee(route('avukat', 'yahya-kazik'), false);

        $this->get('/ekibimiz/yahya-kazik')->assertOk()
            ->assertSee('Kurucu Avukat &amp; Arabulucu', false)
            ->assertSee('Millî Savunma Bakanlığı Müfettişliği')
            ->assertSee('YK');

        $this->get('/av-yahya-kazik')->assertRedirect('/ekibimiz/yahya-kazik');
        $this->get('/sitemap.xml')->assertSee(route('avukat', 'yahya-kazik'), false);
    }

    public function test_yeni_avukat_eklenir_duzenlenir_ve_gizlenir(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/yonetim/avukatlar', [
            'ad' => 'Av. Ayşe Yıldız', 'unvan' => 'Avukat', 'ozet' => 'İş hukuku dosyaları.',
            'biyografi' => "Birinci paragraf.\n\nİkinci paragraf.",
            'uzmanliklar' => "Asker Alma Hukuku\n\n- Aile Hukuku\n",
            'ozgecmis' => [
                ['donem' => '2020', 'baslik' => 'Hukuk Fakültesi', 'metin' => 'Mezun oldu.'],
                ['donem' => '', 'baslik' => '', 'metin' => ''],
            ],
            'sira' => 3, 'yayinda' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect('/yonetim/avukatlar');

        $avukat = Avukat::where('slug', 'ayse-yildiz')->sole();
        $this->assertSame(['Asker Alma Hukuku', 'Aile Hukuku'], $avukat->uzmanliklar);
        $this->assertCount(1, $avukat->ozgecmis);
        $this->assertSame(['Birinci paragraf.', 'İkinci paragraf.'], $avukat->paragraflar());
        $this->assertSame('AY', $avukat->basHarfler());

        $this->get('/hakkimizda')->assertSeeInOrder(['Av. Yahya Kazık', 'Av. Ayşe Yıldız']);
        $this->get('/ekibimiz/ayse-yildiz')->assertOk()
            ->assertSee(route('faaliyet-alani', 'asker-alma-hukuku'), false)   // uzmanlık faaliyet alanına bağlanır
            ->assertSee('Diğer');

        // Aynı adres ikinci kez kullanılamaz
        $this->post('/yonetim/avukatlar', ['ad' => 'Av. Ayşe Yıldız'])->assertSessionHasErrors('slug');

        $this->put("/yonetim/avukatlar/{$avukat->slug}", ['ad' => 'Av. Ayşe Yıldız', 'slug' => 'ayse-yildiz', 'yayinda' => '0'])
            ->assertSessionHasNoErrors();
        $this->get('/ekibimiz/ayse-yildiz')->assertNotFound();
        $this->get('/hakkimizda')->assertDontSee('Av. Ayşe Yıldız');

        $this->delete('/yonetim/avukatlar/ayse-yildiz')->assertRedirect('/yonetim/avukatlar');
        $this->assertSame(1, Avukat::count());
    }

    public function test_fotograf_yuklenir_ve_kaldirilir(): void
    {
        $this->actingAs(User::factory()->create());

        $this->put('/yonetim/avukatlar/yahya-kazik', [
            'ad' => 'Av. Yahya Kazık', 'slug' => 'yahya-kazik', 'yayinda' => '1',
            'fotograf' => UploadedFile::fake()->image('portre.jpg', 800, 1000),
        ])->assertSessionHasNoErrors();

        $yol = Avukat::first()->fotograf;
        $this->assertStringStartsWith('uploads/avukatlar/yahya-kazik-', $yol);
        $this->assertFileExists(public_path($yol));
        $this->get('/ekibimiz/yahya-kazik')->assertSee(asset($yol), false);

        $this->put('/yonetim/avukatlar/yahya-kazik', ['ad' => 'Av. Yahya Kazık', 'slug' => 'yahya-kazik', 'yayinda' => '1', 'fotograf_kaldir' => '1']);
        $this->assertNull(Avukat::first()->fotograf);
        $this->assertFileDoesNotExist(public_path($yol));
    }

    public function test_panel_sayfalari_yalnizca_yoneticiye(): void
    {
        $this->get('/yonetim/avukatlar')->assertRedirect('/yonetim/giris');
        $this->actingAs(User::factory()->create())->get('/yonetim/avukatlar')->assertOk()->assertSee('Yeni avukat ekle');
        $this->get('/yonetim/avukatlar/create')->assertOk();
        $this->get('/yonetim/avukatlar/yahya-kazik/edit')->assertOk()->assertSee('Millî Savunma Bakanlığı Müfettişliği');
    }
}
