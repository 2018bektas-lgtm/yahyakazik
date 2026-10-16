<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Yapılacaklar listesindeki büro içi görev (ör. "Bilirkişi raporuna itiraz yaz").
 * Bir görevin altına işaretlenebilir alt başlıklar eklenebilir; alt başlıklar
 * da aynı tabloda, ust_gorev_id ile bağlı olarak tutulur.
 */
class Gorev extends Model
{
    protected $table = 'gorevler';

    protected $fillable = ['ust_gorev_id', 'baslik', 'tarih', 'saat', 'aciklama', 'tamamlandi_at'];

    protected function casts(): array
    {
        return [
            'tarih'         => 'date',
            'tamamlandi_at' => 'datetime',
        ];
    }

    public function altGorevler(): HasMany
    {
        return $this->hasMany(self::class, 'ust_gorev_id')->orderBy('id');
    }

    public function ustGorev(): BelongsTo
    {
        return $this->belongsTo(self::class, 'ust_gorev_id');
    }

    public function tamamlandiMi(): bool
    {
        return $this->tamamlandi_at !== null;
    }

    /** '14:30:00' yerine '14:30' */
    public function saatMetni(): ?string
    {
        return $this->saat ? substr($this->saat, 0, 5) : null;
    }
}
