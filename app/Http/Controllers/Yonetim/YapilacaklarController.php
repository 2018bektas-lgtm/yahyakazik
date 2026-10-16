<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Etkinlik;
use App\Models\Gorev;
use App\Models\Randevu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Günlük yapılacaklar listesi: o günün randevuları, takvim kayıtları ve
 * yöneticinin eklediği görevler saat saat tek listede.
 */
class YapilacaklarController extends Controller
{
    public function index(Request $istek): View
    {
        $gun = $istek->filled('tarih') && strtotime($istek->tarih)
            ? Carbon::parse($istek->tarih)->startOfDay()
            : Carbon::today();

        $kalemler = $this->randevular($gun)
            ->concat($this->etkinlikler($gun))
            ->concat($this->gorevler($gun))
            ->sortBy(fn ($k) => ($k['saat'] ?? '').'|'.$k['sira']);

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

        Gorev::create($veri);

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

    private function randevular(Carbon $gun): Collection
    {
        return Randevu::aktif()
            ->whereBetween('baslangic', [$gun, $gun->copy()->endOfDay()])
            ->get()
            ->map(fn (Randevu $r) => [
                'tur'    => 'randevu',
                'sira'   => 1,
                'saat'   => $r->baslangic->format('H:i'),
                'bitis'  => $r->bitis()->format('H:i'),
                'baslik' => $r->ad_soyad,
                'alt'    => collect([$r->konu, $r->gorusmeSekliEtiketi(), $r->telefon])->filter()->join(' · '),
                'etiket' => $r->durum === 'beklemede' ? 'Onay bekliyor' : 'Randevu',
                'tamam'  => $r->durum === 'tamamlandi',
                'tamamla' => route('yonetim.randevular.tamamla', $r),
                'link'   => route('yonetim.randevular.edit', $r),
                'sil'    => null,
            ]);
    }

    private function etkinlikler(Carbon $gun): Collection
    {
        return Etkinlik::query()
            ->where('baslangic', '<=', $gun->copy()->endOfDay())
            ->where(fn ($q) => $q->where('baslangic', '>=', $gun)
                ->orWhere(fn ($q) => $q->where('tum_gun', true)->where('bitis', '>=', $gun)))
            ->get()
            ->map(fn (Etkinlik $e) => [
                'tur'    => 'etkinlik tur-'.$e->tur,
                'sira'   => 0,
                'saat'   => $e->tum_gun ? null : $e->baslangic->format('H:i'),
                'bitis'  => $e->tum_gun || ! $e->bitis ? null : $e->bitis->format('H:i'),
                'baslik' => $e->baslik,
                'alt'    => collect([$e->yer, $e->dosya_no])->filter()->join(' · '),
                'etiket' => $e->turEtiketi(),
                'tamam'  => $e->tamamlandi,
                'tamamla' => route('yonetim.etkinlikler.tamamla', $e),
                'link'   => route('yonetim.etkinlikler.edit', $e),
                'sil'    => null,
            ]);
    }

    private function gorevler(Carbon $gun): Collection
    {
        return Gorev::whereDate('tarih', $gun)
            ->whereNull('ust_gorev_id')
            ->with('altGorevler')
            ->get()
            ->map(fn (Gorev $g) => [
                'tur'    => 'gorev',
                'sira'   => 2,
                'saat'   => $g->saatMetni(),
                'bitis'  => null,
                'baslik' => $g->baslik,
                'alt'    => $g->aciklama,
                'etiket' => 'Görev',
                'tamam'  => $g->tamamlandiMi(),
                'tamamla' => route('yonetim.gorevler.tamamla', $g),
                'link'   => null,
                'sil'    => route('yonetim.gorevler.destroy', $g),
                'altEkle' => route('yonetim.gorevler.alt', $g),
                'altlar' => $g->altGorevler->map(fn (Gorev $a) => [
                    'baslik'  => $a->baslik,
                    'tamam'   => $a->tamamlandiMi(),
                    'tamamla' => route('yonetim.gorevler.tamamla', $a),
                    'sil'     => route('yonetim.gorevler.destroy', $a),
                ]),
            ]);
    }
}
