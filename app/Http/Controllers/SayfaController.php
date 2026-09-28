<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Statik ön yüz sayfaları.
 *
 * Sayfa içerikleri config/site.php ve config/icerik.php dosyalarından okunur;
 * ileride veritabanına taşınacağı için tüm erişim burada toplanmıştır.
 */
class SayfaController extends Controller
{
    public function anasayfa(): View
    {
        return view('pages.home');
    }

    public function hakkimizda(): View
    {
        return view('pages.hakkimizda');
    }

    public function faaliyetAlanlari(): View
    {
        return view('pages.faaliyet-alanlari');
    }

    /**
     * Tek bir faaliyet alanı. Yalnızca config/icerik.php > faaliyet_detaylari
     * içinde karşılığı bulunan alanların sayfası açılır.
     */
    public function faaliyetAlani(string $slug): View
    {
        $alan  = collect(config('icerik.faaliyet_alanlari', []))->firstWhere('slug', $slug);
        $detay = config("icerik.faaliyet_detaylari.{$slug}");

        if ($alan === null || $detay === null) {
            throw new NotFoundHttpException("Faaliyet alanı bulunamadı: {$slug}");
        }

        return view('pages.faaliyet-alani', [
            'alan'          => $alan,
            'detay'         => $detay,
            'ilgiliMakale'  => isset($detay['ilgili_makale'])
                ? $this->yazilar()->firstWhere('slug', $detay['ilgili_makale'])
                : null,
        ]);
    }

    public function sss(): View
    {
        return view('pages.sss');
    }

    public function iletisim(): View
    {
        return view('pages.iletisim');
    }

    public function blog(): View
    {
        return view('pages.blog');
    }

    /**
     * Tek bir blog yazısı. Yazı bulunamazsa 404 döner.
     */
    public function yazi(string $slug): View
    {
        $yazilar = $this->yazilar();

        $yazi = $yazilar->firstWhere('slug', $slug);

        if ($yazi === null) {
            throw new NotFoundHttpException("Yazı bulunamadı: {$slug}");
        }

        return view('pages.yazi', [
            'yazi'        => $yazi,
            'digerYazilar' => $yazilar
                ->reject(fn (array $kayit): bool => $kayit['slug'] === $slug)
                ->take(3)
                ->values(),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function yazilar(): Collection
    {
        return collect(config('icerik.yazilar', []));
    }
}
