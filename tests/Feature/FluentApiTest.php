<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\SlugOptions;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Slugger;
use Pharaonic\Slugify\Tests\Fixtures\UppercaseTransliterator;
use Pharaonic\Slugify\Tests\TestCase;

final class FluentApiTest extends TestCase
{
    public function testOfReturnsAStringableBuilder(): void
    {
        $slug = Slugify::of('Hello World');

        $this->assertInstanceOf(Slugger::class, $slug);
        $this->assertSame('hello-world', $slug->toString());
        $this->assertSame('hello-world', (string) $slug);
    }

    public function testDocumentedExamples(): void
    {
        $this->assertSame('hello_world', Slugify::of('Hello World')->separator('_')->lowercase()->toString());
        $this->assertSame('creme-brulee', Slugify::of('Crème brûlée')->ascii()->toString());
        $this->assertSame('aepfel-und-oel', Slugify::of('Äpfel und Öl')->ascii('de')->toString());
        $this->assertSame('hello-world', Slugify::of('HelloWorld')->splitCamelCase()->toString());
        $this->assertSame('500-dollar-bill', Slugify::of('500$ Bill')->rule('$', ' dollar ')->toString());
    }

    public function testLowercaseCanBeDisabled(): void
    {
        $this->assertSame('Hello-World', Slugify::of('Hello World')->lowercase(false)->toString());
        $this->assertSame('Creme-Brulee', Slugify::of('Crème Brûlée')->lowercase(false)->ascii()->toString());
    }

    public function testCamelCaseSplittingCanBeDisabled(): void
    {
        $this->assertSame('helloworld', Slugify::of('helloWorld')->splitCamelCase(false)->toString());
        $this->assertSame('xmlhttprequest', Slugify::of('XMLHttpRequest')->splitCamelCase(false)->toString());
    }

    public function testUnicodeSwitchesAsciiModeOff(): void
    {
        $this->assertSame('crème', Slugify::of('Crème')->ascii('fr')->unicode()->toString());
    }

    public function testBuilderIsImmutable(): void
    {
        $base = Slugify::of('Hello World');
        $underscored = $base->separator('_');
        $ascii = $base->ascii();
        $ruled = $base->rule('world', 'there');

        $this->assertSame('hello-world', $base->toString());
        $this->assertSame('hello_world', $underscored->toString());
        $this->assertTrue($ascii->options()->ascii);
        $this->assertFalse($base->options()->ascii);
        $this->assertSame('hello-there', $ruled->toString());
    }

    public function testOptionsObject(): void
    {
        $options = new SlugOptions('_', true, true, 'de');

        $this->assertSame('muenchen_ist_schoen', Slugify::of('München ist schön', $options)->toString());
    }

    public function testOptionsObjectIsCopiedNotShared(): void
    {
        $options = new SlugOptions();
        $slug = Slugify::of('Hello World', $options);

        $options->separator = '_';
        $slug->options()->separator = '.';

        $this->assertSame('hello-world', $slug->toString());
    }

    public function testInvalidSeparatorFromBuilder(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Slugify::of('Hello')->separator('a')->toString();
    }

    public function testCustomTransliteratorPerSlug(): void
    {
        $slug = Slugify::of('Hello')->ascii('xx')->transliterator(new UppercaseTransliterator())->toString();

        $this->assertSame('hello-xx', $slug);
    }

    public function testGlobalTransliterator(): void
    {
        Slugify::useTransliterator(new UppercaseTransliterator());

        $this->assertSame('hello', Slugify::make('Hello', '-', true));
        $this->assertSame('hello', Slugify::make('Hello'), 'Unicode mode never transliterates');

        Slugify::useTransliterator(null);

        $this->assertSame('creme', Slugify::make('Crème', '-', true));
    }

    /**
     * @return array<string, array{string, int, string, string}>
     */
    public function maxLengthProvider(): array
    {
        return [
            'shorter than limit' => ['Hello World', 20, '-', 'hello-world'],
            'exact limit' => ['Hello World', 11, '-', 'hello-world'],
            'cuts at word boundary' => ['The quick brown fox jumps', 15, '-', 'the-quick-brown'],
            'does not cut a word in half' => ['The quick brown fox jumps', 14, '-', 'the-quick'],
            'single long word is truncated' => ['Supercalifragilistic', 5, '-', 'super'],
            'first word longer than limit' => ['Supercalifragilistic word', 5, '-', 'super'],
            'multibyte' => ['مرحبا بالعالم الجميل', 13, '-', 'مرحبا-بالعالم'],
            'multi-char separator' => ['a b c d', 6, '--', 'a--b'],
            'empty separator keeps whole words' => ['Hello World', 7, '', 'hello'],
        ];
    }

    /**
     * @dataProvider maxLengthProvider
     */
    public function testMaxLength(string $input, int $max, string $separator, string $expected): void
    {
        $slug = Slugify::of($input)->separator($separator)->maxLength($max)->toString();

        $this->assertSame($expected, $slug);
        $this->assertLessThanOrEqual($max, mb_strlen($slug));
    }

    public function testMaxLengthCanBeRemoved(): void
    {
        $this->assertSame('hello-world', Slugify::of('Hello World')->maxLength(5)->maxLength(null)->toString());
    }

    public function testInvalidMaxLength(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Slugify::of('Hello')->maxLength(0)->toString();
    }
}
