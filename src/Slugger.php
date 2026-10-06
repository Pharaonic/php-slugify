<?php

namespace Pharaonic\Slugify;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Normalization\CaseNormalizer;
use Pharaonic\Slugify\Normalization\NumberNormalizer;
use Pharaonic\Slugify\Normalization\SeparatorNormalizer;
use Pharaonic\Slugify\Normalization\UnicodeNormalizer;
use Pharaonic\Slugify\Policies\EmojiPolicy;
use Pharaonic\Slugify\Policies\SymbolPolicy;
use Pharaonic\Slugify\Rules\RuleSet;
use Pharaonic\Slugify\Support\CamelCase;
use Pharaonic\Slugify\Transliteration\LocaleAwareTransliterator;
use Stringable;

/**
 * Immutable, fluent slug builder.
 *
 * Every configuration method returns a new instance, so a configured
 * builder can be safely reused and never touches global state.
 *
 * A slug is produced by a fixed, deterministic pipeline (Stage), which
 * explain() exposes step by step:
 *
 *   unicode_normalized    invalid UTF-8 / controls => boundaries, NFC, compatibility forms
 *   camel_case_split      "helloWorld" => "hello World"
 *   custom_replacements   rules (in ASCII mode, ASCII-word rules wait for transliteration)
 *   numbers_normalized    "١٢" / "۱۲" / "¹²" / "①②" => "12"
 *   emoji_processed       emoji policy, on whole grapheme clusters
 *   symbols_processed     symbol policy
 *   lowercased            locale-aware lowercasing
 *   transliterated        ASCII mode only: locale overrides, then the generic transliterator
 *   ascii_replacements    ASCII mode only: the rules held back for transliterated text
 *   filtered              invisible characters dropped, words separated by single spaces
 *   final                 words joined by the separator, within the max length
 */
final class Slugger implements Stringable
{
    private static ?RuleSet $noRules = null;

    private SlugOptions $options;

    private RuleSet $rules;

    private Transliterator $transliterator;

    public function __construct(
        private readonly string $value,
        ?SlugOptions $options = null,
        ?RuleSet $rules = null,
        ?Transliterator $transliterator = null
    ) {
        $this->options = $options !== null ? clone $options : new SlugOptions();
        $this->rules = $rules ?? new RuleSet();
        $this->transliterator = $transliterator ?? new LocaleAwareTransliterator();
    }

    public function separator(string $separator): self
    {
        return $this->withOption(function (SlugOptions $options) use ($separator): void {
            $options->separator = $separator;
        });
    }

    public function lowercase(bool $lowercase = true): self
    {
        return $this->withOption(function (SlugOptions $options) use ($lowercase): void {
            $options->lowercase = $lowercase;
        });
    }

    /**
     * Transliterate the slug to ASCII.
     *
     * The optional locale is a shorthand for locale($language)->ascii(): "de" => "ä" => "ae".
     */
    public function ascii(?string $language = null): self
    {
        return $this->withOption(function (SlugOptions $options) use ($language): void {
            $options->ascii = true;
            $options->language = $language ?? $options->language;
        });
    }

    /**
     * Keep Unicode letters as they are (the default). The locale is kept.
     */
    public function unicode(): self
    {
        return $this->withOption(function (SlugOptions $options): void {
            $options->ascii = false;
        });
    }

    /**
     * Use language-specific behavior (e.g. "tr": "I" => "ı"; with ascii(), "de": "ä" => "ae").
     *
     * A locale never turns ASCII output on by itself; null removes it.
     */
    public function locale(?string $locale): self
    {
        return $this->withOption(function (SlugOptions $options) use ($locale): void {
            $options->language = $locale;
        });
    }

    public function symbols(SymbolPolicy $policy): self
    {
        return $this->withOption(function (SlugOptions $options) use ($policy): void {
            $options->symbols = $policy;
        });
    }

    public function emoji(EmojiPolicy $policy): self
    {
        return $this->withOption(function (SlugOptions $options) use ($policy): void {
            $options->emoji = $policy;
        });
    }

    /**
     * Convert Unicode digits and numerals ("١٢", "۱۲", "²", "①") to ASCII digits (the default).
     */
    public function normalizeNumbers(bool $normalize = true): self
    {
        return $this->withOption(function (SlugOptions $options) use ($normalize): void {
            $options->normalizeNumbers = $normalize;
        });
    }

    public function splitCamelCase(bool $split = true): self
    {
        return $this->withOption(function (SlugOptions $options) use ($split): void {
            $options->splitCamelCase = $split;
        });
    }

    /**
     * Limit the slug length (in characters), cutting at a word boundary when possible.
     */
    public function maxLength(?int $maxLength): self
    {
        return $this->withOption(function (SlugOptions $options) use ($maxLength): void {
            $options->maxLength = $maxLength;
        });
    }

    /**
     * Add a replacement rule for this slug only.
     */
    public function rule(string $search, string $replace): self
    {
        $clone = clone $this;
        $clone->rules = $this->rules->with($search, $replace);

        return $clone;
    }

    /**
     * Add replacement rules for this slug only.
     *
     * @param array<array-key, string> $rules
     */
    public function rules(array $rules): self
    {
        $clone = clone $this;
        $clone->rules = $this->rules->merge($rules);

        return $clone;
    }

    /**
     * Drop every rule inherited so far (package defaults and global rules).
     */
    public function withoutRules(): self
    {
        $clone = clone $this;
        $clone->rules = new RuleSet();

        return $clone;
    }

    public function transliterator(Transliterator $transliterator): self
    {
        $clone = clone $this;
        $clone->transliterator = $transliterator;

        return $clone;
    }

    public function options(): SlugOptions
    {
        return clone $this->options;
    }

    public function toString(): string
    {
        $this->options->validate();

        $value = $this->value;

        foreach (Stage::cases() as $stage) {
            $value = $this->stage($stage, $value);
        }

        return $value;
    }

    /**
     * Run the pipeline and return the text after every stage, keyed by stage name.
     *
     * Meant for debugging; toString() runs the very same stages without recording them.
     *
     * @return array<string, string> "original", one entry per stage, ending with "final"
     */
    public function explain(): array
    {
        $this->options->validate();

        $value = $this->value;
        $steps = ['original' => $value];

        foreach (Stage::cases() as $stage) {
            $steps[$stage->value] = $value = $this->stage($stage, $value);
        }

        return $steps;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->toString();
    }

    public function __clone()
    {
        $this->options = clone $this->options;
    }

    /**
     * Run one pipeline stage. Every stage is a pure string => string step.
     */
    private function stage(Stage $stage, string $value): string
    {
        $options = $this->options;

        return match ($stage) {
            Stage::UnicodeNormalized => UnicodeNormalizer::normalize($value),
            Stage::CamelCaseSplit => $options->splitCamelCase ? CamelCase::split($value) : $value,
            Stage::CustomReplacements => $this->ruleStages()[0]->apply($value, $options->lowercase),
            Stage::NumbersNormalized => $options->normalizeNumbers ? NumberNormalizer::normalize($value) : $value,
            Stage::EmojiProcessed => $options->emoji->apply($value),
            Stage::SymbolsProcessed => $options->symbols->apply($value, $options->language),
            Stage::Lowercased => $this->toLowerCase($value),
            Stage::Transliterated => $options->ascii
                ? $this->toLowerCase($this->transliterator->transliterate($value, $options->language))
                : $value,
            Stage::AsciiReplacements => $this->ruleStages()[1]->apply($value, $options->lowercase),
            Stage::Filtered => implode(' ', SeparatorNormalizer::words($value, $options->ascii)),
            Stage::Final => SeparatorNormalizer::join(
                $value === '' ? [] : explode(' ', $value),
                $options->separator,
                $options->maxLength
            ),
        };
    }

    private function toLowerCase(string $value): string
    {
        return $this->options->lowercase ? CaseNormalizer::lower($value, $this->options->language) : $value;
    }

    /**
     * The rules applied before the policies and, in ASCII mode, those held back
     * until after transliteration so they match transliterated text (e.g. "allh").
     *
     * @return array{0: RuleSet, 1: RuleSet}
     */
    private function ruleStages(): array
    {
        return $this->options->ascii
            ? $this->rules->splitForTransliteration()
            : [$this->rules, self::$noRules ??= new RuleSet()];
    }

    /**
     * @param callable(SlugOptions): void $change
     */
    private function withOption(callable $change): self
    {
        $clone = clone $this;
        $change($clone->options);

        return $clone;
    }
}
