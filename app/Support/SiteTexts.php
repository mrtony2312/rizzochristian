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

    public static function html(string $key, ?string $locale = null): string
    {
        return static::sanitizeHtml(static::text($key, $locale));
    }

    public static function plain(string $key, ?string $locale = null): string
    {
        $html = static::html($key, $locale);
        $html = preg_replace('/<\s*br\s*\/?>/i', "\n", $html) ?? $html;
        $html = preg_replace('/<\/\s*(p|h2|h3|li|div)\s*>/i', "\n\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    public static function sanitizeHtml(string $html): string
    {
        $html = preg_replace('#<\s*(script|style)\b[^>]*>.*?</\s*\1\s*>#is', '', $html) ?? $html;
        $html = strip_tags($html, '<p><br><strong><b><em><i><a><ul><ol><li><h2><h3>');
        $html = preg_replace('/\s+(on\w+|style)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace_callback('/<a\b[^>]*>/i', function (array $match): string {
            $tag = $match[0];
            if (! preg_match('/href\s*=\s*("|\')(.*?)\1/i', $tag, $href)) {
                return $tag;
            }

            $url = trim(html_entity_decode($href[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (preg_match('#^(https?:|mailto:)#i', $url)) {
                return $tag;
            }

            return preg_replace('/\s*href\s*=\s*("|\')(.*?)\1/i', '', $tag) ?? $tag;
        }, $html) ?? $html;

        return trim($html);
    }

    public static function hasBankDetails(?string $locale = null): bool
    {
        foreach (['bank.holder', 'bank.iban', 'bank.bic', 'bank.transfer_type'] as $key) {
            if (trim(static::text($key, $locale)) !== '') {
                return true;
            }
        }

        return false;
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
