<?php

namespace App\Support;

use App\Models\Ayar;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Yönetim panelinden değiştirilen site ayarları: büro ve iletişim bilgileri
 * (site.*), SEO başlıkları, açıklamalar, doğrulama kodları vb.
 *
 * Tüm ayarlar tek seferde okunup önbelleğe alınır; kaydedilince önbellek
 * temizlenir. Tablo henüz oluşturulmamışsa (migrate çalışmadıysa) site
 * hata vermez, varsayılanlarla açılır.
 */
class SiteAyarlari
{
    private const ONBELLEK = 'site-ayarlari';

    /** SEO başlığı ve açıklaması panelden değiştirilebilen sayfalar (rota adı => etiket). */
    public const SAYFALAR = [
        'home'              => 'Ana Sayfa',
        'hakkimizda'        => 'Hakkımızda',
        'faaliyet-alanlari' => 'Faaliyet Alanlarımız',
        'blog'              => 'Makalelerimiz',
        'sss'               => 'S.S.S.',
        'iletisim'          => 'İletişim',
        'basvuru'           => 'Başvuru ve Randevu',
    ];

    private ?array $degerler = null;

    public function get(string $anahtar, ?string $varsayilan = null): ?string
    {
        $deger = $this->tumu()[$anahtar] ?? null;

        return filled($deger) ? $deger : $varsayilan;
    }

    public function acikMi(string $anahtar, bool $varsayilan = true): bool
    {
        $deger = $this->tumu()[$anahtar] ?? null;

        return $deger === null ? $varsayilan : $deger === '1';
    }

    /**
     * Panelden girilen büro bilgilerini (site.* anahtarları) config('site')
     * değerlerinin yerine koyar. Telefon ve WhatsApp için bağlantıda
     * kullanılan rakam biçimi buradan türetilir.
     */
    public function siteBilgileriniUygula(): void
    {
        $bilgiler = collect($this->tumu())
            ->filter(fn ($deger, $anahtar) => str_starts_with($anahtar, 'site.') && filled($deger));

        if ($bilgiler->isEmpty()) {
            return;
        }

        config($bilgiler->all());

        if ($bilgiler->has('site.telefon')) {
            config(['site.telefon_link' => '+'.self::uluslararasiNumara($bilgiler['site.telefon'])]);
        }
        if ($bilgiler->has('site.whatsapp')) {
            config(['site.whatsapp' => self::uluslararasiNumara($bilgiler['site.whatsapp'])]);
        }
    }

    /** "0542 682 55 16" ya da "+90 542 682 55 16" => "905426825516" */
    public static function uluslararasiNumara(string $numara): string
    {
        $rakamlar = preg_replace('/\D/', '', $numara);

        return match (true) {
            str_starts_with($rakamlar, '00') => substr($rakamlar, 2),
            str_starts_with($rakamlar, '0')  => '90'.substr($rakamlar, 1),
            strlen($rakamlar) === 10        => '90'.$rakamlar,
            default                          => $rakamlar,
        };
    }

    /** @param array<string, string|null> $degerler */
    public function kaydet(array $degerler): void
    {
        foreach ($degerler as $anahtar => $deger) {
            Ayar::updateOrCreate(['anahtar' => $anahtar], ['deger' => $deger]);
        }

        Cache::forget(self::ONBELLEK);
        $this->degerler = null;
        $this->siteBilgileriniUygula();
    }

    /** @return array<string, string|null> */
    public function tumu(): array
    {
        if ($this->degerler !== null) {
            return $this->degerler;
        }

        try {
            return $this->degerler = Cache::rememberForever(self::ONBELLEK,
                fn () => Ayar::pluck('deger', 'anahtar')->all());
        } catch (Throwable) {
            return $this->degerler = [];
        }
    }
}
