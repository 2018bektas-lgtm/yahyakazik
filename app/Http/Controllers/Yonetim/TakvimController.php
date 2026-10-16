<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Etkinlik;
use App\Models\Randevu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Genel takip takvimi: randevular ve büro kayıtları (duruşma, süre vb.)
 * tek takvimde. Görünüm FullCalendar ile çizilir; veriler buradan JSON gelir.
 */
class TakvimController extends Controller
{
    public const RENKLER = [
        'randevu'      => '#7cc4ff',
        'durusma'      => '#ff8a7a',
        'sure'         => '#ffcf6b',
        'gorusme'      => '#8be3b5',
        'toplanti'     => '#c5a3ff',
        'musait_degil' => '#9a9a98',
        'diger'        => '#e0c3a3',
    ];

    public function index(): View
    {
        return view('yonetim.takvim', ['renkler' => self::RENKLER]);
    }

    public function veriler(Request $istek): JsonResponse
    {
        $istek->validate(['start' => ['required', 'date'], 'end' => ['required', 'date']]);
        $basi = Carbon::parse($istek->start);
        $sonu = Carbon::parse($istek->end);

        $randevular = Randevu::aktif()
            ->whereBetween('baslangic', [$basi, $sonu])
            ->get()
            ->map(fn (Randevu $r) => [
                'id'      => 'r'.$r->id,
                'title'   => $r->ad_soyad.($r->konu ? ' · '.$r->konu : ''),
                'start'   => $r->baslangic->toIso8601String(),
                'end'     => $r->bitis()->toIso8601String(),
                'url'     => route('yonetim.randevular.edit', $r),
                'color'   => self::RENKLER['randevu'],
                'classNames' => ['durum-'.$r->durum],
                'extendedProps' => [
                    'kaynak' => 'randevu',
                    'tur'    => $r->tipEtiketi().' · '.$r->durumEtiketi(),
                    'detay'  => trim(($r->telefon ?? '').' '.$r->gorusmeSekliEtiketi()),
                ],
            ]);

        $etkinlikler = Etkinlik::query()
            ->where('baslangic', '<', $sonu)
            ->where(fn ($q) => $q->where('bitis', '>=', $basi)->orWhere('baslangic', '>=', $basi))
            ->get()
            ->map(fn (Etkinlik $e) => [
                'id'      => 'e'.$e->id,
                'title'   => $e->baslik,
                'start'   => $e->tum_gun ? $e->baslangic->toDateString() : $e->baslangic->toIso8601String(),
                // FullCalendar'da tüm gün bitişi hariç tutulur, bu yüzden bir gün eklenir
                'end'     => $e->tum_gun
                    ? ($e->bitis ?? $e->baslangic)->copy()->addDay()->toDateString()
                    : $e->bitisZamani()->toIso8601String(),
                'allDay'  => $e->tum_gun,
                'url'     => route('yonetim.etkinlikler.edit', $e),
                'color'   => self::RENKLER[$e->tur] ?? self::RENKLER['diger'],
                'classNames' => $e->tamamlandi ? ['tamamlandi'] : [],
                'extendedProps' => [
                    'kaynak' => 'etkinlik',
                    'tur'    => $e->turEtiketi(),
                    'detay'  => trim(($e->dosya_no ? 'Dosya '.$e->dosya_no.' ' : '').($e->yer ?? '')),
                ],
            ]);

        return response()->json($randevular->concat($etkinlikler)->values());
    }

    /** Takvimde sürükleyip bırakınca ya da süresini uzatınca çağrılır. */
    public function tasi(Request $istek): JsonResponse
    {
        $veri = $istek->validate([
            'id'     => ['required', 'string', 'regex:/^[re]\d+$/'],
            'start'  => ['required', 'date'],
            'end'    => ['nullable', 'date'],
            'allDay' => ['boolean'],
        ]);

        $id    = (int) substr($veri['id'], 1);
        $basla = Carbon::parse($veri['start'])->setTimezone(config('app.timezone'));
        $bitis = isset($veri['end']) ? Carbon::parse($veri['end'])->setTimezone(config('app.timezone')) : null;

        if ($veri['id'][0] === 'r') {
            $randevu = Randevu::findOrFail($id);
            $randevu->update([
                'baslangic' => $basla,
                'sure'      => $bitis ? max(5, (int) $basla->diffInMinutes($bitis)) : $randevu->sure,
            ]);
        } else {
            $etkinlik = Etkinlik::findOrFail($id);
            $tumGun   = (bool) ($veri['allDay'] ?? false);
            $etkinlik->update([
                'baslangic' => $tumGun ? $basla->copy()->startOfDay() : $basla,
                'bitis'     => $bitis ? ($tumGun ? $bitis->copy()->subDay()->startOfDay() : $bitis) : null,
                'tum_gun'   => $tumGun,
            ]);
        }

        return response()->json(['tamam' => true]);
    }
}
