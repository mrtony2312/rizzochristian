<?php

namespace App\Http\Controllers;

use App\Support\MerchantCatalog;
use Illuminate\Http\Response;

class MerchantFeedController extends Controller
{
    public function google(MerchantCatalog $catalog): Response
    {
        $items = $catalog->feedListings();
        $shipping = $catalog->shipping();

        $xml = view('feeds.google-merchant', [
            'items' => $items,
            'shipping' => $shipping,
            'updated' => now()->toAtomString(),
            'feedTitle' => config('merchant.feed.title'),
            'feedDescription' => $catalog->purchaseTermsHtml(),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
