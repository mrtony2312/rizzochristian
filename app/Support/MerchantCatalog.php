<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MerchantCatalog
{
    /**
     * @return Collection<int, MerchantListing>
     */
    public function feedListings(): Collection
    {
        $products = Product::query()
            ->with(['category', 'images'])
            ->orderBy('id')
            ->get();

        return $products
            ->map(fn (Product $product) => new MerchantListing($product))
            ->filter(function (MerchantListing $listing) {
                if ($listing->imageUrl() !== null) {
                    return true;
                }

                Log::channel('stack')->info('merchant.feed.excluded', [
                    'product_id' => $listing->product->id,
                    'sku' => $listing->product->sku,
                    'name' => $listing->product->name,
                    'reason' => 'missing_usable_image',
                ]);

                return false;
            })
            ->values();
    }

    /**
     * @return array{handling_min: int, handling_max: int, transit_min: int, transit_max: int, total_min: int, total_max: int, free: bool, country: string, price: string, currency: string, service: string}
     */
    public function shipping(): array
    {
        $shipping = config('merchant.shipping');

        return [
            'handling_min' => (int) $shipping['min_handling_time'],
            'handling_max' => (int) $shipping['max_handling_time'],
            'transit_min' => (int) $shipping['min_transit_time'],
            'transit_max' => (int) $shipping['max_transit_time'],
            'total_min' => (int) $shipping['min_total_days'],
            'total_max' => (int) $shipping['max_total_days'],
            'free' => (bool) $shipping['free'],
            'country' => (string) $shipping['country'],
            'price' => (string) $shipping['price'],
            'currency' => (string) config('merchant.market.currency'),
            'service' => (string) $shipping['service'],
        ];
    }

    /**
     * @return array{days: int, customer_pays_return_shipping: bool, applicable_country: string, return_policy_category: string, return_method: string, return_fees: string}
     */
    public function returns(): array
    {
        return config('merchant.returns');
    }

    public function purchaseTermsHtml(): string
    {
        $shipping = $this->shipping();
        $returns = $this->returns();
        $currencyNote = 'Prezzo IVA inclusa.';

        $shippingLine = $shipping['free']
            ? 'Spedizione gratuita in tutta l’Italia.'
            : 'Spedizione in '.$shipping['country'].'.';

        $handling = $shipping['handling_min'].'–'.$shipping['handling_max'].' giorni lavorativi';
        $transit = $shipping['transit_min'].'–'.$shipping['transit_max'].' giorni lavorativi';
        $total = $shipping['total_min'].'–'.$shipping['total_max'].' giorni lavorativi';

        $returnFees = $returns['customer_pays_return_shipping']
            ? 'Le spese di reso sono a carico del cliente, salvo prodotto danneggiato o difettoso.'
            : 'Reso secondo la politica del negozio.';

        return trim(implode(' ', [
            $currencyNote,
            $shippingLine,
            'Preparazione dell’ordine: '.$handling.'.',
            'Spedizione: '.$transit.'.',
            'Tempo totale di consegna: '.$total.'.',
            $returnFees,
        ]));
    }
}
