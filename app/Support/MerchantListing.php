<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Carbon;

class MerchantListing
{
    public function __construct(public Product $product) {}

    public function id(): string
    {
        return 'PC-'.$this->product->id;
    }

    public function title(): string
    {
        $title = trim(preg_replace('/\s+/', ' ', html_entity_decode($this->product->name, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');

        return mb_strlen($title) > 150 ? mb_substr($title, 0, 147).'...' : $title;
    }

    public function description(): string
    {
        $source = trim((string) ($this->product->short_description ?: $this->product->description));
        $text = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($source, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '');

        if ($text === '') {
            $text = $this->title();
        }

        return mb_strlen($text) > 5000 ? mb_substr($text, 0, 4997).'...' : $text;
    }

    public function brand(): ?string
    {
        return MerchantProductIdentifiers::resolveBrand(
            $this->product->brand,
            $this->product->description,
            $this->product->name
        );
    }

    public function gtin(): ?string
    {
        return MerchantProductIdentifiers::resolveGtin(
            $this->product->gtin,
            $this->product->slug
        );
    }

    public function mpn(): ?string
    {
        return MerchantProductIdentifiers::resolveMpn(
            $this->product->mpn,
            $this->product->slug
        );
    }

    public function hasIdentifierExistsNo(): bool
    {
        return $this->brand() === null
            && $this->gtin() === null
            && $this->mpn() === null;
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

    public function salePriceEffectiveDate(): ?string
    {
        if (! $this->product->isOnSale()) {
            return null;
        }

        $tz = config('merchant.market.timezone', 'Europe/Rome');
        $start = $this->product->sale_price_starts_at
            ? Carbon::parse($this->product->sale_price_starts_at)->timezone($tz)
            : null;
        $end = $this->product->sale_price_ends_at
            ? Carbon::parse($this->product->sale_price_ends_at)->timezone($tz)
            : null;

        if ($start === null || $end === null) {
            return null;
        }

        return $start->format('Y-m-d\TH:iO').'/'.$end->format('Y-m-d\TH:iO');
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
            return $this->absoluteHttps(asset($relative));
        }

        if ($this->product->relationLoaded('images')) {
            foreach ($this->product->images as $image) {
                $path = ltrim(str_replace('\\', '/', (string) $image->path), '/');
                if ($this->isUsableImage($path)) {
                    return $this->absoluteHttps(asset($path));
                }
            }
        }

        $resolved = $this->product->imageUrl();

        if ($this->isLogo($resolved)) {
            return null;
        }

        return $this->absoluteHttps($resolved);
    }

    public function link(): string
    {
        return $this->absoluteHttps(route('product', $this->product->slug)) ?? route('product', $this->product->slug);
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
            'shippingDetails' => $this->shippingDetails(),
            'hasMerchantReturnPolicy' => $this->returnPolicy(),
        ];

        if ($this->product->isOnSale() && $this->regularPrice() !== null) {
            $offer['priceSpecification'] = [
                [
                    '@type' => 'UnitPriceSpecification',
                    'priceType' => 'https://schema.org/StrikethroughPrice',
                    'price' => $this->regularPrice(),
                    'priceCurrency' => $this->currency(),
                ],
                [
                    '@type' => 'UnitPriceSpecification',
                    'price' => $this->price(),
                    'priceCurrency' => $this->currency(),
                ],
            ];

            if ($this->product->sale_price_ends_at) {
                $offer['priceValidUntil'] = $this->product->sale_price_ends_at
                    ->timezone(config('merchant.market.timezone', 'Europe/Rome'))
                    ->toDateString();
            }
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->title(),
            'description' => $this->description(),
            'sku' => $this->id(),
            'offers' => $offer,
        ];

        if ($brand = $this->brand()) {
            $data['brand'] = [
                '@type' => 'Brand',
                'name' => $brand,
            ];
        }

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

    private function absoluteHttps(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        if (str_starts_with($url, 'http://')) {
            $url = 'https://'.substr($url, strlen('http://'));
        }

        if (str_starts_with($url, '/')) {
            $url = rtrim((string) config('app.url'), '/').$url;
        }

        return $url;
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
