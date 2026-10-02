<?php

namespace App\Support;

use App\Models\Product;

class MerchantValidator
{
    /**
     * @return array{
     *   total: int,
     *   valid: int,
     *   blocked: int,
     *   warnings: int,
     *   issues: list<array{product_id:int|null, sku:?string, name:string, field:string, value:string, expected:string, severity:string, reason:string, recommendation:string}>
     * }
     */
    public function validateCatalog(): array
    {
        $products = Product::query()->with(['category', 'images'])->orderBy('id')->get();
        $issues = [];
        $blockedIds = [];
        $warningCount = 0;

        $ids = [];
        foreach ($products as $product) {
            $listing = new MerchantListing($product);
            $productIssues = $this->validateProduct($listing);

            foreach ($productIssues as $issue) {
                $issues[] = $issue;
                if ($issue['severity'] === 'CRITICAL') {
                    $blockedIds[$product->id] = true;
                }
                if ($issue['severity'] === 'WARNING') {
                    $warningCount++;
                }
            }

            $id = $listing->id();
            if (isset($ids[$id])) {
                $issues[] = $this->issue($listing, 'id', $id, 'unique', 'CRITICAL', 'Duplicate merchant id.', 'Ensure each product keeps a stable unique id.');
                $blockedIds[$product->id] = true;
            }
            $ids[$id] = true;
        }

        $blocked = count($blockedIds);
        $total = $products->count();

        return [
            'total' => $total,
            'valid' => max(0, $total - $blocked),
            'blocked' => $blocked,
            'warnings' => $warningCount,
            'issues' => $issues,
        ];
    }

    /**
     * @return list<array{product_id:int|null, sku:?string, name:string, field:string, value:string, expected:string, severity:string, reason:string, recommendation:string}>
     */
    public function validateProduct(MerchantListing $listing): array
    {
        $issues = [];
        $product = $listing->product;

        if ($product->price === null || (float) $product->price <= 0) {
            $issues[] = $this->issue($listing, 'price', (string) $product->price, '> 0', 'CRITICAL', 'Missing or invalid price.', 'Set a real selling price in EUR.');
        }

        if ($listing->imageUrl() === null) {
            $issues[] = $this->issue($listing, 'image', '', 'HTTPS product image', 'CRITICAL', 'No usable product image for the feed.', 'Add a real product image file under public/.');
        }

        if (trim($listing->title()) === '') {
            $issues[] = $this->issue($listing, 'title', '', 'non-empty', 'CRITICAL', 'Empty product title.', 'Set the product name.');
        }

        $storedBrand = trim((string) $product->brand);
        if ($storedBrand !== '' && MerchantProductIdentifiers::isForbiddenBrand($storedBrand)) {
            $issues[] = $this->issue(
                $listing,
                'brand',
                $storedBrand,
                'manufacturer brand only',
                'CRITICAL',
                'Shop/reseller brand is forbidden in g:brand.',
                'Clear the brand or set the real manufacturer. Never use Rizzo Christian / Boutique.'
            );
        }

        if ($product->category?->slug === 'stufe-a-pellet' && $listing->brand() === null) {
            $issues[] = $this->issue(
                $listing,
                'brand',
                '',
                'manufacturer brand for pellet stoves',
                'IMPORTANT',
                'Pellet stove without manufacturer brand.',
                'Set brand from the stove manufacturer (e.g. MCZ).'
            );
        }

        if ($listing->gtin() === null && $listing->mpn() === null && $listing->brand() === null) {
            $issues[] = $this->issue(
                $listing,
                'identifiers',
                'identifier_exists=no',
                'brand and/or GTIN/MPN when available',
                'WARNING',
                'No brand/GTIN/MPN — feed will send identifier_exists=no.',
                'Add a real manufacturer brand and confirmed GTIN/MPN only when known.'
            );
        } elseif ($listing->gtin() === null && $listing->mpn() === null) {
            $issues[] = $this->issue(
                $listing,
                'identifiers',
                'brand without GTIN/MPN',
                'confirmed GTIN or MPN when available',
                'WARNING',
                'Brand present but no confirmed GTIN/MPN.',
                'Confirm packaging EAN/MPN with the supplier; do not invent codes.'
            );
        }

        if ((float) $product->price < (float) $product->regular_price
            && $product->sale_price_starts_at === null
            && $product->sale_price_ends_at === null) {
            $issues[] = $this->issue(
                $listing,
                'sale_price',
                $product->price.' / '.$product->regular_price,
                'dated promo or regular_price = price',
                'CRITICAL',
                'Undated strikethrough price (permanent markdown).',
                'Either set sale_price_starts_at/ends_at for a real ≤30-day promo, or set regular_price = payable price.'
            );
        }

        $currency = $listing->currency();
        if ($currency !== 'EUR') {
            $issues[] = $this->issue($listing, 'currency', $currency, 'EUR for IT market', 'IMPORTANT', 'Unexpected currency for IT market.', 'Use EUR for the Italian Merchant market.');
        }

        if ($listing->imageUrl() && ! str_starts_with((string) $listing->imageUrl(), 'https://')) {
            $issues[] = $this->issue($listing, 'image_link', (string) $listing->imageUrl(), 'https absolute URL', 'CRITICAL', 'Image URL is not HTTPS absolute.', 'Serve images over https://rizzochristian.com.');
        }

        if (! str_starts_with($listing->link(), 'https://')) {
            $issues[] = $this->issue($listing, 'link', $listing->link(), 'https absolute URL', 'CRITICAL', 'Product link is not HTTPS absolute.', 'Set APP_URL to https://rizzochristian.com.');
        }

        return $issues;
    }

    /**
     * @return array{product_id:int|null, sku:?string, name:string, field:string, value:string, expected:string, severity:string, reason:string, recommendation:string}
     */
    private function issue(
        MerchantListing $listing,
        string $field,
        string $value,
        string $expected,
        string $severity,
        string $reason,
        string $recommendation
    ): array {
        return [
            'product_id' => $listing->product->id,
            'sku' => $listing->product->sku,
            'name' => $listing->product->name,
            'field' => $field,
            'value' => $value,
            'expected' => $expected,
            'severity' => $severity,
            'reason' => $reason,
            'recommendation' => $recommendation,
        ];
    }
}
