<?php

namespace Pharaonic\Slugify\Transliteration;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Support\Locale;

/**
 * Applies Pharaonic's small, curated locale overrides ("Resources/locales")
 * and hands everything else to a generic transliterator (portable-ascii by default).
 *
 * Overrides exist only where the generic result is demonstrably wrong; every
 * one of them is justified by tests/Fixtures/Transliteration.
 */
final class LocaleAwareTransliterator implements Transliterator
{
    /**
     * @var array<string, string> locale => compiled pattern
     */
    private static array $patterns = [];

    private Transliterator $generic;

    public function __construct(?Transliterator $generic = null)
    {
        $this->generic = $generic ?? new PortableAsciiTransliterator();
    }

    public function transliterate(string $value, ?string $language = null): string
    {
        $map = Locale::overrides($language, 'ascii');

        if ($map !== [] && preg_match('/[^\x00-\x7F]/', $value) === 1) {
            $value = $this->applyOverrides($value, (string) Locale::language($language), $map);
        }

        return $this->generic->transliterate($value, $language);
    }

    /**
     * @param array<string, string> $map
     */
    private function applyOverrides(string $value, string $language, array $map): string
    {
        $initial = Locale::overrides($language, 'ascii_word_initial');

        return (string) preg_replace_callback(
            self::$patterns[$language] ??= self::compile($map, $initial),
            static function (array $match) use ($map, $initial): string {
                $isInitial = isset($match['initial']);
                $source = (string) ($isInitial ? $match['initial'] : $match['other']);
                $key = mb_strtolower($source, 'UTF-8');
                $replacement = ($isInitial ? $initial[$key] ?? null : null) ?? $map[$key] ?? $source;
                $inUpperCaseWord = isset($match['previous']) || isset($match['next']);

                return self::matchCase($replacement, $source, $inUpperCaseWord);
            },
            $value,
            -1,
            $count,
            PREG_UNMATCHED_AS_NULL
        );
    }

    /**
     * @param array<string, string> $map
     * @param array<string, string> $initial
     */
    private static function compile(array $map, array $initial): string
    {
        $alternatives = static function (array $searches): string {
            $searches = array_map('strval', array_keys($searches));
            usort($searches, static function (string $a, string $b): int {
                return strlen($b) <=> strlen($a);
            });

            return implode('|', array_map(static function (string $search): string {
                return preg_quote($search, '/');
            }, $searches));
        };

        // A word starts after anything but a letter, a mark or an apostrophe.
        $initialBranch = $initial === [] ? '' : '(?<![\pL\pM\'’ʼ])(?<initial>' . $alternatives($initial) . ')|';

        // "previous" / "next" are set when an upper-case letter touches the match.
        return '/(?:(?<=\p{Lu})(?<previous>)|)(?:' . $initialBranch . '(?<other>' . $alternatives($map) . '))'
            . '(?:(?=\p{Lu})(?<next>)|)/iu';
    }

    /**
     * "Щ" => "Shch", but "ЩУКА" => "SHCHUKA".
     */
    private static function matchCase(string $replacement, string $source, bool $inUpperCaseWord): string
    {
        if ($replacement === '' || mb_strtolower($source, 'UTF-8') === $source) {
            return $replacement;
        }

        if ($inUpperCaseWord || (mb_strlen($source, 'UTF-8') > 1 && mb_strtoupper($source, 'UTF-8') === $source)) {
            return strtoupper($replacement);
        }

        return ucfirst($replacement);
    }
}
