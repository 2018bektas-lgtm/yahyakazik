<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Etkinlik;
use App\Models\Randevu;
use App\Support\GunlukYapilacaklar;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function __invoke(GunlukYapilacaklar $yapilacaklar): View
    {
        $bugun = Carbon::today();

        return view('yonetim.panel', [
            'yapilacaklar' => $yapilacaklar->kalemler($bugun),
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
            'grafik' => $this->grafikVerisi(),
            'gecikenSureler' => Etkinlik::where('tur', 'sure')->where('tamamlandi', false)
                ->where('baslangic', '<', $bugun)->orderBy('baslangic')->get(),
        ]);
    }

    /**
     * Duruşma ve randevu dağılımı grafiği: bu hafta, bu ay ve bu yıl için
     * sayılar (iptal edilen randevular sayılmaz).
     *
     * @return array<string, array{etiket: string, randevu: int, durusma: int}>
     */
    private function grafikVerisi(): array
    {
        $simdi = Carbon::now();
        $donemler = [
            'hafta' => ['Bu hafta', $simdi->copy()->startOfWeek(), $simdi->copy()->endOfWeek()],
            'ay'    => ['Bu ay', $simdi->copy()->startOfMonth(), $simdi->copy()->endOfMonth()],
            'yil'   => ['Bu yıl', $simdi->copy()->startOfYear(), $simdi->copy()->endOfYear()],
        ];

        return collect($donemler)->map(fn ($d) => [
            'etiket'  => $d[0],
            'randevu' => Randevu::aktif()->whereBetween('baslangic', [$d[1], $d[2]])->count(),
            'durusma' => Etkinlik::where('tur', 'durusma')->whereBetween('baslangic', [$d[1], $d[2]])->count(),
        ])->all();
    }
}
