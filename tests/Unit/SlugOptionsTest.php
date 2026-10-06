<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\SlugOptions;
use PHPUnit\Framework\TestCase;

final class SlugOptionsTest extends TestCase
{
    public function testDefaults(): void
    {
        $options = new SlugOptions();

        $this->assertSame('-', $options->separator);
        $this->assertTrue($options->lowercase);
        $this->assertFalse($options->ascii);
        $this->assertNull($options->language);
        $this->assertTrue($options->splitCamelCase);
        $this->assertNull($options->maxLength);
    }

    public function testNamedArguments(): void
    {
        $options = new SlugOptions(separator: '_', ascii: true, maxLength: 10);

        $this->assertSame('_', $options->separator);
        $this->assertTrue($options->ascii);
        $this->assertSame(10, $options->maxLength);
    }

    /**
     * @return array<string, array{string}>
     */
    public function validSeparatorProvider(): array
    {
        return [
            'dash' => ['-'],
            'underscore' => ['_'],
            'dot' => ['.'],
            'tilde' => ['~'],
            'empty' => [''],
            'double' => ['--'],
        ];
    }

    /**
     * @dataProvider validSeparatorProvider
     */
    public function testValidSeparators(string $separator): void
    {
        new SlugOptions($separator)->validate();

        $this->addToAssertionCount(1);
    }

    /**
     * @return array<string, array{string}>
     */
    public function invalidSeparatorProvider(): array
    {
        return [
            'letter' => ['a'],
            'digit' => ['0'],
            'space' => [' '],
            'tab' => ["\t"],
            'mark' => ["\u{0301}"],
            'invalid utf-8' => ["\xFF"],
        ];
    }

    /**
     * @dataProvider invalidSeparatorProvider
     */
    public function testInvalidSeparators(string $separator): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SlugOptions($separator)->validate();
    }

    public function testInvalidMaxLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[-1]');

        new SlugOptions(maxLength: -1)->validate();
    }
}
