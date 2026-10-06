<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Policies\EmojiPolicy;
use Pharaonic\Slugify\Policies\SymbolPolicy;
use Pharaonic\Slugify\SlugOptions;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;

final class PipelineTest extends TestCase
{
    public function testExplainReturnsEveryStageInOrder(): void
    {
        $steps = Slugify::of('Äpfel & Straße 🚀')->locale('de')->ascii()->explain();

        $this->assertSame([
            'original',
            'unicode_normalized',
            'camel_case_split',
            'custom_replacements',
            'numbers_normalized',
            'emoji_processed',
            'symbols_processed',
            'lowercased',
            'transliterated',
            'ascii_replacements',
            'filtered',
            'final',
        ], array_keys($steps));

        $this->assertSame('Äpfel & Straße 🚀', $steps['original']);
        $this->assertSame('Äpfel & Straße  ', $steps['emoji_processed']);
        $this->assertSame('äpfel & straße  ', $steps['lowercased']);
        $this->assertSame('aepfel & strasse  ', $steps['transliterated']);
        $this->assertSame('aepfel strasse', $steps['filtered']);
        $this->assertSame('aepfel-strasse', $steps['final']);
    }

    public function testExplainEndsWithTheSlug(): void
    {
        $slug = Slugify::of('PHP 🚀 & ١٢ Rocks')
            ->symbols(SymbolPolicy::words())
            ->emoji(EmojiPolicy::custom(['🚀' => 'rocket']));

        $this->assertSame($slug->toString(), $slug->explain()['final']);
        $this->assertSame('php-rocket-and-12-rocks', $slug->toString());
    }

    public function testExplainValidatesOptions(): void
    {
        $this->expectException(\Pharaonic\Slugify\Exceptions\InvalidArgumentException::class);

        Slugify::of('x')->separator('a')->explain();
    }

    public function testUserRulesTakePrecedenceOverLocaleOverrides(): void
    {
        $this->assertSame('kyiv', Slugify::of('Київ')->locale('uk')->ascii()->toString());
        $this->assertSame('kiev', Slugify::of('Київ')->locale('uk')->ascii()->rule('київ', 'kiev')->toString());
    }

    public function testLocaleOverridesTakePrecedenceOverTheGenericTransliterator(): void
    {
        $this->assertSame('zhuk', Slugify::of('Жук')->locale('uk')->ascii()->toString());
        $this->assertSame('zuk', Slugify::of('Жук')->ascii()->transliterator(
            new \Pharaonic\Slugify\Transliteration\PortableAsciiTransliterator()
        )->ascii('uk')->toString());
    }

    public function testRulesSeeTheOriginalText(): void
    {
        // Rules run before number, emoji and symbol handling.
        $this->assertSame('rocket', Slugify::of('🚀')->rule('🚀', 'rocket')->toString());
        $this->assertSame('three', Slugify::of('٣')->rule('٣', 'three')->toString());
        $this->assertSame('a-plus-b', Slugify::of('a+b')->rule('+', ' plus ')->toString());
    }

    public function testDeterministicForCanonicallyEquivalentInput(): void
    {
        $forms = ['Café', "Cafe\u{0301}", 'Ｃａｆé', '𝐂𝐚𝐟é'];

        foreach ([false, true] as $ascii) {
            $slugs = array_unique(array_map(static function (string $form) use ($ascii): string {
                return Slugify::make($form, '-', $ascii);
            }, $forms));

            $this->assertCount(1, $slugs, implode(', ', $slugs));
        }
    }

    public function testOptionsDefaults(): void
    {
        $options = new SlugOptions();

        $this->assertTrue($options->normalizeNumbers);
        $this->assertEquals(SymbolPolicy::remove(), $options->symbols);
        $this->assertEquals(EmojiPolicy::remove(), $options->emoji);
    }

    public function testPoliciesAreExposedThroughOptions(): void
    {
        $words = SymbolPolicy::words();
        $emoji = EmojiPolicy::custom(['🚀' => 'rocket']);
        $options = Slugify::of('x')->symbols($words)->emoji($emoji)->normalizeNumbers(false)->options();

        $this->assertSame($words, $options->symbols);
        $this->assertSame($emoji, $options->emoji);
        $this->assertFalse($options->normalizeNumbers);
    }

    /**
     * @return array<string, array{string, int, string}>
     */
    public function graphemeProvider(): array
    {
        return [
            'devanagari conjunct is not split' => ['नमस्ते', 4, 'नमस्'],
            'devanagari vowel sign is kept' => ['नमस्ते', 5, 'नमस्'],
            'whole word fits' => ['नमस्ते', 6, 'नमस्ते'],
            'thai stacked marks' => ['ที่นี่', 4, 'ที่'],
            'conjoining jamo are composed first' => ["\u{1100}\u{1161}\u{11A8}\u{1100}\u{1161}", 2, '각가'],
        ];
    }

    /**
     * @dataProvider graphemeProvider
     */
    public function testMaxLengthNeverSplitsAGrapheme(string $input, int $max, string $expected): void
    {
        $slug = Slugify::of($input)->maxLength($max)->toString();

        $this->assertSame($expected, $slug);
        $this->assertLessThanOrEqual($max, mb_strlen($slug));
    }

    public function testMaxLengthNeverLeavesATrailingSeparator(): void
    {
        for ($max = 1; $max <= 30; $max++) {
            $slug = Slugify::of('The quick brown fox — jumps')->separator('--')->maxLength($max)->toString();

            $this->assertStringEndsNotWith('-', $slug);
            $this->assertLessThanOrEqual($max, mb_strlen($slug));
        }
    }
}
