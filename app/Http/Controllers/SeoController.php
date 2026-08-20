<?php

namespace App\Http\Controllers;

use App\Models\Giveaway;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('login'), 'priority' => '0.3'],
            ['loc' => route('register'), 'priority' => '0.5'],
        ]);

        // Sorteios concluídos viram páginas públicas de comprovação —
        // vale indexar, ajuda no SEO de cada organizador também.
        Giveaway::where('status', 'completed')
            ->latest('drawn_at')
            ->limit(500)
            ->get()
            ->each(function ($giveaway) use ($urls) {
                $urls->push([
                    'loc' => $giveaway->verificationUrl(),
                    'priority' => '0.4',
                    'lastmod' => $giveaway->drawn_at?->toAtomString(),
                ]);
            });

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $conteudo = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /dashboard\nDisallow: /sorteios/\n\nSitemap: ".route('sitemap');

        return response($conteudo, 200)->header('Content-Type', 'text/plain');
    }
}
