<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Support\Locale;
use PHPUnit\Framework\TestCase;

final class LocaleTest extends TestCase
{
    /**
     * @return array<string, array{string|null, string|null}>
     */
    public function languageProvider(): array
    {
        return [
            'language' => ['de', 'de'],
            'upper case' => ['DE', 'de'],
            'region with dash' => ['de-DE', 'de'],
            'region with underscore' => ['de_AT', 'de'],
            'script and region' => ['sr-Latn-RS', 'sr'],
            'three letters' => ['fil', 'fil'],
            'null' => [null, null],
            'empty' => ['', null],
            'too long' => ['german', null],
            'path traversal' => ['../de', null],
            'directory separator' => ['de/../../x', null],
            'null byte' => ["de\0", null],
            'trailing newline' => ["de\n", null],
        ];
    }

    /**
     * @dataProvider languageProvider
     */
    public function testLanguage(?string $locale, ?string $expected): void
    {
        $this->assertSame($expected, Locale::language($locale));
    }

    public function testOverrides(): void
    {
        $this->assertSame(['I' => 'ı', 'İ' => 'i'], Locale::overrides('tr-TR', 'lowercase'));
        $this->assertSame('zh', Locale::overrides('uk', 'ascii')['ж']);
        $this->assertSame([], Locale::overrides('tr', 'ascii'));
        $this->assertSame([], Locale::overrides('de', 'lowercase'), 'no file, no overrides');
        $this->assertSame([], Locale::overrides(null, 'lowercase'));
    }
}
