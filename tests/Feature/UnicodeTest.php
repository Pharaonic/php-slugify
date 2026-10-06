<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;

final class UnicodeTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public function unicodeProvider(): array
    {
        return [
            'arabic' => ['مرحبا بالعالم', 'مرحبا-بالعالم'],
            'arabic with tashkeel' => ['مُحَمَّدٌ رَسُولُ', 'محمد-رسول'],
            'arabic with tatweel' => ['مـحـمـد', 'محمد'],
            'arabic-indic digits' => ['الفصل ٣', 'الفصل-٣'],
            'persian' => ['پژوهش گروه ژاپن', 'پژوهش-گروه-ژاپن'],
            'french' => ['Crème Brûlée à la française', 'crème-brûlée-à-la-française'],
            'german' => ['Äpfel und Öl in München', 'äpfel-und-öl-in-münchen'],
            'german sharp s' => ['Straße', 'straße'],
            'greek' => ['Γειά σου Κόσμε', 'γειά-σου-κόσμε'],
            'russian' => ['Привет, мир!', 'привет-мир'],
            'chinese' => ['你好，世界', '你好-世界'],
            'japanese' => ['こんにちは 世界', 'こんにちは-世界'],
            'korean' => ['안녕하세요 세계', '안녕하세요-세계'],
            'hindi (combining vowel signs)' => ['नमस्ते दुनिया', 'नमस्ते-दुनिया'],
            'hebrew' => ['שלום עולם', 'שלום-עולם'],
            'mixed rtl/ltr' => ['Laravel مع PHP', 'laravel-مع-php'],
            'mixed scripts' => ['Hello мир 世界', 'hello-мир-世界'],
            'turkish dotted capital i' => ['İstanbul', 'istanbul'],
        ];
    }

    /**
     * @dataProvider unicodeProvider
     */
    public function testUnicodeIsPreserved(string $input, string $expected): void
    {
        $this->assertSame($expected, Slugify::make($input));
    }

    public function testCombiningMarksStayAttachedToTheirWord(): void
    {
        $decomposed = "Cafe\u{0301} au lait";
        $expected = class_exists(\Normalizer::class) ? 'café-au-lait' : "cafe\u{0301}-au-lait";

        $this->assertSame($expected, Slugify::make($decomposed));
    }

    public function testComposedAndDecomposedInputMatchOnlyWithIntl(): void
    {
        $matches = Slugify::make("Cafe\u{0301}") === Slugify::make('Café');

        $this->assertSame(class_exists(\Normalizer::class), $matches);
    }

    public function testComposedAndDecomposedInputMatchInAsciiMode(): void
    {
        $this->assertSame('cafe', Slugify::make("Cafe\u{0301}", '-', true));
        $this->assertSame('cafe', Slugify::make('Café', '-', true));
    }
}
