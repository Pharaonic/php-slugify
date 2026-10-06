<?php

namespace Pharaonic\Slugify\Tests\Unit;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Tests\Fixtures\UppercaseTransliterator;
use Pharaonic\Slugify\Transliteration\LocaleAwareTransliterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocaleAwareTransliteratorTest extends TestCase
{
    public function testImplementsTheContract(): void
    {
        $this->assertInstanceOf(Transliterator::class, new LocaleAwareTransliterator());
    }

    public function testDelegatesToTheGenericTransliterator(): void
    {
        $transliterator = new LocaleAwareTransliterator(new UppercaseTransliterator());

        $this->assertSame('ABC de', $transliterator->transliterate('abc', 'de'));
    }

    public function testNullUsesTheDefaultGenericTransliterator(): void
    {
        $this->assertSame('Aepfel', new LocaleAwareTransliterator(null)->transliterate('Äpfel', 'de'));
    }

    public function testOverridesRunBeforeTheGenericTransliterator(): void
    {
        $transliterator = new LocaleAwareTransliterator(new UppercaseTransliterator());

        $this->assertSame('KYIV uk', $transliterator->transliterate('київ', 'uk'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function ukrainianProvider(): array
    {
        return [
            'word-initial forms' => [
                'Єнакієве Їжакевич Йосипівка Юрій Яготин',
                'Yenakiieve Yizhakevych Yosypivka Yurii Yahotyn',
            ],
            'non-initial forms' => ['Корюківка Мар\'їне Стрий Згорани', 'Koriukivka Marine Stryi Zghorany'],
            'soft sign' => ['Русь Львів', 'Rus Lviv'],
            'upper case word' => ['ЩАСТЯ', 'SHCHASTIA'],
            'upper case word ending in a digraph' => ['КИЇВ ЗАЩ', 'KYIV ZASHCH'],
            'title case digraph' => ['Щастя', 'Shchastia'],
            'non-ukrainian text is untouched' => ['Hello', 'Hello'],
        ];
    }

    #[DataProvider('ukrainianProvider')]
    public function testUkrainian(string $input, string $expected): void
    {
        $this->assertSame($expected, new LocaleAwareTransliterator()->transliterate($input, 'uk'));
    }

    public function testLocalesWithoutOverridesAreGeneric(): void
    {
        $this->assertSame('Aepfel', new LocaleAwareTransliterator()->transliterate('Äpfel', 'de'));
        $this->assertSame('Kiyiv', new LocaleAwareTransliterator()->transliterate('Київ'));
    }
}
