<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Randevu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RandevuController extends Controller
{
    public function index(Request $istek): View
    {
        $sorgu = Randevu::query()
            ->when($istek->tip, fn ($q, $tip) => $q->where('tip', $tip))
            ->when($istek->durum, fn ($q, $durum) => $q->where('durum', $durum))
            ->when($istek->ara, fn ($q, $ara) => $q->where(fn ($q) => $q
                ->where('ad_soyad', 'like', "%{$ara}%")
                ->orWhere('telefon', 'like', "%{$ara}%")
                ->orWhere('eposta', 'like', "%{$ara}%")
                ->orWhere('konu', 'like', "%{$ara}%")
                ->orWhere('kurum', 'like', "%{$ara}%")));

        // Yaklaşan randevular önce, sonra zamanı belli olmayan yeni başvurular
        $sorgu->orderByRaw("CASE WHEN durum = 'beklemede' THEN 0 ELSE 1 END")
              ->orderByRaw('baslangic IS NULL')
              ->orderBy('baslangic', $istek->durum === 'tamamlandi' ? 'desc' : 'asc')
              ->latest();

        return view('yonetim.randevular.index', [
            'randevular' => $sorgu->paginate(20)->withQueryString(),
            'sayilar'    => Randevu::selectRaw('durum, count(*) as adet')->groupBy('durum')->pluck('adet', 'durum'),
        ]);
    }

    public function create(Request $istek): View
    {
        $randevu = new Randevu([
            'kaynak'    => 'yonetim',
            'durum'     => 'onaylandi',
            'sure'      => config('randevu.sure'),
            'baslangic' => $istek->filled('tarih') ? Carbon::parse($istek->tarih) : null,
        ]);

        return view('yonetim.randevular.form', compact('randevu'));
    }

    public function store(Request $istek): RedirectResponse
    {
        $randevu = Randevu::create($this->dogrula($istek) + ['kaynak' => 'yonetim']);

        return redirect()->route('yonetim.randevular.edit', $randevu)->with('bilgi', 'Randevu eklendi.');
    }

    public function edit(Randevu $randevu): View
    {
        return view('yonetim.randevular.form', compact('randevu'));
    }

    public function update(Request $istek, Randevu $randevu): RedirectResponse
    {
        $randevu->fill($this->dogrula($istek));
        $randevu->otomatik_tamamla = ! $randevu->elleYenidenAcildi();
        $randevu->save();

        return back()->with('bilgi', 'Değişiklikler kaydedildi.');
    }

    /** Listeden ve panelden tek tıkla durum değiştirme. */
    public function durum(Request $istek, Randevu $randevu): RedirectResponse
    {
        $istek->validate(['durum' => ['required', Rule::in(array_keys(Randevu::DURUMLAR))]]);

        if ($istek->durum === 'onaylandi' && ! $randevu->baslangic) {
            return redirect()->route('yonetim.randevular.edit', $randevu)
                ->with('bilgi', 'Onaylamadan önce randevu gün ve saatini girin.');
        }

        $randevu->fill(['durum' => $istek->durum]);
        $randevu->otomatik_tamamla = ! $randevu->elleYenidenAcildi();
        $randevu->save();

        return back()->with('bilgi', "{$randevu->ad_soyad}: ".$randevu->durumEtiketi());
    }

    public function destroy(Randevu $randevu): RedirectResponse
    {
        $randevu->delete();

        return redirect()->route('yonetim.randevular.index')->with('bilgi', 'Kayıt silindi.');
    }

    private function dogrula(Request $istek): array
    {
        $veri = $istek->validate([
            'tip'           => ['required', Rule::in(array_keys(Randevu::TIPLER))],
            'ad_soyad'      => ['required', 'string', 'max:120'],
            'telefon'       => ['nullable', 'string', 'max:30'],
            'eposta'        => ['nullable', 'email', 'max:255'],
            'konu'          => ['nullable', 'string', 'max:255'],
            'kurum'         => ['nullable', 'string', 'max:255'],
            'aciklama'      => ['nullable', 'string', 'max:5000'],
            'tarih'         => ['nullable', 'required_if:durum,onaylandi', 'date_format:Y-m-d'],
            'saat'          => ['nullable', 'required_with:tarih', 'date_format:H:i'],
            'sure'          => ['required', 'integer', 'min:5', 'max:600'],
            'gorusme_sekli' => ['required', Rule::in(array_keys(Randevu::GORUSME_SEKILLERI))],
            'durum'         => ['required', Rule::in(array_keys(Randevu::DURUMLAR))],
            'notlar'        => ['nullable', 'string', 'max:5000'],
        ], [
            'tarih.required_if' => 'Onaylı bir randevunun günü girilmelidir.',
        ]);

        $veri['baslangic'] = ! empty($veri['tarih'])
            ? Carbon::createFromFormat('Y-m-d H:i', "{$veri['tarih']} {$veri['saat']}")->seconds(0)
            : null;

        unset($veri['tarih'], $veri['saat']);

        return $veri;
    }
}
