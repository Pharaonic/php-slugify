<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;

final class RulesTest extends TestCase
{
    public function testAddRule(): void
    {
        Slugify::addRule('ö', 'oe');

        $this->assertSame('moeamen', Slugify::make('Möamen'));
    }

    public function testAddRules(): void
    {
        Slugify::addRules(['ö' => 'oe', 'ü' => 'ue', 'ä' => 'ae']);

        $this->assertSame('aepfel-uend-oel', Slugify::make('Äpfel Ünd Öl'));
    }

    public function testGlobalRulesApplyInAsciiModeBeforeTransliteration(): void
    {
        Slugify::addRule('ö', 'oe');

        $this->assertSame('oel', Slugify::make('Öl', '-', true));
    }

    public function testAsciiRulesMatchTransliteratedText(): void
    {
        Slugify::addRule('allh', 'allah');

        $this->assertSame('bsm-allah', Slugify::make('بسم الله', '-', true));
    }

    public function testRulesDoNotCascade(): void
    {
        Slugify::addRules(['a' => 'b', 'b' => 'c']);

        $this->assertSame('bc', Slugify::make('ab'));
    }

    public function testLongestRuleWins(): void
    {
        Slugify::addRules(['c' => 'x', 'c++' => 'cpp', 'c#' => 'csharp']);

        $this->assertSame('cpp-csharp-x', Slugify::make('C++ C# C'));
    }

    public function testLaterRuleOverridesEarlierOne(): void
    {
        Slugify::addRule('&', 'and');
        Slugify::addRule('&', 'und');

        $this->assertSame('a-und-b', Slugify::make('A & B'));
    }

    public function testReplacementIsLowercased(): void
    {
        Slugify::addRule('$', ' Dollar ');

        $this->assertSame('500-dollar-bill', Slugify::make('500$ Bill'));
    }

    public function testRulesAreCaseSensitiveWithoutLowercasing(): void
    {
        Slugify::addRule('php', 'PHP');

        $this->assertSame('PHP-and-PHP', Slugify::of('php and PHP')->lowercase(false)->toString());
    }

    public function testDefaultAtRuleCanBeRemoved(): void
    {
        $this->assertSame('user-at-host', Slugify::make('user@host'));

        Slugify::removeRule('@');

        $this->assertSame('user-host', Slugify::make('user@host'));
    }

    public function testDefaultAtRuleCanBeOverridden(): void
    {
        Slugify::addRule('@', ' chez ');

        $this->assertSame('user-chez-host', Slugify::make('user@host'));
    }

    public function testResetRulesRestoresDefaults(): void
    {
        Slugify::addRule('ö', 'oe');
        Slugify::removeRule('@');

        Slugify::resetRules();

        $this->assertSame('ö-at-x', Slugify::make('ö@x'));
    }

    public function testLocalRulesDoNotLeakIntoGlobalState(): void
    {
        $this->assertSame('500-dollar-bill', Slugify::of('500$ Bill')->rule('$', ' dollar ')->toString());
        $this->assertSame('500-bill', Slugify::make('500$ Bill'));
        $this->assertFalse(Slugify::rules()->has('$'));
    }

    public function testLocalRulesOverrideGlobalRules(): void
    {
        Slugify::addRule('&', 'and');

        $this->assertSame('a-et-b', Slugify::of('A & B')->rule('&', 'et')->toString());
        $this->assertSame('a-and-b', Slugify::make('A & B'));
    }

    public function testMultipleLocalRules(): void
    {
        $slug = Slugify::of('C++ & C#')->rules(['c++' => 'cpp', '&' => 'and', 'c#' => 'csharp'])->toString();

        $this->assertSame('cpp-and-csharp', $slug);
    }

    public function testGlobalRulesAreSnapshottedWhenTheBuilderIsCreated(): void
    {
        $builder = Slugify::of('A & B');

        Slugify::addRule('&', 'and');

        $this->assertSame('a-b', $builder->toString());
        $this->assertSame('a-and-b', Slugify::of('A & B')->toString());
    }

    public function testWithoutRulesIgnoresDefaultsAndGlobals(): void
    {
        Slugify::addRule('&', 'and');

        $this->assertSame('user-host-a-b', Slugify::of('user@host A & B')->withoutRules()->toString());
        $this->assertSame('a-x-b', Slugify::of('A & B')->withoutRules()->rule('&', 'x')->toString());
    }

    public function testReplacementsAreLiteral(): void
    {
        $this->assertSame('500dollar-bill', Slugify::of('500$ Bill')->rule('$', 'dollar')->toString());
        $this->assertSame('500-dollar-bill', Slugify::of('500$ Bill')->rule('$', ' dollar ')->toString());
    }

    public function testEmptyRuleIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Slugify::addRule('', 'x');
    }

    public function testNumericRuleKeys(): void
    {
        Slugify::addRules(['1' => 'one']);

        $this->assertSame('one-two', Slugify::make('1 two'));
    }
}
