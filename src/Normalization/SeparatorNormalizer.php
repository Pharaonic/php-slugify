<?php

namespace Pharaonic\Slugify\Normalization;

/**
 * Last pipeline stages: keeps the characters a slug may contain, splits the
 * text into words and joins them with the separator, honouring a max length.
 *
 * @internal
 */
final class SeparatorNormalizer
{
    /**
     * Invisible characters that must never survive into a slug: format characters
     * (ZWJ, ZWNJ, soft hyphen, bidi marks, BOM, emoji tags), enclosing marks
     * (the keycap U+20E3) and variation selectors.
     *
     * They are removed rather than treated as boundaries, so a soft hyphen or a
     * Persian ZWNJ inside a word does not split it.
     */
    private const string INVISIBLE = '/[\p{Cf}\p{Me}\x{FE00}-\x{FE0F}\x{E0100}-\x{E01EF}]+/u';

    /**
     * Split the text into words, dropping everything that is not a letter, a
     * number or (in Unicode mode) a combining mark attached to a word.
     *
     * @return list<string>
     */
    public static function words(string $value, bool $ascii): array
    {
        if ($ascii) {
            return preg_split('/[^A-Za-z0-9]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        if (preg_match('/[^\x00-\x7F]/', $value) === 1) {
            $value = (string) preg_replace(self::INVISIBLE, '', $value);
        }

        $words = [];

        foreach (preg_split('/[^\pL\pN\pM]+/u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            // Orphan marks (with no letter to attach to) are not part of a word.
            $word = (string) preg_replace('/^\pM+/u', '', $word);

            if ($word !== '') {
                $words[] = $word;
            }
        }

        return $words;
    }

    /**
     * Join the words, keeping whole words when a max length is set.
     *
     * Only a first word that alone exceeds the limit is cut, and then only
     * between grapheme clusters, so a letter never loses its combining marks.
     *
     * @param list<string> $words
     */
    public static function join(array $words, string $separator, ?int $maxLength = null): string
    {
        $slug = implode($separator, $words);

        if ($maxLength === null || $words === [] || mb_strlen($slug, 'UTF-8') <= $maxLength) {
            return $slug;
        }

        $first = array_shift($words);

        if (mb_strlen($first, 'UTF-8') >= $maxLength) {
            return self::truncate($first, $maxLength);
        }

        $slug = $first;

        foreach ($words as $word) {
            $candidate = $slug . $separator . $word;

            if (mb_strlen($candidate, 'UTF-8') > $maxLength) {
                break;
            }

            $slug = $candidate;
        }

        return $slug;
    }

    /**
     * The longest run of whole grapheme clusters within the given number of characters.
     */
    private static function truncate(string $word, int $maxLength): string
    {
        preg_match_all('/\X/u', $word, $graphemes);

        $result = '';
        $length = 0;

        foreach ($graphemes[0] as $grapheme) {
            $length += mb_strlen($grapheme, 'UTF-8');

            if ($length > $maxLength) {
                break;
            }

            $result .= $grapheme;
        }

        return $result;
    }
}
