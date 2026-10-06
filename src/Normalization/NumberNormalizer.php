<?php

namespace Pharaonic\Slugify\Normalization;

use Normalizer;

/**
 * Normalizes unambiguous Unicode number forms to ASCII digits, in every mode.
 *
 *   ١٢٣ / ۱۲۳ / ١٢٣ / １２３ / ߁߂߃ ...  => 123   (decimal digits of any script)
 *   ¹²³ / ₁₂₃ / ①②③ / ⑴ / ⒈           => 123   (compatibility numerals)
 *   1️⃣                                  => 1     (keycap sequences)
 *
 * Arabic-Indic (U+0660) and Persian (U+06F0) digits look alike but are distinct
 * code points; normalizing both keeps "الفصل ٣" and "الفصل ۳" on the same slug.
 *
 * Forms whose meaning is not a plain number are left alone: fractions ("½"),
 * Roman numerals ("Ⅻ") and numbers without a decomposition ("❶").
 *
 * A compatibility numeral right after an ASCII digit is kept as a separate
 * word, so an exponent never merges into the number: "10²" => "10 2", not "102".
 *
 * @internal
 */
final class NumberNormalizer
{
    /**
     * @var array<int, int>|null code point => digit, built lazily from "Resources/numbers.php"
     */
    private static ?array $digits = null;

    /**
     * @var array<string, string>
     */
    private static array $compatibility = [];

    public static function normalize(string $value): string
    {
        if (preg_match('/[^\x00-\x7F]/', $value) !== 1) {
            return $value;
        }

        $value = (string) preg_replace('/([0-9])\x{FE0F}?\x{20E3}/u', '$1', $value);

        $value = (string) preg_replace_callback(
            '/(?![0-9])\p{Nd}/u',
            static fn (array $match): string => self::digit($match[0]),
            $value
        );

        return (string) preg_replace_callback('/([0-9]?)(\p{No}+)/u', static function (array $match): string {
            $numerals = '';

            foreach (mb_str_split($match[2], 1, 'UTF-8') as $character) {
                $numerals .= self::compatibility($character);
            }

            $separator = $match[1] !== '' && preg_match('/^[0-9]/', $numerals) === 1 ? ' ' : '';

            return $match[1] . $separator . $numerals;
        }, $value);
    }

    private static function digit(string $character): string
    {
        if (self::$digits === null) {
            self::$digits = [];

            /** @var list<int> $zeros */
            $zeros = require __DIR__ . '/../Resources/numbers.php';

            foreach ($zeros as $zero) {
                for ($digit = 0; $digit <= 9; $digit++) {
                    self::$digits[$zero + $digit] = $digit;
                }
            }
        }

        $digit = self::$digits[mb_ord($character, 'UTF-8')] ?? null;

        return $digit === null ? $character : (string) $digit;
    }

    private static function compatibility(string $character): string
    {
        if (!array_key_exists($character, self::$compatibility)) {
            $decomposed = (string) Normalizer::normalize($character, Normalizer::FORM_KC);

            // "²" => "2", "⑴" => "(1)", "⒈" => "1."; but "½" => "1⁄2" is not a plain number.
            self::$compatibility[$character] = preg_match('/^\(?([0-9]+)[.)]?$/', $decomposed, $match) === 1
                ? $match[1]
                : $character;
        }

        return self::$compatibility[$character];
    }
}
