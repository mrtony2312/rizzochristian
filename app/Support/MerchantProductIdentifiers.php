<?php

namespace App\Support;

/**
 * Extracts merchant identifiers already present in product text.
 * Never invents GTIN, MPN, brand, or energy class values.
 */
class MerchantProductIdentifiers
{
    public static function brandFromText(?string $description, ?string $name = null): ?string
    {
        $text = (string) $description;

        if (preg_match('/Marca\s*:\s*([^\r\n]+)/iu', $text, $matches) === 1) {
            $brand = trim($matches[1]);
            if ($brand !== '') {
                return $brand;
            }
        }

        $name = (string) $name;
        foreach (config('merchant.known_brands', []) as $known) {
            if ($known !== '' && stripos($name, $known) !== false) {
                return $known;
            }
        }

        return null;
    }

    public static function gtinFromText(?string $description, ?string $shortDescription = null): ?string
    {
        $text = trim((string) $description."\n".$shortDescription);

        if (preg_match('/\b(?:EAN|GTIN)\s*[:#]?\s*(\d{8}|\d{12}|\d{13}|\d{14})\b/i', $text, $matches) !== 1) {
            return null;
        }

        return self::hasValidCheckDigit($matches[1]) ? $matches[1] : null;
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
