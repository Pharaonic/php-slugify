<?php

namespace Pharaonic\Slugify\Tests\Feature;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Policies\EmojiPolicy;
use Pharaonic\Slugify\Policies\SymbolPolicy;
use Pharaonic\Slugify\Slugify;
use Pharaonic\Slugify\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class EmojiTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function emojiProvider(): array
    {
        return [
            'single' => ['🚀'],
            'variation selector' => ['❤️'],
            'text heart' => ['❤'],
            'thumbs up' => ['👍'],
            'skin tone' => ['👍🏽'],
            'zwj family' => ['👨‍👩‍👧‍👦'],
            'flag' => ['🇪🇬'],
            'two flags' => ['🇪🇬🇺🇸'],
            'single regional indicator' => ['🇪'],
            'subdivision flag (tag sequence)' => ['🏴󠁧󠁢󠁥󠁮󠁧󠁿'],
            'hash keycap' => ['#️⃣'],
            'star keycap' => ['*️⃣'],
            'copyright emoji' => ['©️'],
            'trade mark emoji' => ['™️'],
            'double exclamation emoji' => ['‼️'],
            'rainbow flag' => ['🏳️‍🌈'],
            'heart on fire' => ['❤️‍🔥'],
            'modifier alone' => ['🏽'],
            'replacement character' => ["\u{FFFD}"],
        ];
    }

    #[DataProvider('emojiProvider')]
    public function testEmojiAreRemovedWithoutResidue(string $emoji): void
    {
        $this->assertSame('php-rocks', Slugify::make("PHP {$emoji} Rocks"));
        $this->assertSame('php-rocks', Slugify::make("PHP{$emoji}Rocks"));
        $this->assertSame('', Slugify::make($emoji));
        $this->assertSame('php-rocks', Slugify::make("PHP {$emoji} Rocks", '-', true));
    }

    #[DataProvider('emojiProvider')]
    public function testEmojiNextToUnicodeLettersLeaveNoResidue(string $emoji): void
    {
        $this->assertSame('مرحبا-بالعالم', Slugify::make("مرحبا{$emoji}بالعالم"));
    }

    public function testDigitKeycapIsANumber(): void
    {
        $this->assertSame('top-1-tips', Slugify::make('Top 1️⃣ tips'));
    }

    public function testStrayJoinersAndSelectorsAreRemoved(): void
    {
        $this->assertSame('ab', Slugify::make("a\u{200D}b"));
        $this->assertSame('ab', Slugify::make("a\u{FE0F}b"));
        $this->assertSame('ab', Slugify::make("a\u{20E3}b"));
        $this->assertSame('a-b', Slugify::make("a \u{FE0F} b"));
    }

    public function testCustomWords(): void
    {
        $policy = EmojiPolicy::custom(['🚀' => 'rocket', '👍' => 'thumbs up', '❤' => 'love']);

        $this->assertSame('php-rocket-rocks', Slugify::of('PHP 🚀 Rocks')->emoji($policy)->toString());
        $this->assertSame('php-rocket-rocks', Slugify::of('PHP🚀Rocks')->emoji($policy)->toString());
        $this->assertSame('thumbs-up', Slugify::of('👍🏽')->emoji($policy)->toString(), 'skin tones are variants');
        $this->assertSame('i-love-php', Slugify::of('I ❤️ PHP')->emoji($policy)->toString(), 'VS16 is a variant');
        $this->assertSame('a-b', Slugify::of('a 🎉 b')->emoji($policy)->toString(), 'unlisted emoji are removed');
    }

    public function testCustomWordsForSequences(): void
    {
        $policy = EmojiPolicy::custom(['🇪🇬' => 'egypt', '👨‍👩‍👧‍👦' => 'family']);

        $this->assertSame('egypt-family', Slugify::of('🇪🇬👨‍👩‍👧‍👦')->emoji($policy)->toString());
    }

    public function testCustomWordsInAsciiMode(): void
    {
        $slug = Slugify::of('Привет 🚀')->ascii()->emoji(EmojiPolicy::custom(['🚀' => 'ракета']))->toString();

        $this->assertSame('privet-raketa', $slug);
    }

    public function testCustomRejectsEmptyEmoji(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EmojiPolicy::custom(["\u{FE0F}" => 'nothing']);
    }

    public function testPlainSymbolsAreNotEmoji(): void
    {
        $words = SymbolPolicy::words();

        $this->assertSame('copyright-2026', Slugify::of('© 2026')->symbols($words)->toString());
        $this->assertSame('2026', Slugify::of('©️ 2026')->symbols($words)->toString(), '"©️" follows the emoji policy');
    }
}
