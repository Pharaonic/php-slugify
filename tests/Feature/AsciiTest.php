<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class AsciiTest extends TestCase
{
    /**
     * @return array<string, array{string, string|null, string}>
     */
    public static function asciiProvider(): array
    {
        return [
            'french' => ['Crème brûlée', null, 'creme-brulee'],
            'german default' => ['München', null, 'munchen'],
            'german "de"' => ['München', 'de', 'muenchen'],
            'umlauts default' => ['Äpfel und Öl', null, 'apfel-und-ol'],
            'umlauts "de"' => ['Äpfel und Öl', 'de', 'aepfel-und-oel'],
            'russian' => ['Привет мир', null, 'privet-mir'],
            'arabic' => ['مرحبا', null, 'mrhba'],
            'greek' => ['Γειά σου', null, 'gheia-soy'],
            'greek is case-independent' => ['ΓΕΙΆ ΣΟΥ', null, 'gheia-soy'],
            'chinese' => ['你好世界', null, 'ni-hao-shi-jie'],
            'korean' => ['한국어', null, 'hangugeo'],
            'persian digits' => ['۱۲۳', null, '123'],
            'arabic-indic digits' => ['١٢٣', null, '123'],
            'scandinavian' => ['Ørsted Æble', null, 'orsted-aeble'],
            'turkish' => ['İstanbul çay şeker', null, 'istanbul-cay-seker'],
            'locale-style language' => ['München', 'de-DE', 'muenchen'],
            'uppercase language' => ['München', 'DE', 'muenchen'],
            'unknown language falls back' => ['Crème', 'xx', 'creme'],
            'camel case uppercase output' => ['ÉtéÀParis', null, 'ete-a-paris'],
        ];
    }

    #[DataProvider('asciiProvider')]
    public function testAsciiTransliteration(string $input, ?string $language, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input, '-', true, $language));
        $this->assertSame($expected, Slugify::of($input)->ascii($language)->toString());
    }

    public function testAsciiOutputIsAlwaysAscii(): void
    {
        $slug = Slugify::make('Hindi नमस्ते, emoji 😀, symbols ©®™, hebrew שלום', '-', true);

        $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $slug);
    }

    public function testTransliterationIsNotAppliedInUnicodeMode(): void
    {
        $this->assertSame('crème', Slugify::make('Crème'));
    }
}
