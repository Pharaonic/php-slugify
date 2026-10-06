<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Support\Unicode;
use PHPUnit\Framework\TestCase;

final class UnicodeTest extends TestCase
{
    public function testValidTextIsUntouched(): void
    {
        $this->assertSame('Café مرحبا 你好', Unicode::normalize('Café مرحبا 你好'));
    }

    public function testInvalidUtf8IsSanitized(): void
    {
        $normalized = Unicode::normalize("abc\xFFdef");

        $this->assertTrue(mb_check_encoding($normalized, 'UTF-8'));
        $this->assertStringStartsWith('abc', $normalized);
        $this->assertStringEndsWith('def', $normalized);
    }

    public function testDecomposedTextIsComposedWhenIntlIsAvailable(): void
    {
        $expected = class_exists(\Normalizer::class) ? 'Café' : "Cafe\u{0301}";

        $this->assertSame($expected, Unicode::normalize("Cafe\u{0301}"));
    }
}
