<?php

namespace Pharaonic\Slugify\Policies;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;

/**
 * How emoji are handled.
 *
 *   EmojiPolicy::remove()                  // default: "PHP 🚀 Rocks" => "php-rocks"
 *   EmojiPolicy::custom(['🚀' => 'rocket']) // "PHP 🚀 Rocks" => "php-rocket-rocks"
 *
 * Emoji are matched as whole grapheme clusters, so multi-code-point sequences
 * (ZWJ families, skin tones, flags, keycaps, tag sequences, "❤️") are always
 * removed or replaced as one unit and never leave a joiner, variation selector
 * or modifier behind.
 *
 * Custom words match an emoji with or without its skin tone or presentation
 * selector: ['👍' => 'thumbs-up'] also covers "👍🏽" and "👍️".
 *
 * The package ships no emoji names: semantic conversion is opt-in, with your words.
 */
final class EmojiPolicy
{
    /**
     * Characters that may start or belong to an emoji sequence; text without any
     * of them is returned untouched.
     */
    private const CANDIDATES = '/[\x{2300}-\x{2BFF}\x{3030}\x{303D}\x{3297}\x{3299}\x{FE0F}\x{20E3}'
        . '\x{1F000}-\x{1FAFF}\x{E0020}-\x{E007F}]/u';

    /**
     * A grapheme cluster that is an emoji: it starts with a pictograph (but not a
     * number such as "❶"), a regional indicator or emoji tag, or it is a symbol in
     * emoji presentation ("©️", "‼️") or a "#"/"*" keycap. Digit keycaps are numbers
     * (NumberNormalizer).
     */
    private const EMOJI = '/(?=(?!\pN)[\x{1F000}-\x{1FAFF}\x{2300}-\x{23FF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}'
        . '\x{3030}\x{303D}\x{3297}\x{3299}\x{E0020}-\x{E007F}]|[\p{S}\p{Po}]\x{FE0F}|[#*]\x{FE0F}?\x{20E3})\X/u';

    /**
     * Variation selectors and skin-tone modifiers, ignored when looking up custom words.
     */
    private const VARIANTS = '/[\x{FE0E}\x{FE0F}\x{1F3FB}-\x{1F3FF}]/u';

    /**
     * @param array<string, string> $map
     */
    private function __construct(private readonly array $map = [])
    {
    }

    public static function remove(): self
    {
        return new self();
    }

    /**
     * Replace the given emoji with words; any other emoji is removed.
     *
     * @param array<array-key, string> $map emoji => word
     */
    public static function custom(array $map): self
    {
        $words = [];

        foreach ($map as $emoji => $word) {
            $emoji = (string) preg_replace(self::VARIANTS, '', (string) $emoji);

            if ($emoji === '') {
                throw InvalidArgumentException::emptyRule();
            }

            $words[$emoji] = ' ' . $word . ' ';
        }

        return new self($words);
    }

    public function apply(string $value): string
    {
        if (preg_match(self::CANDIDATES, $value) !== 1) {
            return $value;
        }

        $map = $this->map;

        return (string) preg_replace_callback(
            self::EMOJI,
            static fn (array $match): string => $map === []
                ? ' '
                : $map[$match[0]] ?? $map[(string) preg_replace(self::VARIANTS, '', $match[0])] ?? ' ',
            $value
        );
    }
}
