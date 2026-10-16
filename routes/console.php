<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Saati geçen randevuları "Tamamlandı" yapar. Sunucuda cron ile
| `php artisan schedule:run` her dakika çalıştırılırsa kendiliğinden işler;
| ayrıca yönetim sayfaları her açıldığında da çalışır.
*/
Artisan::command('randevular:tamamla', function () {
    $adet = \App\Models\Randevu::gecmisleriTamamla();
    $this->info("{$adet} randevu tamamlandı olarak işaretlendi.");
})->purpose('Saati geçen randevuları tamamlandı olarak işaretler');

Schedule::command('randevular:tamamla')->everyMinute()->withoutOverlapping();
