<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Normalization\UnicodeNormalizer;
use PHPUnit\Framework\TestCase;

final class UnicodeNormalizerTest extends TestCase
{
    public function testValidTextIsUntouched(): void
    {
        $this->assertSame('Café مرحبا 你好', UnicodeNormalizer::normalize('Café مرحبا 你好'));
    }

    public function testInvalidUtf8BecomesABoundary(): void
    {
        $normalized = UnicodeNormalizer::normalize("abc\xFF\xFEdef\xC3");

        $this->assertTrue(mb_check_encoding($normalized, 'UTF-8'));
        $this->assertSame('abc  def ', $normalized);
    }

    public function testDecomposedTextIsComposed(): void
    {
        $this->assertSame('Café', UnicodeNormalizer::normalize("Cafe\u{0301}"));
        $decomposed = "\u{0391}\u{0313}\u{03B8}\u{03B7}\u{0342}\u{03BD}\u{03B1}\u{03B9}";

        $this->assertSame('Ἀθῆναι', UnicodeNormalizer::normalize($decomposed));
    }

    /**
     * The NFC fast path must never skip a character that NFC would change.
     */
    public function testNfcFastPathCoversEveryCharacterNfcChanges(): void
    {
        $missed = [];

        for ($codePoint = 0x80; $codePoint <= 0x2FFFF; $codePoint++) {
            if ($codePoint >= 0xD800 && $codePoint <= 0xDFFF) {
                continue; // surrogates are not characters
            }

            $character = (string) mb_chr($codePoint, 'UTF-8');

            if (
                preg_match(UnicodeNormalizer::MAY_CHANGE_UNDER_NFC, $character) !== 1
                && \Normalizer::normalize($character) !== $character
            ) {
                $missed[] = sprintf('U+%04X', $codePoint);
            }

            // Anything that can follow a starter inside a composition must be caught too.
            $decomposed = (string) \Normalizer::normalize($character, \Normalizer::FORM_D);

            foreach (array_slice(mb_str_split($decomposed), 1) as $part) {
                if (preg_match(UnicodeNormalizer::MAY_CHANGE_UNDER_NFC, $part) !== 1) {
                    $missed[] = sprintf('U+%04X (in U+%04X)', mb_ord($part), $codePoint);
                }
            }
        }

        $this->assertSame([], $missed);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function boundaryProvider(): array
    {
        return [
            'null byte' => ["a\0b", 'a b'],
            'control characters' => ["a\x07b\x1Bc\x7Fd", 'a b c d'],
            'line separator' => ["a\u{2028}b", 'a b'],
            'paragraph separator' => ["a\u{2029}b", 'a b'],
            'zero-width space' => ["a\u{200B}b", 'a b'],
        ];
    }

    /**
     * @dataProvider boundaryProvider
     */
    public function testBoundaries(string $input, string $expected): void
    {
        $this->assertSame($expected, UnicodeNormalizer::normalize($input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function compatibilityProvider(): array
    {
        return [
            'latin ligature' => ['ﬁnance', 'finance'],
            'full-width latin' => ['Ｈｅｌｌｏ', 'Hello'],
            'half-width katakana with voiced mark' => ['ｶﾞｷﾞ', 'ガギ'],
            'arabic presentation form' => ['ﻻ', 'لا'],
            'mathematical bold' => ['𝐇𝐞𝐥𝐥𝐨', 'Hello'],
            'letter-like letter' => ['ℍ', 'H'],
            'letter-like symbol is kept' => ['™ ℃ №', '™ ℃ №'],
            'fraction is kept' => ['½', '½'],
        ];
    }

    /**
     * @dataProvider compatibilityProvider
     */
    public function testCompatibilityForms(string $input, string $expected): void
    {
        $this->assertSame($expected, UnicodeNormalizer::normalize($input));
    }

    public function testScriptMarksAreRemoved(): void
    {
        $this->assertSame('محمد', UnicodeNormalizer::normalize('مُحَمَّد'));
        $this->assertSame('محمد', UnicodeNormalizer::normalize('مـحـمـد'));
        $this->assertSame('שלום', UnicodeNormalizer::normalize('שָׁלוֹם'));
        $this->assertSame('الحمد', UnicodeNormalizer::normalize('ٱلْحَمْدُ'));
    }

    public function testHebrewPunctuationIsKept(): void
    {
        // Maqaf (Hebrew hyphen) must remain a word boundary.
        $this->assertSame("בית\u{05BE}ספר", UnicodeNormalizer::normalize("בֵּית\u{05BE}סֵפֶר"));
    }
}
