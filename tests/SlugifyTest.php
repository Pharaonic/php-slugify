<?php

namespace Pharaonic\Slugify\Tests;

use Pharaonic\Slugify\Facades\Slugify;
use PHPUnit\Framework\TestCase;

class SlugifyTest extends TestCase
{
    public function testSlug()
    {
        $this->assertSame('hello-world', slug('hello world'));
        $this->assertSame('hello-world', slug('hello-world'));
        $this->assertSame('hello-world', slug('hello_world'));
        $this->assertSame('hello_world', slug('hello_world', '_'));
        $this->assertSame('user-at-host', slug('user@host'));
        $this->assertSame('سلام-دنیا', slug('سلام دنیا', '-'));
        $this->assertSame('sometext', slug('some text', ''));
        $this->assertSame('', slug('', ''));
        $this->assertSame('', slug(''));

        $this->assertSame('there-is-faq-module-here', slug('There is FAQ module here'));
        $this->assertSame('method-name-in-camel-case', slug('methodNameInCamelCase'));
        $this->assertSame('class_name_in_pascal_case', slug('ClassNameIn-PascalCase', '_'));
    }

    public function testRuleManipulation()
    {
        Slugify::rule('allh', 'allah');
        $this->assertSame('bsm_allah', slug('بسم الله', '_', true));

        Slugify::rule('ö', 'oe');
        $this->assertSame('moeamen-eltoeuny', Slugify::get('Möamen Eltöuny'));

        Slugify::rule('$', '-dollar');
        $this->assertSame('500-dollar-bill', slug('500$ bill', '-'));
        $this->assertSame('500-dollar-bill', slug('500 $ bill', '-'));
    }
}
