<?php

namespace Pharaonic\Slugify\Normalization;

use Normalizer;

/**
 * First pipeline stage: turns arbitrary input into well-formed, canonical UTF-8.
 *
 * 1. Invalid UTF-8 byte sequences become word boundaries.
 * 2. Control characters, line/paragraph separators and the zero-width space
 *    become word boundaries.
 * 3. The text is composed to NFC, so "e" + U+0301 and "é" are the same.
 * 4. Presentation-only compatibility forms are folded (NFKC) where they never
 *    change meaning: ligatures, full/half-width forms, Arabic presentation forms,
 *    mathematical alphanumerics and letter-like letters ("ﬁ" => "fi", "Ａ" => "A").
 *    Symbols such as "™" or "½" are left to their own policies.
 * 5. Script-level marks listed in "Resources/common.php" are removed.
 *
 * @internal
 */
final class UnicodeNormalizer
{
    private const BOUNDARIES = '/[\p{Cc}\x{2028}\x{2029}\x{200B}]+/u';

    /**
     * Characters NFC may change: combining marks, Hangul conjoining jamo, and the
     * characters whose canonical decomposition is a singleton or excluded from
     * composition. Text without any of them is already NFC, which spares a full
     * normalization pass (costly without ext-intl). Verified against the
     * normalization tables by UnicodeNormalizerTest.
     */
    public const MAY_CHANGE_UNDER_NFC = '/[\pM\x{0340}-\x{0344}\x{0374}\x{037E}\x{0387}\x{0958}-\x{095F}'
        . '\x{09DC}\x{09DD}\x{09DF}\x{0A33}\x{0A36}\x{0A59}-\x{0A5B}\x{0A5E}\x{0B5C}\x{0B5D}\x{0F43}-\x{0FB9}'
        . '\x{1100}-\x{11FF}\x{1F71}-\x{1FFD}\x{2000}\x{2001}\x{2126}\x{212A}\x{212B}\x{2329}\x{232A}\x{2ADC}'
        . '\x{A960}-\x{A97F}\x{D7B0}-\x{D7FF}\x{F900}-\x{FAFF}\x{FB1D}-\x{FB4F}\x{1D15E}-\x{1D1C0}'
        . '\x{2F800}-\x{2FA1F}]/u';

    private const COMPATIBILITY_FORMS = '/(?:[\x{FB00}-\x{FDFF}\x{FE70}-\x{FEFE}\x{FF00}-\x{FFEF}\x{1D400}-\x{1D7FF}]'
        . '|(?=\pL)[\x{2100}-\x{214F}])+/u';

    private const VALID_UTF8 = '/((?:[\x00-\x7F]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]'
        . '|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}'
        . '|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+)|./s';

    /**
     * @var array<string, string>|null
     */
    private static ?array $common = null;

    public static function normalize(string $value): string
    {
        // Printable ASCII is already canonical.
        if (preg_match('/[^\x20-\x7E]/', $value) !== 1) {
            return $value;
        }

        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = self::replaceInvalidBytes($value);
        }

        $value = (string) preg_replace(self::BOUNDARIES, ' ', $value);

        if (preg_match(self::MAY_CHANGE_UNDER_NFC, $value) === 1) {
            $value = (string) Normalizer::normalize($value);
        }

        $value = (string) preg_replace_callback(
            self::COMPATIBILITY_FORMS,
            static function (array $match): string {
                return (string) Normalizer::normalize($match[0], Normalizer::FORM_KC);
            },
            $value
        );

        return strtr($value, self::$common ??= require __DIR__ . '/../Resources/common.php');
    }

    private static function replaceInvalidBytes(string $value): string
    {
        return (string) preg_replace_callback(
            self::VALID_UTF8,
            static function (array $match): string {
                return ($match[1] ?? '') !== '' ? $match[1] : ' ';
            },
            $value
        );
    }
}
