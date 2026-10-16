<?php

namespace App\Support;

use App\Models\Etkinlik;
use App\Models\Randevu;
use Illuminate\Support\Carbon;

/**
 * Müvekkillere sunulacak boş randevu saatlerini hesaplar.
 *
 * Çalışma günleri ve saatleri config/randevu.php dosyasından gelir. Bekleyen
 * veya onaylı randevular, duruşmalar ve "Müsait değil" kayıtları ile çakışan
 * saatler listeden çıkarılır.
 */
class RandevuTakvimi
{
    /** Takvimde seçilebilecek günler (Y-m-d). */
    public function acikGunler(): array
    {
        $gunler = [];
        $bugun  = Carbon::today();

        for ($i = 0; $i <= config('randevu.ileri_gun'); $i++) {
            $gun = $bugun->copy()->addDays($i);
            if ($this->calismaGunuMu($gun)) {
                $gunler[] = $gun->toDateString();
            }
        }

        return $gunler;
    }

    public function calismaGunuMu(Carbon $gun): bool
    {
        return in_array($gun->dayOfWeekIso, config('randevu.gunler'), true)
            && $gun->betweenIncluded(Carbon::today(), Carbon::today()->addDays(config('randevu.ileri_gun')));
    }

    /**
     * Verilen gündeki boş saatler ('09:00' biçiminde).
     *
     * @param  int|null  $haricId  Düzenlenen randevunun kendi saati dolu sayılmasın diye
     */
    public function musaitSaatler(Carbon $gun, ?int $haricId = null): array
    {
        if (! $this->calismaGunuMu($gun)) {
            return [];
        }

        $sure     = (int) config('randevu.sure');
        $enErken  = Carbon::now()->addHours((int) config('randevu.en_erken_saat'));
        $doluluk  = $this->doluAraliklar($gun, $haricId);
        $kapali   = collect(config('randevu.kapali_araliklar'))->map(fn ($a) => [
            $gun->copy()->setTimeFromTimeString($a[0]),
            $gun->copy()->setTimeFromTimeString($a[1]),
        ]);

        $saat  = $gun->copy()->setTimeFromTimeString(config('randevu.baslangic'));
        $bitis = $gun->copy()->setTimeFromTimeString(config('randevu.bitis'));
        $bos   = [];

        while ($saat->copy()->addMinutes($sure)->lte($bitis)) {
            $son = $saat->copy()->addMinutes($sure);

            $cakisiyor = $kapali->concat($doluluk)
                ->contains(fn ($a) => $saat->lt($a[1]) && $son->gt($a[0]));

            if (! $cakisiyor && $saat->gte($enErken)) {
                $bos[] = $saat->format('H:i');
            }

            $saat = $son;
        }

        return $bos;
    }

    public function musaitMi(Carbon $zaman, ?int $haricId = null): bool
    {
        return in_array($zaman->format('H:i'), $this->musaitSaatler($zaman->copy()->startOfDay(), $haricId), true);
    }

    /** @return array<int, array{0: Carbon, 1: Carbon}> */
    private function doluAraliklar(Carbon $gun, ?int $haricId): array
    {
        $basi = $gun->copy()->startOfDay();
        $sonu = $gun->copy()->endOfDay();

        $randevular = Randevu::query()
            ->whereIn('durum', ['beklemede', 'onaylandi'])
            ->whereBetween('baslangic', [$basi->copy()->subDay(), $sonu])
            ->when($haricId, fn ($q) => $q->whereKeyNot($haricId))
            ->get()
            ->map(fn (Randevu $r) => [$r->baslangic, $r->bitis()]);

        $etkinlikler = Etkinlik::query()
            ->whereIn('tur', ['durusma', 'musait_degil'])
            ->where('baslangic', '<=', $sonu)
            ->where(fn ($q) => $q->where('bitis', '>=', $basi)
                ->orWhere(fn ($q) => $q->whereNull('bitis')->where('baslangic', '>=', $basi->copy()->subDay())))
            ->get()
            ->map(fn (Etkinlik $e) => [
                $e->tum_gun ? $e->baslangic->copy()->startOfDay() : $e->baslangic,
                $e->bitisZamani(),
            ]);

        return $randevular->concat($etkinlikler)->all();
    }
}
