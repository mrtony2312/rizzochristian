<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\MerchantListing;
use Illuminate\Http\Response;

class MerchantFeedController extends Controller
{
    public function google(): Response
    {
        $products = Product::with(['category', 'images'])->orderBy('id')->get();

        $items = $products
            ->map(fn (Product $product) => new MerchantListing($product))
            ->filter(fn (MerchantListing $listing) => $listing->imageUrl() !== null);

        $xml = view('feeds.google-merchant', [
            'items' => $items,
            'updated' => now()->toAtomString(),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
