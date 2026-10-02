<?php

namespace App\Support;

/**
 * Resolves manufacturer brand / GTIN / MPN without inventing data.
 */
class MerchantProductIdentifiers
{
    public static function resolveBrand(?string $storedBrand, ?string $description, ?string $name): ?string
    {
        $candidates = [];

        $stored = self::sanitizeBrand($storedBrand);
        if ($stored !== null) {
            $candidates[] = $stored;
        }

        $fromName = self::brandFromName($name);
        if ($fromName !== null) {
            $candidates[] = $fromName;
        }

        $fromDescription = self::brandFromMarcaLine($description);
        if ($fromDescription !== null) {
            $candidates[] = $fromDescription;
        }

        foreach ($candidates as $brand) {
            if (! self::isForbiddenBrand($brand)) {
                return self::canonicalBrand($brand);
            }
        }

        return null;
    }

    public static function brandFromText(?string $description, ?string $name = null): ?string
    {
        return self::resolveBrand(null, $description, $name);
    }

    public static function brandFromName(?string $name): ?string
    {
        $name = (string) $name;

        foreach (config('merchant.known_brands', []) as $known) {
            if ($known === '') {
                continue;
            }

            if (preg_match('/\b'.preg_quote($known, '/').'\b/iu', $name) === 1) {
                return self::canonicalBrand($known);
            }
        }

        return null;
    }

    public static function brandFromMarcaLine(?string $description): ?string
    {
        $text = (string) $description;

        if (preg_match('/Marca\s*:\s*([^\r\n]+)/iu', $text, $matches) !== 1) {
            return null;
        }

        return self::sanitizeBrand($matches[1]);
    }

    public static function sanitizeBrand(?string $brand): ?string
    {
        $brand = trim((string) $brand);

        if ($brand === '' || self::isForbiddenBrand($brand)) {
            return null;
        }

        return $brand;
    }

    public static function isForbiddenBrand(?string $brand): bool
    {
        $brand = trim((string) $brand);
        if ($brand === '') {
            return true;
        }

        foreach (config('merchant.forbidden_brands', []) as $forbidden) {
            if (strcasecmp($brand, (string) $forbidden) === 0) {
                return true;
            }
        }

        return false;
    }

    public static function canonicalBrand(string $brand): string
    {
        $map = [
            'fireflies' => 'Fireflies',
            'rekord' => 'REKORD',
            'verba' => 'VERBA',
            'sunfire' => 'SunFire',
            'pinikay' => 'PiniKay',
            'mcz' => 'MCZ',
            'ruf' => 'RUF',
            'eph' => 'EPH',
            'ecopower' => 'Ecopower',
            'biber' => 'Biber',
            'pfeifer' => 'Pfeifer',
            'primex' => 'Primex',
            'pollmeier' => 'Pollmeier',
        ];

        $key = mb_strtolower($brand);

        return $map[$key] ?? $brand;
    }

    /**
     * Only returns a GTIN when it is confirmed for this product slug
     * or stored and not on the blocked list. Never invents values.
     */
    public static function resolveGtin(?string $storedGtin, string $slug): ?string
    {
        $confirmed = config('merchant.confirmed_gtins.'.$slug)
            ?? config('merchant.confirmed_gtins')[$slug] ?? null;

        if (is_string($confirmed) && $confirmed !== '') {
            return self::validatedGtin($confirmed);
        }

        $stored = self::validatedGtin($storedGtin);
        if ($stored === null) {
            return null;
        }

        if (in_array($stored, config('merchant.blocked_gtins', []), true)) {
            return null;
        }

        // Stored GTINs that were auto-parsed from text are not trusted unless confirmed.
        return null;
    }

    public static function resolveMpn(?string $storedMpn, string $slug): ?string
    {
        $confirmed = config('merchant.confirmed_mpns')[$slug] ?? null;
        if (is_string($confirmed) && trim($confirmed) !== '') {
            return trim($confirmed);
        }

        // Do not trust auto-filled / unconfirmed MPN columns.
        return null;
    }

    public static function validatedGtin(?string $digits): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $digits) ?? '';

        if (! in_array(strlen($digits), [8, 12, 13, 14], true)) {
            return null;
        }

        if (in_array($digits, config('merchant.blocked_gtins', []), true)) {
            return null;
        }

        return self::hasValidCheckDigit($digits) ? $digits : null;
    }

    public static function gtinFromText(?string $description, ?string $shortDescription = null): ?string
    {
        // Intentionally disabled for Merchant output: packaging text is not a confirmed primary source.
        return null;
    }

    public static function energyEfficiencyClassFromText(?string $description, ?string $shortDescription = null): ?string
    {
        $text = trim((string) $description."\n".$shortDescription);

        if (preg_match('/Classe di efficienza energetica\s*:?\s*(A\+{0,3}|[B-G])(?!\+)/iu', $text, $matches) !== 1) {
            return null;
        }

        $class = strtoupper($matches[1]);
        $allowed = ['A+++', 'A++', 'A+', 'A', 'B', 'C', 'D', 'E', 'F', 'G'];

        return in_array($class, $allowed, true) ? $class : null;
    }

    public static function hasValidCheckDigit(string $digits): bool
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
