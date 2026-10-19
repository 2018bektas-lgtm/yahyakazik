<?php

namespace App\Http\Controllers\Yonetim;

use App\Http\Controllers\Controller;
use App\Models\Avukat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Yönetim paneli > Avukatlar: Hakkımızda sayfasındaki ekip ve avukat
 * profilleri buradan yönetilir.
 */
class AvukatController extends Controller
{
    private const KLASOR = 'uploads/avukatlar';

    public function index(): View
    {
        return view('yonetim.avukatlar.index', [
            'avukatlar' => Avukat::orderBy('sira')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('yonetim.avukatlar.form', [
            'avukat' => new Avukat(['sira' => (Avukat::max('sira') ?? 0) + 1]),
        ]);
    }

    public function store(Request $istek): RedirectResponse
    {
        $avukat = Avukat::create($this->dogrula($istek));
        $this->fotografiIsle($istek, $avukat);

        return redirect()->route('yonetim.avukatlar.index')->with('bilgi', "{$avukat->ad} eklendi.");
    }

    public function edit(Avukat $avukat): View
    {
        return view('yonetim.avukatlar.form', compact('avukat'));
    }

    public function update(Request $istek, Avukat $avukat): RedirectResponse
    {
        $avukat->update($this->dogrula($istek, $avukat));
        $this->fotografiIsle($istek, $avukat);

        return redirect()->route('yonetim.avukatlar.index')->with('bilgi', "{$avukat->ad} güncellendi.");
    }

    public function destroy(Avukat $avukat): RedirectResponse
    {
        $this->fotografiSil($avukat);
        $avukat->delete();

        return redirect()->route('yonetim.avukatlar.index')->with('bilgi', "{$avukat->ad} silindi.");
    }

    private function dogrula(Request $istek, ?Avukat $avukat = null): array
    {
        // Adres boş bırakılırsa addan üretilir: "Av. Ayşe Yılmaz" => "ayse-yilmaz"
        if (blank($istek->input('slug'))) {
            $istek->merge(['slug' => Str::slug(preg_replace('/^(Av\.|Stj\. Av\.|Arb\.)\s*/u', '', (string) $istek->input('ad')))]);
        }

        $veri = $istek->validate([
            'ad'          => ['required', 'string', 'max:120'],
            'slug'        => ['required', 'string', 'max:120', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', Rule::unique('avukatlar', 'slug')->ignore($avukat)],
            'unvan'       => ['nullable', 'string', 'max:120'],
            'baro'        => ['nullable', 'string', 'max:120'],
            'ozet'        => ['nullable', 'string', 'max:400'],
            'biyografi'   => ['nullable', 'string', 'max:10000'],
            'uzmanliklar' => ['nullable', 'string', 'max:2000'],
            'ozgecmis'                => ['nullable', 'array', 'max:30'],
            'ozgecmis.*.donem'        => ['nullable', 'string', 'max:60'],
            'ozgecmis.*.baslik'       => ['nullable', 'string', 'max:150'],
            'ozgecmis.*.metin'        => ['nullable', 'string', 'max:600'],
            'eposta'      => ['nullable', 'email', 'max:150'],
            'linkedin'    => ['nullable', 'url', 'max:255'],
            'sira'        => ['nullable', 'integer', 'min:0', 'max:999'],
            'yayinda'     => ['nullable', 'boolean'],
            'fotograf'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'fotograf_kaldir' => ['nullable', 'boolean'],
        ], [
            'ad.required' => 'Avukatın adını yazın.',
            'slug.regex'  => 'Sayfa adresinde yalnızca küçük harf, rakam ve tire olabilir (örn. ayse-yilmaz).',
            'slug.unique' => 'Bu sayfa adresi başka bir avukatta kullanılıyor.',
            'linkedin.url' => 'LinkedIn adresi https:// ile başlayan tam bir bağlantı olmalı.',
            'fotograf.image' => 'Fotoğraf bir resim dosyası olmalı.',
            'fotograf.max'   => 'Fotoğraf en fazla 4 MB olabilir.',
        ]);

        // Uzmanlıklar her satıra bir tane yazılır
        $veri['uzmanliklar'] = collect(preg_split('/\R/u', (string) ($veri['uzmanliklar'] ?? '')))
            ->map(fn ($s) => trim($s, " \t-•"))->filter()->values()->all();

        // Özgeçmişte tamamen boş bırakılan satırlar atılır
        $veri['ozgecmis'] = collect($veri['ozgecmis'] ?? [])
            ->map(fn ($k) => array_map(fn ($v) => trim((string) $v), array_merge(['donem' => '', 'baslik' => '', 'metin' => ''], $k)))
            ->filter(fn ($k) => $k['baslik'] !== '' || $k['metin'] !== '')
            ->values()->all();

        $veri['sira'] = (int) ($veri['sira'] ?? 0);
        $veri['yayinda'] = $istek->boolean('yayinda');

        unset($veri['fotograf'], $veri['fotograf_kaldir']);

        return $veri;
    }

    private function fotografiIsle(Request $istek, Avukat $avukat): void
    {
        if ($istek->boolean('fotograf_kaldir')) {
            $this->fotografiSil($avukat);
            $avukat->update(['fotograf' => null]);
        }

        if ($istek->hasFile('fotograf')) {
            $this->fotografiSil($avukat);
            $dosya = $istek->file('fotograf');
            $ad = $avukat->slug.'-'.Str::lower(Str::random(6)).'.'.$dosya->extension();
            File::ensureDirectoryExists(public_path(self::KLASOR));
            $dosya->move(public_path(self::KLASOR), $ad);
            $avukat->update(['fotograf' => self::KLASOR.'/'.$ad]);
        }
    }

    private function fotografiSil(Avukat $avukat): void
    {
        if ($avukat->fotograf && str_starts_with($avukat->fotograf, self::KLASOR.'/')) {
            File::delete(public_path($avukat->fotograf));
        }
    }
}
