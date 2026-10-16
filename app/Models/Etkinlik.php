<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Takip takvimindeki büro içi kayıt: duruşma, süre sonu, toplantı vb.
 *
 * "Müsait değil" türündeki kayıtlar sitede müvekkillere sunulan boş
 * randevu saatlerini kapatır.
 */
class Etkinlik extends Model
{
    protected $table = 'etkinlikler';

    protected $fillable = [
        'baslik', 'tur', 'baslangic', 'bitis', 'tum_gun', 'yer', 'dosya_no',
        'aciklama', 'tamamlandi',
    ];

    public const TURLER = [
        'durusma'      => 'Duruşma',
        'sure'         => 'Süre / Son gün',
        'gorusme'      => 'Görüşme',
        'toplanti'     => 'Toplantı',
        'musait_degil' => 'Müsait değil',
        'diger'        => 'Diğer',
    ];

    protected function casts(): array
    {
        return [
            'baslangic'  => 'datetime',
            'bitis'      => 'datetime',
            'tum_gun'    => 'boolean',
            'tamamlandi' => 'boolean',
        ];
    }

    public function turEtiketi(): string
    {
        return self::TURLER[$this->tur] ?? $this->tur;
    }

    /** Kaydın kapladığı zamanın sonu; bitiş girilmemişse bir saat sayılır. */
    public function bitisZamani(): Carbon
    {
        if ($this->tum_gun) {
            return ($this->bitis ?? $this->baslangic)->copy()->endOfDay();
        }

        return $this->bitis ?? $this->baslangic->copy()->addHour();
    }
}
