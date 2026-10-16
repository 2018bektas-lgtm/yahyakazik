<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Site ayarlarının tek bir anahtar-değer kaydı. Erişim için App\Support\SiteAyarlari kullanılır. */
class Ayar extends Model
{
    protected $table = 'ayarlar';

    protected $primaryKey = 'anahtar';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['anahtar', 'deger'];
}
