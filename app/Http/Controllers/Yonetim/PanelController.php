<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Etkinlik;
use App\Models\Randevu;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function __invoke(): View
    {
        $bugun = Carbon::today();

        return view('yonetim.panel', [
            'bugunkuRandevular' => Randevu::aktif()
                ->whereBetween('baslangic', [$bugun, $bugun->copy()->endOfDay()])
                ->where('durum', '!=', 'tamamlandi')
                ->orderBy('baslangic')->get(),
            'bekleyenler' => Randevu::where('durum', 'beklemede')->latest()->take(6)->get(),
            'bekleyenSayisi' => Randevu::where('durum', 'beklemede')->count(),
            'haftalikRandevu' => Randevu::aktif()
                ->whereBetween('baslangic', [$bugun, $bugun->copy()->addDays(7)->endOfDay()])->count(),
            'yaklasanlar' => Etkinlik::where('tamamlandi', false)
                ->whereBetween('baslangic', [$bugun, $bugun->copy()->addDays(14)->endOfDay()])
                ->orderBy('baslangic')->take(8)->get(),
            'haftalikDurusma' => Etkinlik::where('tur', 'durusma')
                ->whereBetween('baslangic', [$bugun, $bugun->copy()->addDays(7)->endOfDay()])->count(),
            'gecikenSureler' => Etkinlik::where('tur', 'sure')->where('tamamlandi', false)
                ->where('baslangic', '<', $bugun)->orderBy('baslangic')->get(),
        ]);
    }
}
