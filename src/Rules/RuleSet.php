<?php

namespace Pharaonic\Slugify\Rules;

use Countable;
use Pharaonic\Slugify\Exceptions\InvalidArgumentException;

/**
 * Immutable, ordered collection of "search => replacement" rules.
 *
 * Rules are applied in a single pass, preferring the longest search string,
 * so the output of one rule is never re-processed by another rule.
 */
final class RuleSet implements Countable
{
    /**
     * @var array<string, string>
     */
    private array $rules = [];

    /**
     * Compiled patterns, keyed by case sensitivity ("i" / "s").
     *
     * @var array<string, array{pattern: string, map: array<string, string>}>
     */
    private array $compiled = [];

    /**
     * @var array{0: self, 1: self}|null
     */
    private ?array $partition = null;

    /**
     * @var array{0: self, 1: self}|null
     */
    private ?array $transliterationSplit = null;

    /**
     * @param array<array-key, string> $rules
     */
    public function __construct(array $rules = [])
    {
        foreach ($rules as $search => $replace) {
            $this->set((string) $search, $replace);
        }
    }

    /**
     * The package default rules: only the historical "@" => "at" replacement.
     *
     * Symbols in general are handled by SymbolPolicy, and script-level marks
     * (Arabic tashkeel, Hebrew points) by the Unicode normalizer.
     */
    public static function defaults(): self
    {
        return new self(['@' => ' at ']);
    }

    public function with(string $search, string $replace): self
    {
        $clone = clone $this;
        $clone->set($search, $replace);

        return $clone;
    }

    /**
     * Merge more rules into a new set; later rules override earlier ones.
     *
     * @param self|array<array-key, string> $rules
     */
    public function merge(self|array $rules): self
    {
        $clone = clone $this;

        foreach ($rules instanceof self ? $rules->rules : $rules as $search => $replace) {
            $clone->set((string) $search, $replace);
        }

        return $clone;
    }

    public function without(string $search): self
    {
        $clone = clone $this;
        unset($clone->rules[$search]);

        return $clone;
    }

    public function has(string $search): bool
    {
        return array_key_exists($search, $this->rules);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->rules;
    }

    public function isEmpty(): bool
    {
        return $this->rules === [];
    }

    #[\Override]
    public function count(): int
    {
        return count($this->rules);
    }

    /**
     * Split into rules whose search string is pure ASCII and those that are not.
     *
     * @return array{0: self, 1: self} [ascii, nonAscii]
     */
    public function partitionByAscii(): array
    {
        if ($this->partition !== null) {
            return $this->partition;
        }

        $ascii = [];
        $nonAscii = [];

        foreach ($this->rules as $search => $replace) {
            if (preg_match('/[^\x00-\x7F]/', (string) $search) === 1) {
                $nonAscii[$search] = $replace;
            } else {
                $ascii[$search] = $replace;
            }
        }

        return $this->partition = [new self($ascii), new self($nonAscii)];
    }

    /**
     * Split for ASCII mode into rules applied before and after transliteration.
     *
     * Rules made only of ASCII letters, digits, spaces, "_", "." and "-" (e.g. "allh")
     * run after transliteration, so they also match transliterated text. Every
     * other rule ("ö", "$", "c++") runs first, before any policy or transliteration
     * can change the text it targets.
     *
     * @return array{0: self, 1: self} [before, after]
     */
    public function splitForTransliteration(): array
    {
        if ($this->transliterationSplit !== null) {
            return $this->transliterationSplit;
        }

        $before = [];
        $after = [];

        foreach ($this->rules as $search => $replace) {
            if (preg_match('/^[A-Za-z0-9 _.\-]*[A-Za-z0-9][A-Za-z0-9 _.\-]*$/', (string) $search) === 1) {
                $after[$search] = $replace;
            } else {
                $before[$search] = $replace;
            }
        }

        return $this->transliterationSplit = [new self($before), new self($after)];
    }

    /**
     * Apply every rule to the given UTF-8 text.
     */
    public function apply(string $value, bool $caseInsensitive = false): string
    {
        if ($this->rules === [] || $value === '') {
            return $value;
        }

        $compiled = $this->compile($caseInsensitive);
        $map = $compiled['map'];

        $result = preg_replace_callback(
            $compiled['pattern'],
            static function (array $match) use ($map, $caseInsensitive): string {
                $key = $caseInsensitive ? mb_convert_case($match[0], MB_CASE_FOLD, 'UTF-8') : $match[0];

                return $map[$key] ?? $match[0];
            },
            $value
        );

        return $result ?? $value;
    }

    public function __clone()
    {
        $this->compiled = [];
        $this->partition = null;
        $this->transliterationSplit = null;
    }

    private function set(string $search, string $replace): void
    {
        if ($search === '') {
            throw InvalidArgumentException::emptyRule();
        }

        // Re-inserting moves an overridden rule to the end, keeping "last wins" ordering obvious.
        unset($this->rules[$search]);
        $this->rules[$search] = $replace;
    }

    /**
     * @return array{pattern: string, map: array<string, string>}
     */
    private function compile(bool $caseInsensitive): array
    {
        $mode = $caseInsensitive ? 'i' : 's';

        if (isset($this->compiled[$mode])) {
            return $this->compiled[$mode];
        }

        $map = [];

        foreach ($this->rules as $search => $replace) {
            $search = (string) $search;
            $map[$caseInsensitive ? mb_convert_case($search, MB_CASE_FOLD, 'UTF-8') : $search] = $replace;
        }

        $searches = array_map(strval(...), array_keys($this->rules));
        usort($searches, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $alternatives = array_map(static fn (string $search): string => preg_quote($search, '/'), $searches);

        return $this->compiled[$mode] = [
            'pattern' => '/' . implode('|', $alternatives) . '/u' . ($caseInsensitive ? 'i' : ''),
            'map' => $map,
        ];
    }
}
