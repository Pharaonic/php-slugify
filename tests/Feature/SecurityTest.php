<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Arbitrary, untrusted input must always yield a well-formed slug.
 */
final class SecurityTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string, 2?: string}>
     */
    public static function inputProvider(): array
    {
        return [
            'null byte' => ["foo\0bar", 'foo-bar'],
            'control characters' => ["foo\x07\x1B[31mbar", 'foo-31mbar'],
            'delete' => ["foo\x7Fbar", 'foo-bar'],
            'invalid utf-8' => ["foo\xC3\x28bar", 'foo-bar'],
            'overlong encoding' => ["foo\xC0\xAFbar", 'foo-bar'],
            'utf-16 surrogate' => ["foo\xED\xA0\x80bar", 'foo-bar'],
            'truncated sequence' => ["foo\xE2\x82", 'foo'],
            'zero-width space is a boundary' => ["foo\u{200B}bar", 'foo-bar'],
            'zero-width joiner is removed' => ["foo\u{200D}bar", 'foobar'],
            'zero-width non-joiner is removed' => ["foo\u{200C}bar", 'foobar'],
            'word joiner is removed' => ["foo\u{2060}bar", 'foobar'],
            'soft hyphen is removed' => ["Stra\u{00AD}ße", 'straße', 'strasse'],
            'byte order mark' => ["\u{FEFF}foo", 'foo'],
            'bidi override' => ["\u{202E}foo\u{202C}bar", 'foobar'],
            'right-to-left mark' => ["foo\u{200F} bar", 'foo-bar'],
            'path characters' => ['../../etc/passwd', 'etc-passwd'],
            'windows path' => ['C:\\Windows\\System32', 'c-windows-system32'],
            'html' => ['<script>alert(1)</script>', 'script-alert-1-script'],
            'url' => ['https://example.com/a?b=c#d', 'https-example-com-a-b-c-d'],
            'line separator' => ["foo\u{2028}bar", 'foo-bar'],
            // portable-ascii drops characters it cannot transliterate.
            'private use' => ["foo\u{E000}bar", 'foo-bar', 'foobar'],
            'unassigned' => ["foo\u{0378}bar", 'foo-bar', 'foobar'],
            'orphan combining mark' => ["\u{0301}foo", 'foo'],
            'only invisible' => ["\u{200B}\u{200D}\u{FE0F}\u{2060}", ''],
            'zero' => ['0', '0'],
        ];
    }

    #[DataProvider('inputProvider')]
    public function testUntrustedInput(string $input, string $expected, ?string $expectedAscii = null): void
    {
        $this->assertSame($expected, Slugify::make($input));
        $this->assertSame($expectedAscii ?? $expected, Slugify::make($input, '-', true));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function rawInputProvider(): array
    {
        return array_map(static fn (array $case): array => [$case[0]], self::inputProvider());
    }

    #[DataProvider('rawInputProvider')]
    public function testOutputIsAlwaysValidAndVisible(string $input): void
    {
        $slug = Slugify::make($input);

        $this->assertTrue(mb_check_encoding($slug, 'UTF-8'));
        $this->assertSame(0, preg_match('/[\p{C}\p{Z}\x{FE00}-\x{FE0F}\x{FFFD}]/u', $slug));
        $this->assertMatchesRegularExpression('/^[a-z0-9-]*$/', Slugify::make($input, '-', true));
    }

    public function testRandomBytesNeverBreakTheSlug(): void
    {
        mt_srand(8);

        for ($i = 0; $i < 300; $i++) {
            $bytes = '';

            for ($j = 0, $length = mt_rand(1, 64); $j < $length; $j++) {
                $bytes .= chr(mt_rand(0, 255));
            }

            $slug = Slugify::make($bytes);

            $this->assertTrue(mb_check_encoding($slug, 'UTF-8'));
            $this->assertSame(0, preg_match('/[\p{C}\p{Z}\x{FFFD}]|^-|-$|--/u', $slug), bin2hex($bytes));
            $this->assertMatchesRegularExpression('/^[a-z0-9]+(?:-[a-z0-9]+)*$|^$/', Slugify::make($bytes, '-', true));
        }
    }

    public function testPathologicalInputStaysLinear(): void
    {
        $inputs = [
            str_repeat('a', 200000),
            str_repeat('-', 200000),
            str_repeat("\u{0301}", 50000),
            str_repeat('👨‍👩‍👧‍👦', 10000),
            str_repeat("\xFF", 100000),
            str_repeat('aB', 100000),
            str_repeat('١٢٣ ', 30000),
        ];

        foreach ($inputs as $input) {
            $start = microtime(true);
            Slugify::make($input);
            Slugify::make($input, '-', true);

            $this->assertLessThan(5.0, microtime(true) - $start);
        }
    }
}
