<?php

namespace App\Http\Middleware;

use App\Models\Randevu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yönetim sayfası her açıldığında saati geçen randevuları tamamlar.
 * Sunucuda zamanlayıcı (schedule:run) kurulu olmasa da liste güncel kalır.
 */
class GecmisRandevulariTamamla
{
    public function handle(Request $istek, Closure $sonraki): Response
    {
        Randevu::gecmisleriTamamla();

        return $sonraki($istek);
    }
}
