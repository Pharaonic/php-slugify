<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Transliteration\PortableAsciiTransliterator;
use PHPUnit\Framework\TestCase;

final class PortableAsciiTransliteratorTest extends TestCase
{
    public function testImplementsTheContract(): void
    {
        $this->assertInstanceOf(Transliterator::class, new PortableAsciiTransliterator());
    }

    /**
     * @return array<string, array{string, string|null, string}>
     */
    public function transliterationProvider(): array
    {
        return [
            'latin' => ['Crème brûlée', null, 'Creme brulee'],
            'german default' => ['Äpfel', null, 'Apfel'],
            'german' => ['Äpfel', 'de', 'Aepfel'],
            'cyrillic' => ['Привет', null, 'Privet'],
            'chinese fallback' => ['你好', null, 'Ni Hao '],
            'ascii untouched' => ['Hello @ 1', null, 'Hello @ 1'],
            'region falls back to the language' => ['Äpfel', 'de-DE', 'Aepfel'],
            'known regional variant' => ['Straße', 'de-AT', 'Strasze'],
            'script subtag falls back to the language' => ['Đorđe', 'sr-Latn', 'Djordje'],
            'unknown language is generic' => ['Äpfel', 'xx-YY', 'Apfel'],
            // Same result with portable-ascii 1.x and 2.x (Resources/portable-ascii-1.php).
            'cyrillic e keeps the word whole' => ['Мэр', null, 'Mer'],
            'cyrillic generic letters' => ['ёлка объявление', null, 'elka obieiavlenie'],
            'cyrillic language map wins' => ['ёлка', 'ru', 'yolka'],
            'persian peh' => ['پ', null, 'p'],
            'persian language map' => ['پنجره', 'fa', 'pnjrh'],
        ];
    }

    /**
     * @dataProvider transliterationProvider
     */
    public function testTransliterate(string $input, ?string $language, string $expected): void
    {
        $this->assertSame($expected, (new PortableAsciiTransliterator())->transliterate($input, $language));
    }
}
