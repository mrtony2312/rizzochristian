<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $static = [
            route('home'),
            route('shop'),
            route('contact'),
            route('help-center'),
            route('tracking-order'),
            url('/termini-e-condizioni'),
            url('/resi-e-rimborsi'),
            url('/spedizione-e-consegna'),
            url('/privacy'),
            url('/note-legali'),
            url('/metodi-di-pagamento'),
            url('/chi-siamo'),
            route('merchant.feed'),
        ];

        $categories = Category::query()->orderBy('id')->get(['slug', 'updated_at']);
        $products = Product::query()->orderBy('id')->get(['slug', 'updated_at']);

        $xml = view('feeds.sitemap', [
            'staticUrls' => $static,
            'categories' => $categories,
            'products' => $products,
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
