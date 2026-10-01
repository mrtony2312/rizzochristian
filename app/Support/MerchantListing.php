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
        $text = (string) $this->product->description;

        if (preg_match('/Marca\s*:\s*([^\r\n]+)/iu', $text, $matches) === 1) {
            $brand = trim($matches[1]);
            if ($brand !== '') {
                return $brand;
            }
        }

        foreach (['Pfeifer', 'RUF', 'MCZ', 'Ecopower', 'Edilkamin', 'Palazzetti', 'Extraflame', 'La Nordica'] as $known) {
            if (stripos($this->product->name, $known) !== false) {
                return $known;
            }
        }

        return 'Rizzo Christian';
    }

    public function gtin(): ?string
    {
        $text = $this->product->description."\n".$this->product->short_description;

        if (preg_match('/\b(?:EAN|GTIN)\s*[:#]?\s*(\d{8}|\d{12}|\d{13}|\d{14})\b/i', $text, $matches) !== 1) {
            return null;
        }

        return $this->hasValidCheckDigit($matches[1]) ? $matches[1] : null;
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
        return $this->product->in_stock ? 'in_stock' : 'out_of_stock';
    }

    public function schemaAvailability(): string
    {
        return $this->product->in_stock
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';
    }

    public function googleProductCategory(): string
    {
        return $this->product->category?->slug === 'stufe-a-pellet' ? '2639' : '625';
    }

    public function energyEfficiencyClass(): ?string
    {
        $text = $this->product->description."\n".$this->product->short_description;

        if (preg_match('/Classe di efficienza energetica\s*:?\s*(A\+{0,3}|[B-G])(?!\+)/iu', $text, $matches) !== 1) {
            return null;
        }

        $class = strtoupper($matches[1]);
        $allowed = ['A+++', 'A++', 'A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G'];

        return in_array($class, $allowed, true) ? $class : null;
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

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        $offer = [
            '@type' => 'Offer',
            'url' => route('product', $this->product->slug),
            'priceCurrency' => 'EUR',
            'price' => $this->price(),
            'availability' => $this->schemaAvailability(),
            'itemCondition' => 'https://schema.org/NewCondition',
            'priceValidUntil' => now()->addYear()->toDateString(),
            'shippingDetails' => [
                '@type' => 'OfferShippingDetails',
                'shippingRate' => [
                    '@type' => 'MonetaryAmount',
                    'value' => '0.00',
                    'currency' => 'EUR',
                ],
                'shippingDestination' => [
                    '@type' => 'DefinedRegion',
                    'addressCountry' => 'IT',
                ],
                'deliveryTime' => [
                    '@type' => 'ShippingDeliveryTime',
                    'handlingTime' => [
                        '@type' => 'QuantitativeValue',
                        'minValue' => 1,
                        'maxValue' => 2,
                        'unitCode' => 'DAY',
                    ],
                    'transitTime' => [
                        '@type' => 'QuantitativeValue',
                        'minValue' => 1,
                        'maxValue' => 2,
                        'unitCode' => 'DAY',
                    ],
                ],
            ],
            'hasMerchantReturnPolicy' => [
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'IT',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays' => 14,
                'returnMethod' => 'https://schema.org/ReturnByMail',
                'returnFees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
            ],
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

    private function money(mixed $amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }

    private function hasValidCheckDigit(string $digits): bool
    {
        $check = (int) substr($digits, -1);
        $body = substr($digits, 0, -1);
        $sum = 0;
        $factor = 3;

        for ($i = strlen($body) - 1; $i >= 0; $i--) {
            $sum += (int) $body[$i] * $factor;
            $factor = $factor === 3 ? 1 : 3;
        }

        return ((10 - ($sum % 10)) % 10) === $check;
    }
}
