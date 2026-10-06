<?php

namespace Pharaonic\Slugify\Policies;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Support\Locale;
use voku\helper\ASCII;

/**
 * How symbols such as "&", "%", "+" or "©" are handled.
 *
 *   SymbolPolicy::remove()                // default: symbols are dropped ("R&D" => "r-d")
 *   SymbolPolicy::words()                 // spelled out in the slug's locale ("R&D" => "r-and-d")
 *   SymbolPolicy::custom(['&' => 'and'])  // only the given symbols are spelled out
 *
 * A symbol that the policy does not replace always becomes a word boundary.
 * Replacement words are always separate words in the slug.
 */
final class SymbolPolicy
{
    private const REMOVE = 'remove';
    private const WORDS = 'words';
    private const CUSTOM = 'custom';

    /**
     * @var array<string, array<string, string>> locale => symbol words, built lazily
     */
    private static array $words = [];

    /**
     * @param array<string, string> $map
     */
    private function __construct(private string $mode, private array $map = [])
    {
    }

    public static function remove(): self
    {
        return new self(self::REMOVE);
    }

    /**
     * Spell symbols out in the slug's locale, falling back to English.
     *
     * Words come from voku/portable-ascii ("&" => "and" / "und" / "ve", currencies, ...),
     * completed by "Resources/symbols.php".
     */
    public static function words(): self
    {
        return new self(self::WORDS);
    }

    /**
     * Spell out only the given symbols.
     *
     * @param array<array-key, string> $map symbol => word
     */
    public static function custom(array $map): self
    {
        $words = [];

        foreach ($map as $symbol => $word) {
            $symbol = (string) $symbol;

            if ($symbol === '') {
                throw InvalidArgumentException::emptyRule();
            }

            $words[$symbol] = ' ' . $word . ' ';
        }

        return new self(self::CUSTOM, $words);
    }

    public function apply(string $value, ?string $locale = null): string
    {
        switch ($this->mode) {
            case self::WORDS:
                return strtr($value, self::wordsFor($locale));
            case self::CUSTOM:
                return $this->map === [] ? $value : strtr($value, $this->map);
            default:
                return $value;
        }
    }

    /**
     * @return array<string, string>
     */
    private static function wordsFor(?string $locale): array
    {
        $language = Locale::language($locale) ?? 'en';

        if (!isset(self::$words[$language])) {
            $words = self::portableAsciiWords($language) ?: self::portableAsciiWords('en');
            $words += self::portableAsciiWords('currency');

            /** @var array<string, string> $fallback */
            $fallback = require __DIR__ . '/../Resources/symbols.php';

            foreach ($fallback as $symbol => $word) {
                $words[$symbol] ??= ' ' . $word . ' ';
            }

            self::$words[$language] = $words;
        }

        return self::$words[$language];
    }

    /**
     * The symbol words ("extras") voku/portable-ascii ships for a language.
     *
     * @return array<string, string>
     */
    private static function portableAsciiWords(string $language): array
    {
        /** @var array<string, string> $withExtras */
        $withExtras = ASCII::charsArrayWithOneLanguage($language, true, false);
        /** @var array<string, string> $withoutExtras */
        $withoutExtras = ASCII::charsArrayWithOneLanguage($language, false, false);

        return array_diff_key($withExtras, $withoutExtras);
    }
}
