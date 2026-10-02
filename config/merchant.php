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
        'timezone' => 'Europe/Rome',
    ],

    /**
     * Never use the shop / reseller name as g:brand.
     */
    'forbidden_brands' => [
        'Boutique',
        'Rizzo',
        'Rizzo Christian',
        'Generic',
        'heizbrikett.de',
        'PelletCasa',
        'Pellet Casa',
    ],

    /**
     * GTIN values known to belong to a DIFFERENT SKU / packaging.
     * Do not publish these on the catalogue offers currently sold.
     */
    'blocked_gtins' => [
        '8018459172355', // MCZ Stream Comfort Air 12 R (rear outlet), not UP!
        '5430000813013', // Ecopower 15 kg bag, not 65×15 kg pallet
    ],

    /**
     * GTINs confirmed on the exact sold packaging (carton / supplier invoice).
     * Empty until confirmed by a human — never invent entries.
     */
    'confirmed_gtins' => [
        // 'PRODUCT_SLUG' => 'GTIN',
    ],

    /**
     * Manufacturer MPNs confirmed for the exact sold SKU.
     * Empty until confirmed — e.g. MCZ Cute Air 8 UP! article 7122002 is NOT confirmed here yet.
     */
    'confirmed_mpns' => [
        // 'mcz-cute-air-8-up-stufa-a-pellet-compatta-uscita-superiore' => '7122002',
    ],

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
        // Home & Garden > Household Appliances > Climate Control > Heaters
        'stufe-a-pellet' => '2639',
        // Home & Garden > Fireplace & Wood Stove Accessories > Firewood & Fuel
        'pellet-di-legno' => '625',
        'legna-da-ardere' => '625',
        'bricchetti-di-legno' => '625',
        'default' => '625',
    ],

    /**
     * Manufacturer names matched from the product title (literal occurrence).
     * Longer names first. Not inventing — matching existing text only.
     */
    'known_brands' => [
        'La Nordica',
        'FIREFLIES',
        'Fireflies',
        'Ecopower',
        'Extraflame',
        'Edilkamin',
        'Palazzetti',
        'Pollmeier',
        'PiniKay',
        'SunFire',
        'Pfeifer',
        'Primex',
        'REKORD',
        'Rekord',
        'VERBA',
        'Verba',
        'Biber',
        'MCZ',
        'RUF',
        'EPH',
    ],

    'availability' => [
        'in_stock' => 'in_stock',
        'out_of_stock' => 'out_of_stock',
        'schema_in_stock' => 'https://schema.org/InStock',
        'schema_out_of_stock' => 'https://schema.org/OutOfStock',
    ],

];
