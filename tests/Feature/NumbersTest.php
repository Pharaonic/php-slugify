<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class NumbersTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function numberProvider(): array
    {
        return [
            'arabic-indic' => ['١٢٣', '123'],
            'persian' => ['۱۲۳', '123'],
            'devanagari' => ['१२३', '123'],
            'bengali' => ['১২৩', '123'],
            'thai' => ['๑๒๓', '123'],
            'nko' => ['߁߂߃', '123'],
            'full-width' => ['１２３', '123'],
            'mathematical' => ['𝟏𝟐𝟑', '123'],
            'subscript' => ['₁₂₃', '123'],
            'superscript' => ['¹²³', '123'],
            'circled' => ['①②③', '123'],
            'circled twenty' => ['⑳', '20'],
            'parenthesized' => ['⑴⑵', '12'],
            'full stop' => ['⒈', '1'],
            'keycap' => ['1️⃣2️⃣', '12'],
            'keycap without selector' => ["1\u{20E3}", '1'],
            'arabic sentence' => ['الإصدار ١٢', 'الإصدار-12'],
            'persian sentence' => ['نسخه ۱۲', 'نسخه-12'],
            'chemical formula' => ['H₂O', 'h2o'],
            'unit' => ['10 m²', '10-m2'],
            'exponent stays a separate word' => ['10²', '10-2'],
            'fraction is not a plain number' => ['½', '½'],
            'roman numeral is kept' => ['Ⅻ', 'ⅻ'],
            'number without decomposition is kept' => ['❶', '❶'],
        ];
    }

    #[DataProvider('numberProvider')]
    public function testUnicodeMode(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
    }

    public function testArabicAndPersianDigitsGiveTheSameSlug(): void
    {
        $this->assertSame(Slugify::make('الفصل ٣'), Slugify::make('الفصل ۳'));
    }

    public function testAsciiModeDoesNotDependOnTheTransliterator(): void
    {
        $this->assertSame('alfsl-12', Slugify::make('الفصل ١٢', '-', true));
        $this->assertSame('123', Slugify::make('①②③', '-', true));
    }

    public function testNormalizationCanBeDisabled(): void
    {
        $this->assertSame('الفصل-٣', Slugify::of('الفصل ٣')->normalizeNumbers(false)->toString());
        $this->assertSame('m²', Slugify::of('m²')->normalizeNumbers(false)->toString());
    }

    public function testEveryUnicodeDecimalDigitIsCovered(): void
    {
        $unmapped = [];

        for ($codePoint = 0x80; $codePoint <= 0x1FFFF; $codePoint++) {
            if ($codePoint >= 0xD800 && $codePoint <= 0xDFFF) {
                continue; // surrogates are not characters
            }

            $character = (string) mb_chr($codePoint, 'UTF-8');

            if (preg_match('/^\p{Nd}$/u', $character) !== 1) {
                continue;
            }

            if (preg_match('/^[0-9]$/', Slugify::make($character)) !== 1) {
                $unmapped[] = sprintf('U+%04X', $codePoint);
            }
        }

        $this->assertSame([], $unmapped, 'Add the missing zeros to src/Resources/numbers.php.');
    }
}
