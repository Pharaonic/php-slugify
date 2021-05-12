<?php

namespace Pharaonic\Slugify\Tests;

use Pharaonic\Slugify\Slugify;
use PHPUnit\Framework\TestCase;

class SlugifyTest extends TestCase
{
    public function testSlug()
    {
        $expected = 'moamen-eltouny';

        $this->assertSame($expected, Slugify::get('Moamen Eltouny'));
    }

    public function testRuleManipulation()
    {
        $expected = 'moeamen-eltoeuny';
        Slugify::rule('ö', 'oe');

        $this->assertSame($expected, Slugify::get('Möamen Eltöuny'));
    }
}
