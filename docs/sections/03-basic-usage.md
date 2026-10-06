## Basic Usage

Import the `Slugify` class and call `make()`:

```php
use Pharaonic\Slugify\Slugify;

Slugify::make('Hello World');            // "hello-world"
Slugify::make('  Hello   World!!  ');    // "hello-world"
Slugify::make('hello - _ world');        // "hello-world"
Slugify::make('Top 10 Tips for 2026');   // "top-10-tips-for-2026"
```

The full signature is `make(string $value, string $separator = '-', bool $ascii = false, ?string $language = null)`:

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

Each call runs the same steps in order:

1. Repair invalid UTF-8 and, when `ext-intl` is installed, normalize to NFC.
2. Split camelCase words and acronyms.
3. Apply the replacement rules.
4. Transliterate to ASCII (ASCII mode only).
5. Lowercase.
6. Keep letters, numbers and combining marks (only `[A-Za-z0-9]` in ASCII mode), and join the words with the separator.
7. Apply `maxLength()`, if set.

Empty results are returned as `""`. `"0"` is treated as real input and gives `"0"`.
