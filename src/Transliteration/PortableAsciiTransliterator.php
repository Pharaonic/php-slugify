<?php

namespace Pharaonic\Slugify\Transliteration;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Support\Locale;
use voku\helper\ASCII;

/**
 * Transliterates through voku/portable-ascii, including its generic
 * fallback tables for scripts without a language map (CJK, Hebrew, ...).
 *
 * Works with portable-ascii 1.x and 2.x and gives the same result with both
 * (see Resources/portable-ascii-1.php).
 */
final class PortableAsciiTransliterator implements Transliterator
{
    /**
     * @var array<string, true>|null the language codes portable-ascii knows
     */
    private static ?array $known = null;

    /**
     * Whether portable-ascii 1.x is installed; null until checked.
     */
    private static ?bool $isLegacy = null;

    /**
     * @var array<string, array<string, string>> language => the 2.x results applied over 1.x
     */
    private static array $legacy = [];

    public function transliterate(string $value, ?string $language = null): string
    {
        $language = self::language($language);

        if (preg_match('/[^\x00-\x7F]/', $value) === 1 && ($map = self::legacy($language)) !== []) {
            $value = strtr($value, $map);
        }

        return ASCII::to_ascii($value, $language, true, false, true);
    }

    /**
     * The portable-ascii 2.x results for the letters 1.x transliterates differently.
     *
     * @return array<string, string>
     */
    private static function legacy(string $language): array
    {
        // portable-ascii 1.x transliterates "پ" (peh) as "b".
        self::$isLegacy ??= ASCII::to_ascii('پ') === 'b';

        if (!self::$isLegacy) {
            return [];
        }

        if (!isset(self::$legacy[$language])) {
            /** @var array<string, array<string, string>> $resource */
            $resource = require __DIR__ . '/../Resources/portable-ascii-1.php';
            $own = ASCII::charsArrayWithOneLanguage($language, false, false);

            self::$legacy[$language] = ($resource[$language] ?? [])
                + array_diff_key($resource['generic'], $own);
        }

        return self::$legacy[$language];
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
