<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Support\SiteAyarlari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Yönetim paneli > Ayarlar > Site ayarları: büro adı, iletişim bilgileri ve
 * sosyal medya hesapları.
 *
 * Kaydedilen değerler config/site.php içindeki karşılıklarının yerine geçer
 * (AppServiceProvider), böylece sitenin her yerinde güncellenir. Boş
 * bırakılan alan config dosyasındaki değere döner.
 */
class AyarlarController extends Controller
{
    /** Formdaki alanlar: config/site.php anahtarı => doğrulama kuralları. */
    private const ALANLAR = [
        'unvan'             => ['nullable', 'string', 'max:150'],
        'kisa_unvan'        => ['nullable', 'string', 'max:60'],
        'ad'                => ['nullable', 'string', 'max:80'],
        'slogan'            => ['nullable', 'string', 'max:150'],
        'telefon'           => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\s]+$/'],
        'whatsapp'          => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\s]+$/'],
        'eposta'            => ['nullable', 'email', 'max:150'],
        'adres'             => ['nullable', 'string', 'max:200'],
        'adres_2'           => ['nullable', 'string', 'max:120'],
        'calisma_saatleri'  => ['nullable', 'string', 'max:120'],
        'sosyal.linkedin'   => ['nullable', 'url', 'max:255'],
        'sosyal.instagram'  => ['nullable', 'url', 'max:255'],
        'sosyal.twitter'    => ['nullable', 'url', 'max:255'],
        'sosyal.facebook'   => ['nullable', 'url', 'max:255'],
    ];

    public function __construct(private SiteAyarlari $ayarlar) {}

    public function index(): View
    {
        return view('yonetim.ayarlar', ['ayar' => $this->ayarlar]);
    }

    public function update(Request $istek): RedirectResponse
    {
        $kurallar = [];
        foreach (self::ALANLAR as $alan => $kural) {
            $kurallar["site.{$alan}"] = $kural;
        }

        $istek->validate($kurallar, [
            'site.telefon.regex'  => 'Telefon numarasında yalnızca rakam, boşluk, + ve parantez olabilir.',
            'site.whatsapp.regex' => 'WhatsApp numarasında yalnızca rakam, boşluk, + ve parantez olabilir.',
            'site.eposta.email'   => 'Geçerli bir e-posta adresi yazın.',
            'site.sosyal.*.url'   => 'Sosyal medya adresi https:// ile başlayan tam bir bağlantı olmalı.',
        ]);

        $degerler = [];
        foreach (array_keys(self::ALANLAR) as $alan) {
            $degerler["site.{$alan}"] = $istek->input("site.{$alan}");
        }

        $this->ayarlar->kaydet($degerler);

        return back()->with('bilgi', 'Site ayarları kaydedildi.');
    }
}
