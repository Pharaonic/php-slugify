<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Support\CamelCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CamelCaseTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function splitProvider(): array
    {
        return [
            'lowercase' => ['hello', 'hello'],
            'camelCase' => ['helloWorld', 'hello World'],
            'PascalCase' => ['HelloWorld', 'Hello World'],
            'XMLHttpRequest' => ['XMLHttpRequest', 'XML Http Request'],
            'APIResponse' => ['APIResponse', 'API Response'],
            'getUserID' => ['getUserID', 'get User ID'],
            'PharaonicPHP' => ['PharaonicPHP', 'Pharaonic PHP'],
            'all caps' => ['FAQ', 'FAQ'],
            'digit before word' => ['Version2Beta', 'Version2 Beta'],
            'digit before acronym' => ['3D', '3D'],
            'unicode' => ['ÉtéÀParis', 'Été À Paris'],
            'cyrillic' => ['приветМир', 'привет Мир'],
            'caseless script' => ['مرحبا', 'مرحبا'],
        ];
    }

    #[DataProvider('splitProvider')]
    public function testSplit(string $input, string $expected): void
    {
        $this->assertSame($expected, CamelCase::split($input));
    }
}
