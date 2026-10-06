<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;

final class LocaleTest extends TestCase
{
    public function testLocaleDoesNotImplyAscii(): void
    {
        $this->assertSame('äpfel-straße', Slugify::of('Äpfel Straße')->locale('de')->toString());
        $this->assertFalse(Slugify::of('x')->locale('de')->options()->ascii);
    }

    public function testLocaleAndAsciiCombine(): void
    {
        $this->assertSame('aepfel-strasse', Slugify::of('Äpfel Straße')->locale('de')->ascii()->toString());
        $this->assertSame('aepfel-strasse', Slugify::of('Äpfel Straße')->ascii()->locale('de')->toString());
    }

    public function testAsciiWithLanguageIsAShorthandForLocale(): void
    {
        $this->assertSame('de', Slugify::of('x')->ascii('de')->options()->language);
        $this->assertSame(
            Slugify::of('Äpfel')->locale('de')->ascii()->toString(),
            Slugify::of('Äpfel')->ascii('de')->toString()
        );
    }

    public function testAsciiWithoutLanguageKeepsTheLocale(): void
    {
        $this->assertSame('de', Slugify::of('x')->locale('de')->ascii()->options()->language);
    }

    public function testUnicodeKeepsTheLocale(): void
    {
        $slug = Slugify::of('IŞIK')->locale('tr')->ascii()->unicode();

        $this->assertSame('tr', $slug->options()->language);
        $this->assertSame('ışık', $slug->toString());
    }

    public function testLocaleCanBeRemoved(): void
    {
        $this->assertSame('apfel', Slugify::of('Äpfel')->locale('de')->locale(null)->ascii()->toString());
    }

    public function testTurkishLowercasing(): void
    {
        $this->assertSame('ışık-istanbul', Slugify::of('IŞIK İstanbul')->locale('tr')->toString());
        $this->assertSame('işik-istanbul', Slugify::make('IŞIK İstanbul'), 'without a locale "I" is "i"');
        $this->assertSame('ışık', Slugify::make('IŞIK', '-', false, 'tr-TR'));
    }

    public function testTurkishLowercasingOnlyAppliesWhenLowercasing(): void
    {
        $this->assertSame('IŞIK', Slugify::of('IŞIK')->locale('tr')->lowercase(false)->toString());
    }

    public function testUkrainianKeepsCaseWithoutLowercasing(): void
    {
        $slug = Slugify::of('Київ ЩУКА Щука')->locale('uk')->ascii()->lowercase(false)->toString();

        $this->assertSame('Kyiv-SHCHUKA-Shchuka', $slug);
    }

    public function testUkrainianOverridesOnlyApplyToTheUkrainianLocale(): void
    {
        $this->assertSame('kiyiv', Slugify::of('Київ')->ascii()->toString());
        $this->assertSame('kyiv', Slugify::of('Київ')->ascii('uk-UA')->toString());
    }

    /**
     * @return array<string, array{string}>
     */
    public function unknownLocaleProvider(): array
    {
        return [
            'unknown language' => ['xx'],
            'unknown region' => ['xx-YY'],
            'path traversal' => ['../../../etc/passwd'],
            'null byte' => ["uk\0"],
            'empty' => [''],
        ];
    }

    /**
     * @dataProvider unknownLocaleProvider
     */
    public function testUnknownOrMalformedLocalesFallBackToGenericBehavior(string $locale): void
    {
        $this->assertSame('crème-işik', Slugify::of('Crème IŞIK')->locale($locale)->toString());
        $this->assertSame('creme-isik', Slugify::of('Crème IŞIK')->locale($locale)->ascii()->toString());
    }
}
