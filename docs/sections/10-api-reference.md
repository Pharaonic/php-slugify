## API Reference

### `Pharaonic\Slugify\Slugify`

| Method | Description | Returns |
| --- | --- | --- |
| `make(string $value, string $separator = '-', bool $ascii = false, ?string $language = null)` | Generate a slug. | `string` |
| `of(string $value, ?SlugOptions $options = null)` | Start a fluent builder that uses the current package-wide rules. | `Slugger` |
| `get(mixed $value, string $separator = '-', bool $ascii_only = false, ?string $ascii_lang = 'en')` | 2.x alias of `make()`. Accepts scalars, `Stringable` and `null`. | `string` |
| `addRule(string $search, string $replace)` | Add or override a package-wide rule. | `void` |
| `addRules(array $rules)` | Add or override several package-wide rules. | `void` |
| `rule(string $key, string $value)` | 2.x alias of `addRule()`. | `void` |
| `removeRule(string $search)` | Remove a package-wide rule, including a default. | `void` |
| `rules()` | The current package-wide rules. | `RuleSet` |
| `resetRules()` | Restore the package default rules. | `void` |
| `useTransliterator(?Transliterator $transliterator)` | Replace the package-wide transliterator; `null` restores the default. | `void` |

### `Pharaonic\Slugify\Slugger`

Every configuration method returns a new instance.

| Method | Description | Returns |
| --- | --- | --- |
| `separator(string $separator)` | Word separator. | `Slugger` |
| `lowercase(bool $lowercase = true)` | Lowercase the slug. | `Slugger` |
| `ascii(?string $language = null)` | Transliterate to ASCII, with an optional language hint. | `Slugger` |
| `unicode()` | Turn ASCII mode off (the default). | `Slugger` |
| `splitCamelCase(bool $split = true)` | Split camelCase words and acronyms. | `Slugger` |
| `maxLength(?int $maxLength)` | Limit the length in characters; `null` removes the limit. | `Slugger` |
| `rule(string $search, string $replace)` | Add a rule for this slug. | `Slugger` |
| `rules(array $rules)` | Add several rules for this slug. | `Slugger` |
| `withoutRules()` | Drop the default and package-wide rules. | `Slugger` |
| `transliterator(Transliterator $transliterator)` | Use a custom transliterator for this slug. | `Slugger` |
| `options()` | A copy of the current options. | `SlugOptions` |
| `toString()` / `__toString()` | Build the slug. | `string` |

### `Pharaonic\Slugify\SlugOptions`

| Property | Type | Default | Description |
| --- | --- | --- | --- |
| `separator` | `string` | `'-'` | Word separator. |
| `lowercase` | `bool` | `true` | Lowercase the slug. |
| `ascii` | `bool` | `false` | Transliterate to ASCII. |
| `language` | `?string` | `null` | Language hint for transliteration. |
| `splitCamelCase` | `bool` | `true` | Split camelCase words and acronyms. |
| `maxLength` | `?int` | `null` | Maximum length in characters. |

### `Pharaonic\Slugify\Rules\RuleSet`

An immutable, ordered rule collection, returned by `Slugify::rules()`.

| Method | Description | Returns |
| --- | --- | --- |
| `RuleSet::defaults()` | The package default rules. | `RuleSet` |
| `with(string $search, string $replace)` | Copy with one rule added. | `RuleSet` |
| `merge(RuleSet\|array $rules)` | Copy with more rules merged in. | `RuleSet` |
| `without(string $search)` | Copy without a rule. | `RuleSet` |
| `has(string $search)` | Whether a rule exists. | `bool` |
| `all()` | Every rule as `search => replacement`. | `array` |
| `isEmpty()` / `count()` | Size checks. | `bool` / `int` |
| `apply(string $value, bool $caseInsensitive = false)` | Apply the rules to a string. | `string` |

### Other

| Name | Description |
| --- | --- |
| `slug(mixed $value, string $separator = '-', bool $ascii_only = false, ?string $ascii_lang = 'en')` | Global helper; same as `Slugify::get()`. |
| `Contracts\Transliterator::transliterate(string $value, ?string $language = null): string` | Transliteration contract. |
| `Transliteration\PortableAsciiTransliterator` | Default transliterator (voku/portable-ascii). |
| `Exceptions\InvalidArgumentException` | Thrown for invalid separators, a `maxLength` below 1, empty rule search strings, and unsupported value types. |
