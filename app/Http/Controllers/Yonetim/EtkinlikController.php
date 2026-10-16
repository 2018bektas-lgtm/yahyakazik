<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Etkinlik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EtkinlikController extends Controller
{
    public function create(Request $istek): View
    {
        $baslangic = $istek->filled('tarih') ? Carbon::parse($istek->tarih) : Carbon::today()->setTime(9, 0);

        $etkinlik = new Etkinlik([
            'tur'       => $istek->input('tur', 'durusma'),
            'baslangic' => $baslangic,
            'tum_gun'   => $istek->boolean('tum_gun'),
        ]);

        return view('yonetim.etkinlikler.form', compact('etkinlik'));
    }

    public function store(Request $istek): RedirectResponse
    {
        Etkinlik::create($this->dogrula($istek));

        return redirect()->route('yonetim.takvim')->with('bilgi', 'Takvime eklendi.');
    }

    public function edit(Etkinlik $etkinlik): View
    {
        return view('yonetim.etkinlikler.form', compact('etkinlik'));
    }

    public function update(Request $istek, Etkinlik $etkinlik): RedirectResponse
    {
        $etkinlik->update($this->dogrula($istek));

        return redirect()->route('yonetim.takvim')->with('bilgi', 'Kayıt güncellendi.');
    }

    public function tamamla(Etkinlik $etkinlik): RedirectResponse
    {
        $etkinlik->update(['tamamlandi' => ! $etkinlik->tamamlandi]);

        return back()->with('bilgi', $etkinlik->tamamlandi ? 'Tamamlandı olarak işaretlendi.' : 'Yeniden açıldı.');
    }

    public function destroy(Etkinlik $etkinlik): RedirectResponse
    {
        $etkinlik->delete();

        return redirect()->route('yonetim.takvim')->with('bilgi', 'Kayıt silindi.');
    }

    private function dogrula(Request $istek): array
    {
        $veri = $istek->validate([
            'baslik'      => ['required', 'string', 'max:255'],
            'tur'         => ['required', Rule::in(array_keys(Etkinlik::TURLER))],
            'tarih'       => ['required', 'date_format:Y-m-d'],
            'saat'        => ['nullable', 'required_unless:tum_gun,1', 'date_format:H:i'],
            'bitis_tarih' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tarih'],
            'bitis_saat'  => ['nullable', 'date_format:H:i'],
            'tum_gun'     => ['boolean'],
            'yer'         => ['nullable', 'string', 'max:255'],
            'dosya_no'    => ['nullable', 'string', 'max:255'],
            'aciklama'    => ['nullable', 'string', 'max:5000'],
            'tamamlandi'  => ['boolean'],
        ], [
            'saat.required_unless' => 'Saat girin ya da "Tüm gün" seçeneğini işaretleyin.',
        ]);

        $tumGun    = (bool) ($veri['tum_gun'] ?? false);
        $baslangic = Carbon::parse($veri['tarih'].' '.($tumGun ? '00:00' : $veri['saat']));
        $bitis     = null;

        if (! empty($veri['bitis_tarih']) || ! empty($veri['bitis_saat'])) {
            $bitis = Carbon::parse(($veri['bitis_tarih'] ?? $veri['tarih']).' '.($tumGun ? '00:00' : ($veri['bitis_saat'] ?? $veri['saat'])));
            if ($bitis->lt($baslangic)) {
                $bitis = null;
            }
        }

        return [
            'baslik'     => $veri['baslik'],
            'tur'        => $veri['tur'],
            'baslangic'  => $baslangic,
            'bitis'      => $bitis,
            'tum_gun'    => $tumGun,
            'yer'        => $veri['yer'] ?? null,
            'dosya_no'   => $veri['dosya_no'] ?? null,
            'aciklama'   => $veri['aciklama'] ?? null,
            'tamamlandi' => (bool) ($veri['tamamlandi'] ?? false),
        ];
    }
}
