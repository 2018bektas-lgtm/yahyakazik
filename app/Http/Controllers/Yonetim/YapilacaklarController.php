<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Gorev;
use App\Models\Randevu;
use App\Support\GunlukYapilacaklar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Günlük yapılacaklar listesi: o günün randevuları, takvim kayıtları ve
 * yöneticinin eklediği görevler saat saat tek listede.
 */
class YapilacaklarController extends Controller
{
    public function __construct(private GunlukYapilacaklar $yapilacaklar) {}

    public function index(Request $istek): View
    {
        $gun = $istek->filled('tarih') && strtotime($istek->tarih)
            ? Carbon::parse($istek->tarih)->startOfDay()
            : Carbon::today();

        $kalemler = $this->yapilacaklar->kalemler($gun);

        [$saatli, $gunBoyu] = $kalemler->partition(fn ($k) => $k['saat'] !== null);

        // Çalışma saatlerinin bir saat öncesinden bir saat sonrasına kadar her saat
        // gösterilir; bu aralığın dışında kalan kayıt varsa aralık genişler.
        $kalemSaatleri = $saatli->map(fn ($k) => (int) substr($k['saat'], 0, 2));
        $ilk = $kalemSaatleri->push((int) substr(config('randevu.baslangic'), 0, 2) - 1)->min();
        $son = $kalemSaatleri->push((int) substr(config('randevu.bitis'), 0, 2) + 1)->max();

        $saatler = collect(range(max(0, $ilk), min(23, $son)))->mapWithKeys(fn ($s) => [
            $s => $saatli->filter(fn ($k) => (int) substr($k['saat'], 0, 2) === $s)->values(),
        ]);

        return view('yonetim.yapilacaklar', [
            'gun'      => $gun,
            'saatler'  => $saatler,
            'gunBoyu'  => $gunBoyu->values(),
            'toplam'   => $kalemler->count(),
            'biten'    => $kalemler->where('tamam', true)->count(),
            'simdiSaat' => $gun->isToday() ? Carbon::now()->hour : null,
            // Geçmiş günlere görev eklenmez; bugün için en erken şu anki dakika
            'eklenebilir' => ! $gun->isPast() || $gun->isToday(),
            'enErkenSaat' => $gun->isToday() ? Carbon::now()->format('H:i') : null,
        ]);
    }

    public function store(Request $istek): RedirectResponse
    {
        $veri = $istek->validate([
            'baslik'   => ['required', 'string', 'max:255'],
            'tarih'    => ['required', 'date_format:Y-m-d'],
            'saat'     => ['nullable', 'date_format:H:i'],
            'aciklama' => ['nullable', 'string', 'max:2000'],
        ], ['baslik.required' => 'Görevin ne olduğunu yazın.']);

        // Geçmiş bir güne ya da bugünün geçmiş bir saatine görev yazılmaz
        $simdi = Carbon::now();
        $zaman = Carbon::parse($veri['tarih'].' '.($veri['saat'] ?? '23:59'));
        if ($zaman->lt($simdi->copy()->startOfMinute())) {
            throw ValidationException::withMessages(['saat' => $veri['tarih'] < $simdi->toDateString()
                ? 'Geçmiş bir güne görev eklenemez.'
                : 'Geçmiş bir saate görev eklenemez. Şu an saat '.$simdi->format('H:i').'.']);
        }

        Gorev::create($veri);

        // Panel ana sayfasındaki hızlı ekleme kutusundan gelindiyse oraya dön
        if (url()->previous() === route('yonetim.panel')) {
            return back()->with('bilgi', 'Görev eklendi.');
        }

        return redirect()->route('yonetim.yapilacaklar', ['tarih' => $veri['tarih']])->with('bilgi', 'Görev eklendi.');
    }

    /** Bir görevin altına işaretlenebilir alt başlık ekler. */
    public function altGorevEkle(Request $istek, Gorev $gorev): RedirectResponse
    {
        $veri = $istek->validate(['baslik' => ['required', 'string', 'max:255']],
            ['baslik.required' => 'Alt başlığı yazın.']);

        // Alt başlığın altına alt başlık açılmaz; en üstteki göreve bağlanır
        $ust = $gorev->ustGorev ?? $gorev;

        $ust->altGorevler()->create([
            'baslik' => $veri['baslik'],
            'tarih'  => $ust->tarih,
        ]);

        // Yeni açık madde eklenince üst görev yeniden açılır
        $ust->update(['tamamlandi_at' => null]);

        return back();
    }

    /**
     * Görevi ya da alt başlığı işaretler/geri alır. Üst görev işaretlenince
     * tüm alt başlıkları da tamamlanır; alt başlıkların hepsi bitince üst
     * görev kendiliğinden tamamlanır.
     */
    public function tamamla(Gorev $gorev): RedirectResponse
    {
        $zaman = $gorev->tamamlandiMi() ? null : Carbon::now();
        $gorev->update(['tamamlandi_at' => $zaman]);

        if ($gorev->ust_gorev_id === null) {
            if ($zaman) {
                $gorev->altGorevler()->whereNull('tamamlandi_at')->update(['tamamlandi_at' => $zaman]);
            }
        } else {
            $ust = $gorev->ustGorev;
            $acikKalan = $ust->altGorevler()->whereNull('tamamlandi_at')->exists();
            $ust->update(['tamamlandi_at' => $acikKalan ? null : ($ust->tamamlandi_at ?? Carbon::now())]);
        }

        return back();
    }

    public function destroy(Gorev $gorev): RedirectResponse
    {
        $gorev->delete();

        return back()->with('bilgi', 'Görev silindi.');
    }

    /** Listedeki onay kutusu: randevuyu tamamlandı yapar ya da geri alır. */
    public function randevuTamamla(Randevu $randevu): RedirectResponse
    {
        $randevu->durum = $randevu->durum === 'tamamlandi' ? 'onaylandi' : 'tamamlandi';
        // Elle geri alınan randevu, saati geçmiş olsa da yeniden kendiliğinden kapanmasın
        $randevu->otomatik_tamamla = ! $randevu->elleYenidenAcildi();
        $randevu->save();

        return back();
    }
}
