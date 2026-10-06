<?php

namespace Pharaonic\Slugify;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Policies\EmojiPolicy;
use Pharaonic\Slugify\Policies\SymbolPolicy;

/**
 * Plain options bag describing how a slug is generated.
 *
 * Every concern is a separate option: the locale never implies ASCII output,
 * and symbols, emoji and numbers each have their own policy.
 */
final class SlugOptions
{
    /**
     * How symbols ("&", "%", "©") are handled. Defaults to SymbolPolicy::remove().
     */
    public SymbolPolicy $symbols;

    /**
     * How emoji are handled. Defaults to EmojiPolicy::remove().
     */
    public EmojiPolicy $emoji;

    /**
     * @param string            $separator        Joins the words of the slug. May be empty.
     * @param bool              $lowercase        Lowercase the result (multibyte and locale aware).
     * @param bool              $ascii            Transliterate the result to ASCII.
     * @param string|null       $language         Locale (e.g. "de", "tr", "uk-UA") for language-specific
     *                                            behavior. It never enables ASCII output by itself.
     * @param bool              $splitCamelCase   Split "camelCase" and "PascalCase" words.
     * @param int|null          $maxLength        Maximum length in characters, or null for no limit.
     * @param SymbolPolicy|null $symbols          How symbols are handled; null means remove().
     * @param EmojiPolicy|null  $emoji            How emoji are handled; null means remove().
     * @param bool              $normalizeNumbers Convert Unicode digits ("١٢", "²", "①") to ASCII digits.
     */
    public function __construct(
        public string $separator = '-',
        public bool $lowercase = true,
        public bool $ascii = false,
        public ?string $language = null,
        public bool $splitCamelCase = true,
        public ?int $maxLength = null,
        ?SymbolPolicy $symbols = null,
        ?EmojiPolicy $emoji = null,
        public bool $normalizeNumbers = true
    ) {
        $this->symbols = $symbols ?? SymbolPolicy::remove();
        $this->emoji = $emoji ?? EmojiPolicy::remove();
    }

    /**
     * Ensure the options can produce a well-formed slug.
     *
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if (preg_match('/[\pL\pN\pM\s]/u', $this->separator) !== 0) {
            throw InvalidArgumentException::separator($this->separator);
        }

        if ($this->maxLength !== null && $this->maxLength < 1) {
            throw InvalidArgumentException::maxLength($this->maxLength);
        }
    }
}
