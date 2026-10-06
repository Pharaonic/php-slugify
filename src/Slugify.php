<?php

namespace Pharaonic\Slugify;

use Pharaonic\Slugify\Contracts\Transliterator;
use Pharaonic\Slugify\Exceptions\InvalidArgumentException;
use Pharaonic\Slugify\Rules\RuleSet;
use Pharaonic\Slugify\Transliteration\LocaleAwareTransliterator;
use Stringable;

/**
 * Package entry point.
 *
 * Slugify::make('Hello World');                     // hello-world
 * Slugify::make('مرحبا بالعالم');                    // مرحبا-بالعالم (Unicode by default)
 * Slugify::of('Crème brûlée')->ascii()->toString(); // creme-brulee (ASCII on request)
 */
class Slugify
{
    /**
     * Package-wide rules: the package defaults plus anything added through addRule().
     */
    private static ?RuleSet $rules = null;

    private static ?Transliterator $transliterator = null;

    final private function __construct()
    {
    }

    /**
     * Generate a slug.
     *
     * @param string      $value     The text to slugify.
     * @param string      $separator Joins the words; may be empty.
     * @param bool        $ascii     Transliterate the slug to ASCII.
     * @param string|null $language  Locale for language-specific behavior (e.g. "de", "tr").
     */
    public static function make(
        string $value,
        string $separator = '-',
        bool $ascii = false,
        ?string $language = null
    ): string {
        return self::of($value, new SlugOptions($separator, true, $ascii, $language))->toString();
    }

    /**
     * Start a fluent, per-call configurable slug.
     */
    public static function of(string $value, ?SlugOptions $options = null): Slugger
    {
        return new Slugger(
            $value,
            $options,
            self::rules(),
            self::$transliterator ?? new LocaleAwareTransliterator()
        );
    }

    /**
     * Legacy entry point, kept for backward compatibility. Prefer make().
     *
     * Accepts any scalar or Stringable value; null yields an empty slug.
     */
    public static function get(
        mixed $value,
        string $separator = '-',
        bool $ascii_only = false,
        ?string $ascii_lang = 'en'
    ): string {
        return self::make(self::stringify($value), $separator, $ascii_only, $ascii_lang);
    }

    /**
     * Add (or override) a package-wide replacement rule.
     */
    public static function addRule(string $search, string $replace): void
    {
        self::$rules = self::rules()->with($search, $replace);
    }

    /**
     * Add (or override) several package-wide replacement rules.
     *
     * @param array<array-key, string> $rules
     */
    public static function addRules(array $rules): void
    {
        self::$rules = self::rules()->merge($rules);
    }

    /**
     * Legacy alias of addRule(), kept for backward compatibility.
     */
    public static function rule(string $key, string $value): void
    {
        self::addRule($key, $value);
    }

    /**
     * Remove a package-wide rule, including a package default such as "@".
     */
    public static function removeRule(string $search): void
    {
        self::$rules = self::rules()->without($search);
    }

    /**
     * The current package-wide rules.
     */
    public static function rules(): RuleSet
    {
        return self::$rules ??= RuleSet::defaults();
    }

    /**
     * Restore the package-wide rules to the package defaults.
     */
    public static function resetRules(): void
    {
        self::$rules = null;
    }

    /**
     * Replace the package-wide transliterator; null restores the default.
     *
     * The default applies Pharaonic's curated locale overrides around voku/portable-ascii.
     * A custom transliterator replaces both.
     */
    public static function useTransliterator(?Transliterator $transliterator): void
    {
        self::$transliterator = $transliterator;
    }

    private static function stringify(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value) || $value instanceof Stringable) {
            return (string) $value;
        }

        throw InvalidArgumentException::unsupportedValue($value);
    }
}
