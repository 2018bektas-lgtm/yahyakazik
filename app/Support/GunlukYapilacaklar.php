<?php

namespace App\Support;

use App\Models\Etkinlik;
use App\Models\Gorev;
use App\Models\Randevu;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Bir günün yapılacakları: randevular, takvim kayıtları ve görevler tek
 * listede, saate göre sıralı. Yapılacaklar sayfası ve panel ana sayfası
 * aynı listeyi buradan alır.
 */
class GunlukYapilacaklar
{
    public function kalemler(Carbon $gun): Collection
    {
        return $this->randevular($gun)
            ->concat($this->etkinlikler($gun))
            ->concat($this->gorevler($gun))
            ->sortBy(fn ($k) => ($k['saat'] ?? '').'|'.$k['sira'])
            ->values();
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
