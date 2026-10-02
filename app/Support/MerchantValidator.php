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

        if ($listing->usesDefaultBrand()) {
            $issues[] = $this->issue(
                $listing,
                'brand',
                $listing->brand(),
                'manufacturer or confirmed private label',
                'WARNING',
                'Brand falls back to the shop default brand.',
                'Set products.brand to the real manufacturer when known; do not invent a brand.'
            );
        }

        if ($listing->gtin() === null && $listing->mpn() === null) {
            $issues[] = $this->issue(
                $listing,
                'identifiers',
                'identifier_exists=no',
                'GTIN and/or MPN when available',
                'WARNING',
                'No GTIN/MPN on file.',
                'Add a real GTIN/MPN only when known; otherwise keep identifier_exists=no.'
            );
        }

        if ($product->isOnSale()) {
            $issues[] = $this->issue(
                $listing,
                'sale_price',
                $listing->price().' / '.$listing->regularPrice(),
                'credible sale vs regular price',
                'WARNING',
                'Product is on sale.',
                'Confirm the regular_price is a genuine prior/reference price, not an inflated strike-through.'
            );
        }

        $currency = $listing->currency();
        if ($currency !== 'EUR') {
            $issues[] = $this->issue($listing, 'currency', $currency, 'EUR for IT market', 'IMPORTANT', 'Unexpected currency for IT market.', 'Use EUR for the Italian Merchant market.');
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
