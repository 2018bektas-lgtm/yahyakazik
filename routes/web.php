<?php

use App\Http\Controllers\SayfaController;
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

/*
| Av. Kazık'ın özgeçmişi ayrı bir sayfaydı; tek kişilik bir büroda
| "Hakkımızda" ile aynı şeyi anlattığı için birleştirildi. Eski adres,
| paylaşılmış bağlantılar ve arama motorları için kalıcı olarak yönlendirilir.
*/
Route::permanentRedirect('/av-yahya-kazik', '/hakkimizda');
