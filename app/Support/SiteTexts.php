<?php

namespace App\Support;

use App\Models\SiteText;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SiteTexts
{
    public static function text(string $key, ?string $locale = null): string
    {
        $locale = static::locale($locale);
        $stored = static::stored($locale);

        if (array_key_exists($key, $stored)) {
            return (string) $stored[$key];
        }

        $defaults = config('site_texts.defaults', []);

        return (string) ($defaults[$key] ?? '');
    }

    public static function locale(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $supported = array_keys(config('site_texts.locales', []));

        if (! in_array($locale, $supported, true)) {
            return (string) config('site_texts.fallback', 'it');
        }

        return $locale;
    }

    /**
     * @return array<string, string>
     */
    public static function stored(string $locale): array
    {
        $load = fn (): array => SiteText::query()
            ->where('locale', $locale)
            ->pluck('value', 'key')
            ->all();

        try {
            return Cache::remember('site_texts.'.$locale, 3600, $load);
        } catch (Throwable) {
            return $load();
        }
    }

    public static function flush(?string $locale = null): void
    {
        $locales = $locale ? [$locale] : array_keys(config('site_texts.locales', []));

        foreach ($locales as $code) {
            try {
                Cache::forget('site_texts.'.$code);
            } catch (Throwable) {
                // The storefront still reads fresh rows when the cache store is unavailable.
            }
        }
    }
}
