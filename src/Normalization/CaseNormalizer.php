<?php

namespace Pharaonic\Slugify\Normalization;

use Pharaonic\Slugify\Support\Locale;

/**
 * Locale-aware lowercasing.
 *
 * The locale's "lowercase" overrides run first (Turkish "I" => "ı"), then
 * multibyte lowercasing. Lowercasing the capital dotted "İ" yields "i" followed by
 * U+0307 (combining dot above); a plain "i" is kept instead.
 *
 * @internal
 */
final class CaseNormalizer
{
    public static function lower(string $value, ?string $locale = null): string
    {
        $overrides = Locale::overrides($locale, 'lowercase');

        if ($overrides !== []) {
            $value = strtr($value, $overrides);
        }

        return str_replace("i\u{0307}", 'i', mb_strtolower($value, 'UTF-8'));
    }
}
