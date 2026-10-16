<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Müvekkil başvurusu veya randevusu.
 *
 * Sitedeki başvuru sihirbazından, iletişim formundan ya da yönetim
 * panelinden elle eklenir. Takvimde baslangic alanı dolu olanlar görünür.
 */
class Randevu extends Model
{
    protected $table = 'randevular';

    protected $fillable = [
        'tip', 'konu', 'kurum', 'aciklama', 'ad_soyad', 'telefon', 'eposta',
        'baslangic', 'sure', 'gorusme_sekli', 'durum', 'kaynak', 'notlar',
    ];

    protected $attributes = [
        'tip' => 'randevu', 'durum' => 'beklemede', 'kaynak' => 'site',
        'sure' => 30, 'gorusme_sekli' => 'yuz_yuze',
    ];

    public const TIPLER = [
        'randevu' => 'Randevu',
        'basvuru' => 'Başvuru',
        'mesaj'   => 'Mesaj',
    ];

    public const DURUMLAR = [
        'beklemede'  => 'Beklemede',
        'onaylandi'  => 'Onaylandı',
        'tamamlandi' => 'Tamamlandı',
        'iptal'      => 'İptal',
    ];

    public const GORUSME_SEKILLERI = [
        'yuz_yuze' => 'Büroda yüz yüze',
        'telefon'  => 'Telefonla',
        'online'   => 'Online (görüntülü)',
    ];

    protected function casts(): array
    {
        return ['baslangic' => 'datetime', 'otomatik_tamamla' => 'boolean'];
    }

    public function bitis(): ?Carbon
    {
        return $this->baslangic?->copy()->addMinutes($this->sure);
    }

    /** Takvimde yer kaplayan (iptal edilmemiş, zamanı belli) kayıtlar. */
    public function scopeAktif(Builder $sorgu): Builder
    {
        return $sorgu->whereIn('durum', ['beklemede', 'onaylandi', 'tamamlandi'])
                     ->whereNotNull('baslangic');
    }

    /**
     * Saati geçmiş randevuları "Tamamlandı" yapar. Bekleyen ve onaylı
     * randevular etkilenir; iptal edilenler ve tamamlandı işareti elle geri
     * alınanlar (otomatik_tamamla = false) olduğu gibi kalır.
     * Yönetim sayfaları açılırken ve zamanlanmış görevle her dakika çalışır.
     */
    public static function gecmisleriTamamla(): int
    {
        $simdi = Carbon::now();

        $gecenler = self::query()
            ->whereIn('durum', ['beklemede', 'onaylandi'])
            ->where('otomatik_tamamla', true)
            ->whereNotNull('baslangic')
            ->where('baslangic', '<=', $simdi)
            ->get()
            ->filter(fn (self $r) => $r->bitis()->lte($simdi));

        if ($gecenler->isNotEmpty()) {
            self::whereKey($gecenler->modelKeys())->update(['durum' => 'tamamlandi']);
        }

        return $gecenler->count();
    }

    /**
     * Saati geçmiş bir randevu elle açık (beklemede/onaylı) bırakılıyorsa
     * true döner; bu durumda kendiliğinden tamamlanma kapatılır.
     */
    public function elleYenidenAcildi(): bool
    {
        return in_array($this->durum, ['beklemede', 'onaylandi'], true)
            && $this->baslangic !== null
            && $this->bitis()->lte(Carbon::now());
    }

    public function tipEtiketi(): string
    {
        return self::TIPLER[$this->tip] ?? $this->tip;
    }

    public function durumEtiketi(): string
    {
        return self::DURUMLAR[$this->durum] ?? $this->durum;
    }

    public function gorusmeSekliEtiketi(): string
    {
        return self::GORUSME_SEKILLERI[$this->gorusme_sekli] ?? $this->gorusme_sekli;
    }
}
