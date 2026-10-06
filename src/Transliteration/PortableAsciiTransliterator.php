<?php

namespace Pharaonic\Slugify\Transliteration;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Support\Locale;
use voku\helper\ASCII;

/**
 * Transliterates through voku/portable-ascii, including its generic
 * fallback tables for scripts without a language map (CJK, Hebrew, ...).
 */
final class PortableAsciiTransliterator implements Transliterator
{
    /**
     * @var array<string, true>|null the language codes portable-ascii knows
     */
    private static ?array $known = null;

    #[\Override]
    public function transliterate(string $value, ?string $language = null): string
    {
        return ASCII::to_ascii($value, self::language($language), true, false, true);
    }

    /**
     * Resolve a locale to a portable-ascii language code: the exact variant when it
     * has one ("de-AT" => "de_at"), otherwise the base language ("sr-Latn", "uk-UA"
     * => "sr", "uk"), which portable-ascii would otherwise treat as unknown.
     */
    private static function language(?string $language): string
    {
        if ($language === null) {
            return ASCII::ENGLISH_LANGUAGE_CODE;
        }

        self::$known ??= array_fill_keys(array_filter(ASCII::getAllLanguages(), is_string(...)), true);

        $code = str_replace('-', '_', strtolower($language));

        if (isset(self::$known[$code])) {
            return $code;
        }

        return Locale::language($language) ?? $code;
    }
}
