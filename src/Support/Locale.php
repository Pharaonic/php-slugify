<?php

namespace Pharaonic\Slugify\Support;

/**
 * Resolves locale codes and lazily loads the curated per-locale overrides
 * from "Resources/locales". A file is only read the first time its locale
 * is used, then kept in memory for the rest of the process.
 *
 * @internal
 */
final class Locale
{
    /**
     * @var array<string, array<string, array<string, string>>>
     */
    private static array $resources = [];

    /**
     * Reduce a locale such as "de-DE", "de_AT" or "DE" to its language ("de").
     *
     * Anything that is not a well-formed language code yields null, so it can
     * never be used to build a file path.
     */
    public static function language(?string $locale): ?string
    {
        if ($locale === null || preg_match('/^([A-Za-z]{2,3})(?:[-_][A-Za-z0-9]{1,8})*$/D', $locale, $match) !== 1) {
            return null;
        }

        return strtolower($match[1]);
    }

    /**
     * One section ("lowercase", "ascii", "ascii_word_initial") of a locale's overrides.
     *
     * @return array<string, string>
     */
    public static function overrides(?string $locale, string $section): array
    {
        $language = self::language($locale);

        if ($language === null) {
            return [];
        }

        if (!array_key_exists($language, self::$resources)) {
            $file = __DIR__ . '/../Resources/locales/' . $language . '.php';

            /** @var array<string, array<string, string>> $resource */
            $resource = is_file($file) ? require $file : [];
            self::$resources[$language] = $resource;
        }

        return self::$resources[$language][$section] ?? [];
    }
}
