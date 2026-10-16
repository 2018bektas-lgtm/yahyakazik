<?php

namespace App\Http\Controllers;

use App\Models\Avukat;
use Illuminate\Http\Response;

/**
 * /sitemap.xml: arama motorlarına sitedeki sayfaları bildirir.
 * Google Search Console'a bu adres eklenir.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $adresler = collect(['home', 'hakkimizda', 'faaliyet-alanlari', 'blog', 'sss', 'iletisim', 'basvuru'])
            ->map(fn ($rota) => route($rota))
            ->concat(collect(array_keys(config('icerik.faaliyet_detaylari', [])))->map(fn ($slug) => route('faaliyet-alani', $slug)))
            ->concat(collect(config('icerik.yazilar', []))->map(fn ($yazi) => route('yazi', $yazi['slug'])))
            ->concat(Avukat::ekip()->map(fn (Avukat $avukat) => route('avukat', $avukat)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$adresler->map(fn ($adres) => '  <url><loc>'.e($adres).'</loc></url>')->join("\n")."\n"
            .'</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
