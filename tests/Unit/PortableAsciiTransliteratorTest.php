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
