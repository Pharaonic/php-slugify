<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Rules\RuleSet;
use PHPUnit\Framework\TestCase;

final class RuleSetTest extends TestCase
{
    public function testEmptySetLeavesValueUntouched(): void
    {
        $rules = new RuleSet();

        $this->assertTrue($rules->isEmpty());
        $this->assertCount(0, $rules);
        $this->assertSame('Hello', $rules->apply('Hello'));
    }

    public function testApply(): void
    {
        $rules = new RuleSet(['$' => 'dollar', '%' => 'percent']);

        $this->assertSame('5dollar 5percent', $rules->apply('5$ 5%'));
    }

    public function testRegexMetaCharactersAreLiteral(): void
    {
        $rules = new RuleSet(['.*' => 'x', '/' => 'slash', '(a|b)' => 'y']);

        $this->assertSame('ax slash y ab', $rules->apply('a.* / (a|b) ab'));
    }

    public function testCaseInsensitiveMatchingUsesCaseFolding(): void
    {
        $rules = new RuleSet(['Öl' => 'oil', 'straße' => 'street']);

        $this->assertSame('oil oil OIL', $rules->apply('öl ÖL OIL', true));
        $this->assertSame('ÖL', $rules->apply('ÖL', false));
        $this->assertSame('street', $rules->apply("STRA\u{1E9E}E", true));
    }

    public function testImmutability(): void
    {
        $rules = new RuleSet(['a' => 'b']);
        $with = $rules->with('c', 'd');
        $merged = $rules->merge(['e' => 'f']);
        $without = $rules->without('a');

        $this->assertSame(['a' => 'b'], $rules->all());
        $this->assertSame(['a' => 'b', 'c' => 'd'], $with->all());
        $this->assertSame(['a' => 'b', 'e' => 'f'], $merged->all());
        $this->assertSame([], $without->all());
    }

    public function testCompiledPatternIsNotSharedWithDerivedSets(): void
    {
        $rules = new RuleSet(['a' => 'b']);
        $this->assertSame('b', $rules->apply('a'));

        $this->assertSame('a', $rules->without('a')->apply('a'));
        $this->assertSame('z', $rules->with('a', 'z')->apply('a'));
        $this->assertSame('b', $rules->apply('a'));
    }

    public function testMergeAcceptsRuleSets(): void
    {
        $merged = (new RuleSet(['a' => '1']))->merge(new RuleSet(['a' => '2', 'b' => '3']));

        $this->assertSame(['a' => '2', 'b' => '3'], $merged->all());
    }

    public function testOverriddenRuleMovesToTheEnd(): void
    {
        $rules = (new RuleSet(['a' => '1', 'b' => '2']))->with('a', '3');

        $this->assertSame(['b' => '2', 'a' => '3'], $rules->all());
    }

    public function testPartitionByAscii(): void
    {
        $rules = new RuleSet(['$' => 'd', 'ö' => 'oe', 'allh' => 'allah', 'ب' => 'b']);

        [$ascii, $nonAscii] = $rules->partitionByAscii();

        $this->assertSame(['$' => 'd', 'allh' => 'allah'], $ascii->all());
        $this->assertSame(['ö' => 'oe', 'ب' => 'b'], $nonAscii->all());
    }

    public function testSplitForTransliteration(): void
    {
        $rules = new RuleSet([
            '$' => 'd',
            'ö' => 'oe',
            'allh' => 'allah',
            'c++' => 'cpp',
            'e-mail' => 'email',
            '1' => 'one',
            '--' => 'dash',
        ]);

        [$before, $after] = $rules->splitForTransliteration();

        $this->assertSame(['$' => 'd', 'ö' => 'oe', 'c++' => 'cpp', '--' => 'dash'], $before->all());
        $this->assertSame(['allh' => 'allah', 'e-mail' => 'email', '1' => 'one'], $after->all());
        $this->assertSame([$before, $after], $rules->splitForTransliteration(), 'cached');
    }

    public function testHas(): void
    {
        $rules = new RuleSet(['a' => 'b']);

        $this->assertTrue($rules->has('a'));
        $this->assertFalse($rules->has('b'));
    }

    public function testEmptySearchIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RuleSet(['' => 'x']);
    }

    public function testDefaults(): void
    {
        $defaults = RuleSet::defaults();

        $this->assertSame(['@' => ' at '], $defaults->all());
    }
}
