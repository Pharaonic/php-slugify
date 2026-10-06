<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Facades\Slugify as LegacyFacade;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use stdClass;
use Stringable;

/**
 * Covers the 2.x public API: slug(), Slugify::get(), Slugify::rule() and the Facades namespace.
 */
final class BackwardCompatibilityTest extends TestCase
{
    public function testLegacyRuleManipulationScenario(): void
    {
        LegacyFacade::rule('allh', 'allah');
        $this->assertSame('bsm_allah', slug('بسم الله', '_', true));

        LegacyFacade::rule('ö', 'oe');
        $this->assertSame('moeamen-eltoeuny', LegacyFacade::get('Möamen Eltöuny'));

        LegacyFacade::rule('$', '-dollar');
        $this->assertSame('500-dollar-bill', slug('500$ bill', '-'));
        $this->assertSame('500-dollar-bill', slug('500 $ bill', '-'));
    }

    public function testFacadeSharesStateWithTheEntryPoint(): void
    {
        LegacyFacade::rule('ö', 'oe');

        $this->assertSame('oe', Slugify::make('ö'));
        $this->assertArrayHasKey('ö', Slugify::rules()->all());
    }

    public function testGetIsAnAliasOfMake(): void
    {
        $this->assertSame(Slugify::make('Hello World', '_', true, 'de'), Slugify::get('Hello World', '_', true, 'de'));
    }

    public function testGetDefaultsToEnglishTransliteration(): void
    {
        $this->assertSame('munchen', Slugify::get('München', '-', true));
    }

    public function testRuleIsAnAliasOfAddRule(): void
    {
        Slugify::rule('&', 'and');

        $this->assertSame(['&' => 'and'], array_intersect_key(Slugify::rules()->all(), ['&' => true]));
    }

    public function testLegacyMixedValues(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'Hello World';
            }
        };

        $this->assertSame('', Slugify::get(null));
        $this->assertSame('', slug(false));
        $this->assertSame('1', slug(true));
        $this->assertSame('0', slug(0));
        $this->assertSame('123', slug(123));
        $this->assertSame('1-5', slug(1.5));
        $this->assertSame('hello-world', slug($stringable));
    }

    public function testLegacyNullLanguageIsAccepted(): void
    {
        $this->assertSame('munchen', slug('München', '-', true, null));
    }

    public function testUnsupportedValuesAreRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('stdClass');

        slug(new stdClass());
    }

    public function testRulesAreCaseInsensitiveLikeBefore(): void
    {
        Slugify::rule('ÖL', 'oil');

        $this->assertSame('oil', slug('öl'));
        $this->assertSame('oil', slug('Öl'));
    }
}
