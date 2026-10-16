<?php

namespace App\Http\Controllers;

use App\Models\Randevu;
use App\Support\RandevuTakvimi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Müvekkillerin sitedeki adım adım başvuru ve randevu sayfası.
 *
 * Gelen her kayıt "beklemede" durumunda yönetim panelindeki
 * Randevular listesine ve takvime düşer.
 */
class BasvuruController extends Controller
{
    public function __construct(private RandevuTakvimi $takvim) {}

    public function goster(): View
    {
        return view('pages.basvuru', [
            'konular' => self::konular(),
            'gunler'  => collect($this->takvim->acikGunler())->map(fn ($g) => Carbon::parse($g)),
        ]);
    }

    /** Seçilen gündeki boş saatler; sayfadaki saat düğmeleri bunu çağırır. */
    public function saatler(Request $istek): JsonResponse
    {
        $istek->validate(['tarih' => ['required', 'date_format:Y-m-d']]);

        return response()->json([
            'saatler' => $this->takvim->musaitSaatler(Carbon::parse($istek->tarih)->startOfDay()),
        ]);
    }

    public function kaydet(Request $istek): RedirectResponse
    {
        // Bot tuzağı: gerçek kullanıcılar bu gizli alanı görmez
        if (filled($istek->input('web_sitesi'))) {
            return redirect()->route('basvuru.tamam');
        }

        $veri = $istek->validate([
            'tip'           => ['required', Rule::in(['basvuru', 'randevu'])],
            'konu'          => ['nullable', Rule::in(self::konular())],
            'kurum'         => ['nullable', 'string', 'max:255'],
            'aciklama'      => ['nullable', 'required_if:tip,basvuru', 'string', 'min:20', 'max:5000'],
            'tarih'         => ['nullable', 'required_if:tip,randevu', 'date_format:Y-m-d'],
            'saat'          => ['nullable', 'required_with:tarih', 'date_format:H:i'],
            'ad_soyad'      => ['required', 'string', 'max:120'],
            'telefon'       => ['required', 'string', 'max:30', 'regex:/^[0-9 +()\-]{10,}$/'],
            'eposta'        => ['nullable', 'email', 'max:255'],
            'gorusme_sekli' => ['required', Rule::in(array_keys(Randevu::GORUSME_SEKILLERI))],
            'kvkk'          => ['accepted'],
        ], [
            'aciklama.required_if' => 'Lütfen durumunuzu kısaca anlatın.',
            'aciklama.min'         => 'Durumunuzu biraz daha ayrıntılı anlatır mısınız? (en az 20 karakter)',
            'tarih.required_if'    => 'Lütfen randevu için bir gün seçin.',
            'saat.required_with'   => 'Lütfen bir saat seçin.',
            'telefon.regex'        => 'Lütfen geçerli bir telefon numarası yazın.',
            'kvkk.accepted'        => 'Devam etmek için aydınlatma metnini onaylamanız gerekir.',
        ]);

        $baslangic = null;
        if (! empty($veri['tarih'])) {
            $baslangic = Carbon::createFromFormat('Y-m-d H:i', "{$veri['tarih']} {$veri['saat']}")->seconds(0);

            if (! $this->takvim->musaitMi($baslangic)) {
                return back()->withInput()->withErrors([
                    'saat' => 'Seçtiğiniz saat az önce doldu. Lütfen başka bir saat seçin.',
                ]);
            }
        }

        $randevu = Randevu::create([
            'tip'           => $veri['tip'],
            'konu'          => $veri['konu'] ?? null,
            'kurum'         => $veri['kurum'] ?? null,
            'aciklama'      => $veri['aciklama'] ?? null,
            'ad_soyad'      => $veri['ad_soyad'],
            'telefon'       => $veri['telefon'],
            'eposta'        => $veri['eposta'] ?? null,
            'gorusme_sekli' => $veri['gorusme_sekli'],
            'baslangic'     => $baslangic,
            'sure'          => config('randevu.sure'),
            'kaynak'        => 'site',
        ]);

        return redirect()->route('basvuru.tamam')->with('randevu_id', $randevu->id);
    }

    public function tamam(Request $istek): View
    {
        return view('pages.basvuru-tamam', [
            'randevu' => Randevu::find($istek->session()->get('randevu_id')),
        ]);
    }

    /** İletişim sayfasındaki "Bize yazın" formu. */
    public function mesaj(Request $istek): RedirectResponse
    {
        if (filled($istek->input('web_sitesi'))) {
            return back()->with('mesaj_gonderildi', true);
        }

        $veri = $istek->validate([
            'ad'      => ['required', 'string', 'max:120'],
            'eposta'  => ['required', 'email', 'max:255'],
            'telefon' => ['nullable', 'string', 'max:30'],
            'mesaj'   => ['required', 'string', 'max:5000'],
        ]);

        Randevu::create([
            'tip'      => 'mesaj',
            'ad_soyad' => $veri['ad'],
            'eposta'   => $veri['eposta'],
            'telefon'  => $veri['telefon'] ?? null,
            'aciklama' => $veri['mesaj'],
            'kaynak'   => 'site',
        ]);

        return back()->with('mesaj_gonderildi', true);
    }

    /** @return array<int, string> */
    public static function konular(): array
    {
        return array_merge(
            array_column(config('icerik.faaliyet_alanlari', []), 'ad'),
            config('randevu.ek_konular', []),
        );
    }
}
