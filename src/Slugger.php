<?php

namespace Pharaonic\Slugify;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Rules\RuleSet;
use Pharaonic\Slugify\Support\CamelCase;
use Pharaonic\Slugify\Support\Unicode;
use Pharaonic\Slugify\Transliteration\PortableAsciiTransliterator;
use Stringable;

/**
 * Immutable, fluent slug builder.
 *
 * Every configuration method returns a new instance, so a configured
 * builder can be safely reused and never touches global state.
 */
final class Slugger implements Stringable
{
    private SlugOptions $options;

    private RuleSet $rules;

    private Transliterator $transliterator;

    public function __construct(
        private string $value,
        ?SlugOptions $options = null,
        ?RuleSet $rules = null,
        ?Transliterator $transliterator = null
    ) {
        $this->options = $options !== null ? clone $options : new SlugOptions();
        $this->rules = $rules ?? new RuleSet();
        $this->transliterator = $transliterator ?? new PortableAsciiTransliterator();
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
     * Transliterate the slug to ASCII, optionally using a language hint (e.g. "de": "ä" => "ae").
     */
    public function ascii(?string $language = null): self
    {
        return $this->withOption(function (SlugOptions $options) use ($language): void {
            $options->ascii = true;
            $options->language = $language;
        });
    }

    /**
     * Keep Unicode letters as they are (the default).
     */
    public function unicode(): self
    {
        return $this->withOption(function (SlugOptions $options): void {
            $options->ascii = false;
            $options->language = null;
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
        $options = $this->options;
        $options->validate();

        $value = Unicode::normalize($this->value);

        if ($options->splitCamelCase) {
            $value = CamelCase::split($value);
        }

        $value = $this->applyRulesAndTransliteration($value);

        if ($options->lowercase) {
            // Lowercasing the Turkish "İ" yields "i" + U+0307 (combining dot above); keep a plain "i".
            $value = str_replace("i\u{0307}", 'i', mb_strtolower($value, 'UTF-8'));
        }

        $words = $this->words($value, $options->ascii);

        if ($words === []) {
            return '';
        }

        return $this->join($words, $options->separator, $options->maxLength);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __clone()
    {
        $this->options = clone $this->options;
    }

    /**
     * In ASCII mode, rules written in a non-Latin script must run before
     * transliteration, while ASCII rules (e.g. "allh" => "allah") also need
     * to match transliterated text, so they run afterwards.
     */
    private function applyRulesAndTransliteration(string $value): string
    {
        $caseInsensitive = $this->options->lowercase;

        if (!$this->options->ascii) {
            return $this->rules->apply($value, $caseInsensitive);
        }

        [$asciiRules, $nonAsciiRules] = $this->rules->partitionByAscii();

        $value = $nonAsciiRules->apply($value, $caseInsensitive);
        $value = $this->transliterator->transliterate($value, $this->options->language);

        return $asciiRules->apply($value, $caseInsensitive);
    }

    /**
     * Split the text into words, dropping everything that is not a letter,
     * number or (in Unicode mode) a combining mark attached to a word.
     *
     * @return list<string>
     */
    private function words(string $value, bool $ascii): array
    {
        if ($ascii) {
            return preg_split('/[^A-Za-z0-9]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $words = [];

        foreach (preg_split('/[^\pL\pN\pM]+/u', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            // Orphan marks (e.g. the U+FE0F emoji variation selector) are not part of a word.
            $word = (string) preg_replace('/^\pM+/u', '', $word);

            if ($word !== '') {
                $words[] = $word;
            }
        }

        return $words;
    }

    /**
     * @param non-empty-list<string> $words
     */
    private function join(array $words, string $separator, ?int $maxLength): string
    {
        $slug = implode($separator, $words);

        if ($maxLength === null || mb_strlen($slug, 'UTF-8') <= $maxLength) {
            return $slug;
        }

        $first = array_shift($words);

        if (mb_strlen($first, 'UTF-8') >= $maxLength) {
            return mb_substr($first, 0, $maxLength, 'UTF-8');
        }

        $slug = $first;

        foreach ($words as $word) {
            $candidate = $slug . $separator . $word;

            if (mb_strlen($candidate, 'UTF-8') > $maxLength) {
                break;
            }

            $slug = $candidate;
        }

        return $slug;
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
