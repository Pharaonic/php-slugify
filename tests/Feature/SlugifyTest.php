<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class SlugifyTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function basicProvider(): array
    {
        return [
            'words' => ['Hello World', 'hello-world'],
            'already a slug' => ['hello-world', 'hello-world'],
            'underscores' => ['hello_world', 'hello-world'],
            'multiple spaces' => ['hello     world', 'hello-world'],
            'leading/trailing spaces' => ["  \t hello world \n ", 'hello-world'],
            'repeated dashes' => ['hello---world', 'hello-world'],
            'repeated underscores' => ['hello___world', 'hello-world'],
            'mixed separators' => ['hello - _ world', 'hello-world'],
            'repeated punctuation' => ['Wait... what?!?', 'wait-what'],
            'numbers' => ['Top 10 Tips for 2026', 'top-10-tips-for-2026'],
        ];
    }

    #[DataProvider('basicProvider')]
    public function testBasicSlugs(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function separatorProvider(): array
    {
        return [
            'dash' => ['-', 'hello-big-world'],
            'underscore' => ['_', 'hello_big_world'],
            'dot' => ['.', 'hello.big.world'],
            'tilde' => ['~', 'hello~big~world'],
            'empty' => ['', 'hellobigworld'],
            'multi-character' => ['--', 'hello--big--world'],
            'non-ascii' => ['·', 'hello·big·world'],
        ];
    }

    #[DataProvider('separatorProvider')]
    public function testSeparators(string $separator, string $expected): void
    {
        $this->assertSame($expected, Slugify::make('Hello big World', $separator));
    }

    #[DataProvider('separatorProvider')]
    public function testExistingSeparatorsAreNormalized(string $separator, string $expected): void
    {
        $this->assertSame($expected, Slugify::make('-hello__big.. ~world-', $separator));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidSeparatorProvider(): array
    {
        return [
            'letter' => ['x'],
            'digit' => ['1'],
            'space' => [' '],
            'unicode letter' => ['ب'],
        ];
    }

    #[DataProvider('invalidSeparatorProvider')]
    public function testInvalidSeparatorsAreRejected(string $separator): void
    {
        $this->expectException(InvalidArgumentException::class);

        Slugify::make('Hello World', $separator);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function edgeCaseProvider(): array
    {
        return [
            'empty' => ['', ''],
            'zero' => ['0', '0'],
            'zero with spaces' => [' 0 ', '0'],
            'dashes only' => ['---', ''],
            'underscores only' => ['___', ''],
            'spaces only' => ["   \t\n", ''],
            'emoji only' => ['😀 🎉', ''],
            'emoji with variation selector' => ['I ❤️ PHP', 'i-php'],
            'emoji zwj sequence' => ['👨‍👩‍👧 family', 'family'],
            'symbols only' => ['#$%^&*()', ''],
            'invalid utf-8' => ["abc\xFF\xFEdef", 'abc-def'],
        ];
    }

    #[DataProvider('edgeCaseProvider')]
    public function testEdgeCases(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
    }

    public function testVeryLongInput(): void
    {
        $input = str_repeat('Lorem ipsum dolor sit amet ', 2000);

        $slug = Slugify::make($input);

        $this->assertStringStartsWith('lorem-ipsum-dolor-sit-amet-lorem', $slug);
        $this->assertStringEndsWith('sit-amet', $slug);
        $this->assertSame(2000 * 27 - 1, strlen($slug));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function camelCaseProvider(): array
    {
        return [
            'camelCase' => ['helloWorld', 'hello-world'],
            'PascalCase' => ['HelloWorld', 'hello-world'],
            'leading acronym' => ['XMLHttpRequest', 'xml-http-request'],
            'acronym then word' => ['APIResponse', 'api-response'],
            'trailing acronym' => ['getUserID', 'get-user-id'],
            'trailing acronym 2' => ['PharaonicPHP', 'pharaonic-php'],
            'acronym only' => ['FAQ', 'faq'],
            'acronym in sentence' => ['There is FAQ module here', 'there-is-faq-module-here'],
            'digits' => ['Version2Beta', 'version2-beta'],
            'digit then acronym' => ['3D Printing', '3d-printing'],
            'unicode letters' => ['ÉtéÀParis', 'été-à-paris'],
            'greek' => ['καλημέραΚόσμε', 'καλημέρα-κόσμε'],
            'snake_case' => ['snake_case_value', 'snake-case-value'],
        ];
    }

    #[DataProvider('camelCaseProvider')]
    public function testCamelCaseAndAcronyms(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
    }
}
