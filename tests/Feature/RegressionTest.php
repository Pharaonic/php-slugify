<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class RegressionTest extends TestCase
{
    /**
     * @return array<int, array{string, string, bool, string, string}>
     */
    public static function fixtures(): array
    {
        /** @var array<int, array{string, string, bool, string, string}> $fixtures */
        $fixtures = require __DIR__ . '/../Fixtures/regression.php';

        return $fixtures;
    }

    #[DataProvider('fixtures')]
    public function testHelper(string $input, string $separator, bool $ascii, string $language, string $expected): void
    {
        $this->assertSame($expected, slug($input, $separator, $ascii, $language));
    }

    #[DataProvider('fixtures')]
    public function testMake(string $input, string $separator, bool $ascii, string $language, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input, $separator, $ascii, $language));
    }
}
