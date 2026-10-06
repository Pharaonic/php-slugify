<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Rules\RuleSet;
use Pharaonic\Slugify\SlugOptions;
use Pharaonic\Slugify\Slugger;
use PHPUnit\Framework\TestCase;

final class SluggerTest extends TestCase
{
    public function testStandaloneUsageHasNoRules(): void
    {
        $this->assertSame('user-host', new Slugger('user@host')->toString());
    }

    public function testExplicitRuleSet(): void
    {
        $this->assertSame('user-at-host', (new Slugger('user@host', null, RuleSet::defaults()))->toString());
    }

    public function testExplicitOptions(): void
    {
        $this->assertSame('Hello.World', (new Slugger('Hello World', new SlugOptions('.', false)))->toString());
    }

    public function testOptionsAreExposedAsACopy(): void
    {
        $slugger = new Slugger('x')
            ->separator('_')
            ->ascii('de')
            ->maxLength(5)
            ->splitCamelCase(false)
            ->lowercase(false);
        $options = $slugger->options();

        $this->assertSame('_', $options->separator);
        $this->assertTrue($options->ascii);
        $this->assertSame('de', $options->language);
        $this->assertSame(5, $options->maxLength);
        $this->assertFalse($options->splitCamelCase);
        $this->assertFalse($options->lowercase);
    }
}
