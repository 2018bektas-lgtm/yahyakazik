<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * Büro avukatı: Hakkımızda > Ekibimiz kartı ve /ekibimiz/{slug} profil sayfası.
 */
class Avukat extends Model
{
    protected $table = 'avukatlar';

    protected $fillable = [
        'slug', 'ad', 'unvan', 'baro', 'ozet', 'biyografi', 'uzmanliklar', 'ozgecmis',
        'fotograf', 'eposta', 'linkedin', 'sira', 'yayinda',
    ];

    protected $attributes = ['yayinda' => true, 'sira' => 0];

    protected function casts(): array
    {
        return ['uzmanliklar' => 'array', 'ozgecmis' => 'array', 'yayinda' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeYayinda(Builder $sorgu): Builder
    {
        return $sorgu->where('yayinda', true)->orderBy('sira')->orderBy('id');
    }

    /**
     * Sitede gösterilecek avukatlar. Tablo henüz oluşturulmadıysa (migrate
     * çalışmadıysa) sayfa hata vermesin diye boş liste döner.
     */
    public static function ekip(): Collection
    {
        try {
            return static::yayinda()->get();
        } catch (Throwable) {
            return collect();
        }
    }

    /** "Av. Yahya Kazık" => "YK"; fotoğraf yoksa kartta bu baş harfler görünür. */
    public function basHarfler(): string
    {
        return Str::of($this->ad)->replaceMatches('/^(Av\.|Stj\. Av\.|Arb\.)\s*/u', '')
            ->explode(' ')->filter()->map(fn ($k) => mb_substr($k, 0, 1))
            ->take(2)->join('');
    }

    /** @return array<int, string> */
    public function paragraflar(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', (string) $this->biyografi))));
    }
}
