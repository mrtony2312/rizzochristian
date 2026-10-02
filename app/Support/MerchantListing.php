<?php

namespace App\Support;

use App\Models\Product;

class MerchantListing
{
    public function __construct(public Product $product) {}

    public function id(): string
    {
        return 'PC-'.$this->product->id;
    }

    public function title(): string
    {
        $title = trim(preg_replace('/\s+/', ' ', $this->product->name) ?? '');

        return mb_strlen($title) > 150 ? mb_substr($title, 0, 147).'...' : $title;
    }

    public function description(): string
    {
        $source = trim((string) ($this->product->short_description ?: $this->product->description));
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($source)) ?? '');

        if ($text === '') {
            $text = $this->title();
        }

        return mb_strlen($text) > 5000 ? mb_substr($text, 0, 4997).'...' : $text;
    }

    public function brand(): string
    {
        $stored = trim((string) $this->product->brand);
        if ($stored !== '') {
            return $stored;
        }

        $fromText = MerchantProductIdentifiers::brandFromText(
            $this->product->description,
            $this->product->name
        );

        if ($fromText !== null) {
            return $fromText;
        }

        return (string) config('merchant.default_brand');
    }

    public function usesDefaultBrand(): bool
    {
        $stored = trim((string) $this->product->brand);
        if ($stored !== '') {
            return false;
        }

        return MerchantProductIdentifiers::brandFromText(
            $this->product->description,
            $this->product->name
        ) === null;
    }

    public function gtin(): ?string
    {
        $stored = trim((string) $this->product->gtin);
        if ($stored !== '' && MerchantProductIdentifiers::hasValidCheckDigit($stored)) {
            return $stored;
        }

        return MerchantProductIdentifiers::gtinFromText(
            $this->product->description,
            $this->product->short_description
        );
    }

    public function mpn(): ?string
    {
        $stored = trim((string) $this->product->mpn);

        return $stored !== '' ? $stored : null;
    }

    public function price(): string
    {
        return $this->money($this->product->price);
    }

    public function regularPrice(): ?string
    {
        if (! $this->product->isOnSale()) {
            return null;
        }

        return $this->money($this->product->regular_price);
    }

    public function availability(): string
    {
        return $this->product->in_stock
            ? (string) config('merchant.availability.in_stock')
            : (string) config('merchant.availability.out_of_stock');
    }

    public function schemaAvailability(): string
    {
        return $this->product->in_stock
            ? (string) config('merchant.availability.schema_in_stock')
            : (string) config('merchant.availability.schema_out_of_stock');
    }

    public function googleProductCategory(): string
    {
        $map = config('merchant.google_product_category', []);
        $slug = $this->product->category?->slug;

        return (string) ($map[$slug] ?? $map['default'] ?? '625');
    }

    public function energyEfficiencyClass(): ?string
    {
        $stored = trim((string) $this->product->energy_efficiency_class);
        if ($stored !== '') {
            return $stored;
        }

        return MerchantProductIdentifiers::energyEfficiencyClassFromText(
            $this->product->description,
            $this->product->short_description
        );
    }

    public function imageUrl(): ?string
    {
        $relative = ltrim(str_replace('\\', '/', (string) $this->product->image), '/');

        if ($this->isUsableImage($relative)) {
            return asset($relative);
        }

        if ($this->product->relationLoaded('images')) {
            foreach ($this->product->images as $image) {
                $path = ltrim(str_replace('\\', '/', (string) $image->path), '/');
                if ($this->isUsableImage($path)) {
                    return asset($path);
                }
            }
        }

        $resolved = $this->product->imageUrl();

        if ($this->isLogo($resolved)) {
            return null;
        }

        return $resolved;
    }

    public function link(): string
    {
        return route('product', $this->product->slug);
    }

    public function currency(): string
    {
        return (string) config('merchant.market.currency');
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingDetails(): array
    {
        $shipping = app(MerchantCatalog::class)->shipping();

        return [
            '@type' => 'OfferShippingDetails',
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                'value' => $shipping['price'],
                'currency' => $shipping['currency'],
            ],
            'shippingDestination' => [
                '@type' => 'DefinedRegion',
                'addressCountry' => $shipping['country'],
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $shipping['handling_min'],
                    'maxValue' => $shipping['handling_max'],
                    'unitCode' => 'DAY',
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $shipping['transit_min'],
                    'maxValue' => $shipping['transit_max'],
                    'unitCode' => 'DAY',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function returnPolicy(): array
    {
        $returns = app(MerchantCatalog::class)->returns();

        return [
            '@type' => 'MerchantReturnPolicy',
            'applicableCountry' => $returns['applicable_country'],
            'returnPolicyCategory' => $returns['return_policy_category'],
            'merchantReturnDays' => $returns['days'],
            'returnMethod' => $returns['return_method'],
            'returnFees' => $returns['return_fees'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        $offer = [
            '@type' => 'Offer',
            'url' => $this->link(),
            'priceCurrency' => $this->currency(),
            'price' => $this->price(),
            'availability' => $this->schemaAvailability(),
            'itemCondition' => 'https://schema.org/NewCondition',
            'priceValidUntil' => now()->addYear()->toDateString(),
            'shippingDetails' => $this->shippingDetails(),
            'hasMerchantReturnPolicy' => $this->returnPolicy(),
        ];

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->title(),
            'description' => $this->description(),
            'sku' => $this->id(),
            'brand' => [
                '@type' => 'Brand',
                'name' => $this->brand(),
            ],
            'offers' => $offer,
        ];

        $image = $this->imageUrl();
        if ($image !== null) {
            $data['image'] = $image;
        }

        if ($gtin = $this->gtin()) {
            $data['gtin'] = $gtin;
        }

        if ($mpn = $this->mpn()) {
            $data['mpn'] = $mpn;
        }

        if ($class = $this->energyEfficiencyClass()) {
            $category = $this->energyEfficiencyCategoryUrl($class);
            if ($category !== null) {
                $data['hasEnergyConsumptionDetails'] = [
                    '@type' => 'EnergyConsumptionDetails',
                    'hasEnergyEfficiencyCategory' => $category,
                    'energyEfficiencyScaleMax' => 'https://schema.org/EUEnergyEfficiencyCategoryA3Plus',
                    'energyEfficiencyScaleMin' => 'https://schema.org/EUEnergyEfficiencyCategoryG',
                ];
            }
        }

        return $data;
    }

    private function energyEfficiencyCategoryUrl(string $class): ?string
    {
        return match ($class) {
            'A+++' => 'https://schema.org/EUEnergyEfficiencyCategoryA3Plus',
            'A++' => 'https://schema.org/EUEnergyEfficiencyCategoryA2Plus',
            'A+' => 'https://schema.org/EUEnergyEfficiencyCategoryA1Plus',
            'A' => 'https://schema.org/EUEnergyEfficiencyCategoryA',
            'B' => 'https://schema.org/EUEnergyEfficiencyCategoryB',
            'C' => 'https://schema.org/EUEnergyEfficiencyCategoryC',
            'D' => 'https://schema.org/EUEnergyEfficiencyCategoryD',
            'E' => 'https://schema.org/EUEnergyEfficiencyCategoryE',
            'F' => 'https://schema.org/EUEnergyEfficiencyCategoryF',
            'G' => 'https://schema.org/EUEnergyEfficiencyCategoryG',
            default => null,
        };
    }

    private function isUsableImage(string $relative): bool
    {
        return $relative !== ''
            && ! $this->isLogo($relative)
            && is_file(public_path($relative))
            && filesize(public_path($relative)) > 0;
    }

    private function isLogo(string $path): bool
    {
        return str_contains($path, 'logo-rizzo')
            || str_contains($path, 'logo.svg')
            || str_contains($path, 'logo-brand')
            || str_contains($path, 'logo-email');
    }

    private function money(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
