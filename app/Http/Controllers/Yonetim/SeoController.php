<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Support\SiteAyarlari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Yönetim paneli > Ayarlar > SEO: arama motoru ayarları.
 */
class SeoController extends Controller
{
    public function __construct(private SiteAyarlari $ayarlar) {}

    public function index(): View
    {
        return view('yonetim.seo', [
            'ayar'     => $this->ayarlar,
            'sayfalar' => SiteAyarlari::SAYFALAR,
        ]);
    }

    public function update(Request $istek): RedirectResponse
    {
        $kurallar = [
            'seo.site_adi'           => ['nullable', 'string', 'max:120'],
            'seo.aciklama'           => ['nullable', 'string', 'max:320'],
            'seo.anahtar_kelimeler'  => ['nullable', 'string', 'max:500'],
            'seo.indeksle'           => ['nullable', 'boolean'],
            'seo.google_dogrulama'   => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9_\-]+$/'],
            'seo.bing_dogrulama'     => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9_\-]+$/'],
            'seo.analytics_id'       => ['nullable', 'string', 'regex:/^G-[A-Z0-9]{4,20}$/'],
            'og_gorsel'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'og_gorsel_kaldir'       => ['nullable', 'boolean'],
        ];

        foreach (array_keys(SiteAyarlari::SAYFALAR) as $rota) {
            $kurallar["sayfa.{$rota}.baslik"]   = ['nullable', 'string', 'max:120'];
            $kurallar["sayfa.{$rota}.aciklama"] = ['nullable', 'string', 'max:320'];
        }

        $istek->validate($kurallar, [
            'seo.google_dogrulama.regex' => 'Google doğrulama kodunda yalnızca harf, rakam, - ve _ olabilir. Meta etiketinin tamamını değil, content="..." içindeki kodu yapıştırın.',
            'seo.bing_dogrulama.regex'   => 'Bing doğrulama kodunda yalnızca harf, rakam, - ve _ olabilir.',
            'seo.analytics_id.regex'     => 'Google Analytics ölçüm kimliği G-XXXXXXX biçiminde olmalı.',
            'og_gorsel.image'            => 'Paylaşım görseli bir resim dosyası olmalı.',
            'og_gorsel.max'              => 'Paylaşım görseli en fazla 4 MB olabilir.',
        ]);

        // Form alanı adlarındaki noktalar dizi olarak gelir; düz anahtarlara çeviriyoruz
        $degerler = [
            'seo.site_adi'          => $istek->input('seo.site_adi'),
            'seo.aciklama'          => $istek->input('seo.aciklama'),
            'seo.anahtar_kelimeler' => $istek->input('seo.anahtar_kelimeler'),
            'seo.indeksle'          => $istek->boolean('seo.indeksle') ? '1' : '0',
            'seo.google_dogrulama'  => $istek->input('seo.google_dogrulama'),
            'seo.bing_dogrulama'    => $istek->input('seo.bing_dogrulama'),
            'seo.analytics_id'      => $istek->input('seo.analytics_id'),
        ];

        foreach (array_keys(SiteAyarlari::SAYFALAR) as $rota) {
            $degerler["sayfa.{$rota}.baslik"]   = $istek->input("sayfa.{$rota}.baslik");
            $degerler["sayfa.{$rota}.aciklama"] = $istek->input("sayfa.{$rota}.aciklama");
        }

        if ($istek->boolean('og_gorsel_kaldir')) {
            $degerler['seo.og_gorsel'] = null;
        }

        if ($istek->hasFile('og_gorsel')) {
            $dosya = $istek->file('og_gorsel');
            $ad = 'paylasim-'.Str::lower(Str::random(8)).'.'.$dosya->extension();
            File::ensureDirectoryExists(public_path('uploads/seo'));
            $dosya->move(public_path('uploads/seo'), $ad);
            $degerler['seo.og_gorsel'] = 'uploads/seo/'.$ad;
        }

        $this->ayarlar->kaydet($degerler);

        return back()->with('bilgi', 'SEO ayarları kaydedildi.');
    }
}
