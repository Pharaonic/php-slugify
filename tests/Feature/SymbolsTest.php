<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Policies\SymbolPolicy;
use Pharaonic\Slugify\SlugOptions;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class SymbolsTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function defaultProvider(): array
    {
        return [
            'ampersand' => ['R&D', 'r-d'],
            'plus' => ['C++', 'c'],
            'hash' => ['C# #1', 'c-1'],
            'percent' => ['50%', '50'],
            'legal marks' => ['Acme© Widget® Pro™', 'acme-widget-pro'],
            'currency' => ['€100 $5 £3', '100-5-3'],
            'math' => ['1+1=2', '1-1-2'],
            'legacy "@" rule' => ['user@host', 'user-at-host'],
        ];
    }

    #[DataProvider('defaultProvider')]
    public function testSymbolsAreRemovedByDefault(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
        $this->assertSame($expected, Slugify::of($input)->symbols(SymbolPolicy::remove())->toString());
    }

    /**
     * @return array<string, array{string, string|null, string}>
     */
    public static function wordsProvider(): array
    {
        return [
            'ampersand' => ['R&D', null, 'r-and-d'],
            'plus' => ['1+1=2', null, '1-plus-1-equal-2'],
            'percent' => ['50% off', null, '50-percent-off'],
            'currency' => ['€100', null, 'euro-100'],
            'legal marks' => ['Acme© Pro™', null, 'acme-copyright-pro-trademark'],
            'hash is ambiguous and removed' => ['C#', null, 'c'],
            'german' => ['R&D 50%', 'de', 'r-und-d-50-prozent'],
            'turkish' => ['R&D', 'tr', 'r-ve-d'],
            'locale without words falls back to english' => ['R&D', 'ja', 'r-and-d'],
            'regional locale' => ['R&D', 'de-AT', 'r-und-d'],
        ];
    }

    #[DataProvider('wordsProvider')]
    public function testWords(string $input, ?string $locale, string $expected): void
    {
        $this->assertSame($expected, Slugify::of($input)->locale($locale)->symbols(SymbolPolicy::words())->toString());
    }

    public function testWordsInAsciiMode(): void
    {
        $slug = Slugify::of('Äpfel & Birnen')->locale('de')->ascii()->symbols(SymbolPolicy::words())->toString();

        $this->assertSame('aepfel-und-birnen', $slug);
    }

    public function testCustom(): void
    {
        $policy = SymbolPolicy::custom(['&' => 'and', '#' => 'sharp']);

        $this->assertSame('r-and-d', Slugify::of('R&D')->symbols($policy)->toString());
        $this->assertSame('c-sharp-c', Slugify::of('C# C++')->symbols($policy)->toString());
        $this->assertSame('50', Slugify::of('50%')->symbols($policy)->toString(), 'Unlisted symbols are removed');
    }

    public function testCustomRejectsEmptySymbols(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SymbolPolicy::custom(['' => 'nothing']);
    }

    public function testCustomReplacementsTakePrecedenceOverThePolicy(): void
    {
        $slug = Slugify::of('R&D C++')
            ->symbols(SymbolPolicy::words())
            ->rules(['&' => ' n ', 'c++' => 'cpp'])
            ->toString();

        $this->assertSame('r-n-d-cpp', $slug);
    }

    public function testCustomReplacementsTakePrecedenceOverThePolicyInAsciiMode(): void
    {
        $slug = Slugify::of('R&D C++')
            ->ascii()
            ->symbols(SymbolPolicy::words())
            ->rules(['&' => ' n ', 'c++' => 'cpp'])
            ->toString();

        $this->assertSame('r-n-d-cpp', $slug);
    }

    public function testPolicyThroughOptions(): void
    {
        $options = new SlugOptions(symbols: SymbolPolicy::words());

        $this->assertSame('r-and-d', Slugify::of('R&D', $options)->toString());
    }
}
