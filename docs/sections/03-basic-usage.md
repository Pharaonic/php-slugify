## Basic Usage

Import the `Slugify` class and call `make()`:

```php
use Pharaonic\Slugify\Slugify;

Slugify::make('Hello World');            // "hello-world"
Slugify::make('  Hello   World!!  ');    // "hello-world"
Slugify::make('hello - _ world');        // "hello-world"
Slugify::make('Top 10 Tips for 2026');   // "top-10-tips-for-2026"
```

The full signature is `make(string $value, string $separator = '-', bool $ascii = false, ?string $language = null)`. `$language` is the [locale](#locales):

```php
Slugify::make('Hello World', '_');                // "hello_world"
Slugify::make('Crème brûlée', '-', true);         // "creme-brulee"
Slugify::make('Äpfel und Öl', '-', true, 'de');   // "aepfel-und-oel"
```

### The `slug()` Helper

The global helper takes the same arguments. It also accepts any scalar or `Stringable` value, and `null` gives an empty string.

```php
slug('Hello World');                // "hello-world"
slug('Hello World', '_', true);     // "hello_world"
slug(2026);                         // "2026"
slug(null);                         // ""
```

### How a Slug Is Built

Each call runs the same deterministic steps in order. Every step can be inspected with [`explain()`](#fluent-api).

1. **Unicode normalization**: repair invalid UTF-8, turn control characters into word breaks, normalize to NFC and fold presentation forms (`ﬁ` → `fi`).
2. **CamelCase splitting**: `helloWorld` → `hello World`.
3. **Replacement rules**: your rules, before anything else changes the text.
4. **Numbers**: `١٢`, `۱۲`, `①②` → `12`.
5. **Emoji policy**: removed by default, as whole sequences.
6. **Symbol policy**: removed by default.
7. **Lowercasing**, locale-aware (`tr`: `I` → `ı`).
8. **Transliteration** (ASCII mode only): locale overrides, then portable-ascii. In ASCII mode, rules made of ASCII words (`allh` → `allah`) run right after this step.
9. **Filtering**: invisible characters are dropped. Letters, numbers and combining marks are kept (only `[A-Za-z0-9]` in ASCII mode).
10. **Joining**: words joined with the separator, within `maxLength()` if set.

Empty results are returned as `""`. `"0"` is treated as real input and gives `"0"`.
