<?php

/**
 * Single source of truth for Google Merchant / Shopping market data.
 * Product identifiers (brand, GTIN, MPN, energy) live on the products table.
 * Never invent GTINs, MPNs, brands, prices, or promotions here.
 */
return [

    'market' => [
        'language' => 'it',
        'country' => 'IT',
        'currency' => 'EUR',
    ],

    /**
     * Last-resort brand when the manufacturer is unknown.
     * Prefer products.brand. Using the shop name as brand is only acceptable
     * for true private-label items — merchant:validate warns when this is used.
     */
    'default_brand' => 'Rizzo Christian',

    'feed' => [
        'title' => 'Rizzo Christian',
        'path' => 'feed/google-merchant.xml',
    ],

    'shipping' => [
        'country' => 'IT',
        'service' => 'Spedizione gratuita in Italia',
        'price' => '0.00',
        'min_handling_time' => 1,
        'max_handling_time' => 2,
        'min_transit_time' => 1,
        'max_transit_time' => 2,
        // Derived customer-facing total window (handling + transit).
        'min_total_days' => 2,
        'max_total_days' => 4,
        'free' => true,
    ],

    'returns' => [
        'days' => 14,
        'customer_pays_return_shipping' => true,
        'applicable_country' => 'IT',
        'return_policy_category' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
        'return_method' => 'https://schema.org/ReturnByMail',
        'return_fees' => 'https://schema.org/ReturnFeesCustomerResponsibility',
    ],

    'google_product_category' => [
        'stufe-a-pellet' => '2639',
        'default' => '625',
    ],

    /**
     * Manufacturer names that may be inferred from the product title only when
     * they literally appear in the name. Not inventing — matching existing text.
     */
    'known_brands' => [
        'Pfeifer',
        'RUF',
        'MCZ',
        'Ecopower',
        'Edilkamin',
        'Palazzetti',
        'Extraflame',
        'La Nordica',
        'Primex',
    ],

    'availability' => [
        'in_stock' => 'in_stock',
        'out_of_stock' => 'out_of_stock',
        'schema_in_stock' => 'https://schema.org/InStock',
        'schema_out_of_stock' => 'https://schema.org/OutOfStock',
    ],

];
