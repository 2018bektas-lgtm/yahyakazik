<?php

use App\Http\Controllers\BasvuruController;
use App\Http\Controllers\SayfaController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Yonetim;
use App\Http\Middleware\GecmisRandevulariTamamla;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ön Yüz Rotaları
|--------------------------------------------------------------------------
| Rota adları menülerde (resources/views/partials/navbar.blade.php) ve
| içerik dosyalarındaki buton tanımlarında kullanılır. Ad değiştirirseniz
| oraları da güncellemeyi unutmayın.
*/

Route::get('/', [SayfaController::class, 'anasayfa'])->name('home');
Route::get('/hakkimizda', [SayfaController::class, 'hakkimizda'])->name('hakkimizda');
Route::get('/faaliyet-alanlari', [SayfaController::class, 'faaliyetAlanlari'])->name('faaliyet-alanlari');
Route::get('/faaliyet-alanlari/{slug}', [SayfaController::class, 'faaliyetAlani'])->name('faaliyet-alani');
Route::get('/sss', [SayfaController::class, 'sss'])->name('sss');
Route::get('/iletisim', [SayfaController::class, 'iletisim'])->name('iletisim');

Route::get('/makaleler', [SayfaController::class, 'blog'])->name('blog');
Route::get('/makaleler/{slug}', [SayfaController::class, 'yazi'])->name('yazi');

// Avukat profilleri (Hakkımızda > Ekibimiz); kayıtlar yönetim panelinden eklenir
Route::get('/ekibimiz/{avukat}', [SayfaController::class, 'avukat'])->name('avukat');

/*
| Av. Kazık'ın eski özgeçmiş sayfası. Paylaşılmış bağlantılar ve arama
| motorları için profil sayfasına kalıcı olarak yönlendirilir.
*/
Route::permanentRedirect('/av-yahya-kazik', '/ekibimiz/yahya-kazik');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Müvekkil Başvurusu ve Randevu
|--------------------------------------------------------------------------
*/

Route::get('/basvuru', [BasvuruController::class, 'goster'])->name('basvuru');
Route::get('/basvuru/saatler', [BasvuruController::class, 'saatler'])->name('basvuru.saatler');
Route::post('/basvuru', [BasvuruController::class, 'kaydet'])->middleware('throttle:6,1')->name('basvuru.kaydet');
Route::get('/basvuru/tamam', [BasvuruController::class, 'tamam'])->name('basvuru.tamam');
Route::post('/iletisim', [BasvuruController::class, 'mesaj'])->middleware('throttle:6,1')->name('iletisim.mesaj');

/*
|--------------------------------------------------------------------------
| Yönetim Paneli
|--------------------------------------------------------------------------
| Hesap açmak için: php artisan yonetici:olustur
*/

Route::prefix('yonetim')->name('yonetim.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/giris', [Yonetim\GirisController::class, 'goster'])->name('giris');
        Route::post('/giris', [Yonetim\GirisController::class, 'giris']);
    });

    Route::middleware(['auth', GecmisRandevulariTamamla::class])->group(function () {
        Route::post('/cikis', [Yonetim\GirisController::class, 'cikis'])->name('cikis');
        Route::get('/', Yonetim\PanelController::class)->name('panel');

        Route::patch('/randevular/{randevu}/durum', [Yonetim\RandevuController::class, 'durum'])->name('randevular.durum');
        Route::resource('randevular', Yonetim\RandevuController::class)
            ->except('show')->parameters(['randevular' => 'randevu']);

        Route::get('/yapilacaklar', [Yonetim\YapilacaklarController::class, 'index'])->name('yapilacaklar');
        Route::post('/gorevler', [Yonetim\YapilacaklarController::class, 'store'])->name('gorevler.store');
        Route::post('/gorevler/{gorev}/alt', [Yonetim\YapilacaklarController::class, 'altGorevEkle'])->name('gorevler.alt');
        Route::patch('/gorevler/{gorev}/tamamla', [Yonetim\YapilacaklarController::class, 'tamamla'])->name('gorevler.tamamla');
        Route::delete('/gorevler/{gorev}', [Yonetim\YapilacaklarController::class, 'destroy'])->name('gorevler.destroy');
        Route::patch('/randevular/{randevu}/tamamla', [Yonetim\YapilacaklarController::class, 'randevuTamamla'])->name('randevular.tamamla');

        Route::resource('avukatlar', Yonetim\AvukatController::class)
            ->except('show')->parameters(['avukatlar' => 'avukat']);

        Route::get('/ayarlar', [Yonetim\AyarlarController::class, 'index'])->name('ayarlar');
        Route::put('/ayarlar', [Yonetim\AyarlarController::class, 'update']);
        Route::get('/ayarlar/seo', [Yonetim\SeoController::class, 'index'])->name('seo');
        Route::put('/ayarlar/seo', [Yonetim\SeoController::class, 'update']);

        Route::get('/takvim', [Yonetim\TakvimController::class, 'index'])->name('takvim');
        Route::get('/takvim/veriler', [Yonetim\TakvimController::class, 'veriler'])->name('takvim.veriler');
        Route::patch('/takvim/tasi', [Yonetim\TakvimController::class, 'tasi'])->name('takvim.tasi');

        Route::patch('/etkinlikler/{etkinlik}/tamamla', [Yonetim\EtkinlikController::class, 'tamamla'])->name('etkinlikler.tamamla');
        Route::resource('etkinlikler', Yonetim\EtkinlikController::class)
            ->except(['index', 'show'])->parameters(['etkinlikler' => 'etkinlik']);
    });
});
