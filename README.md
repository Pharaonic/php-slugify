<p align="center"><a href="https://pharaonic.io" target="_blank"><img src="https://raw.githubusercontent.com/Pharaonic/logos/main/php/slugify.jpg" alt="Pharaonic Slugify"></a></p>

<p align="center">
  <a href="https://github.com/Pharaonic/php-slugify/actions/workflows/build.yml" target="_blank"><img src="https://github.com/Pharaonic/php-slugify/actions/workflows/build.yml/badge.svg" alt="Build"></a>
  <a href="https://php.net" target="_blank"><img src="https://img.shields.io/static/v1?label=PHP&message=8.0&color=blue&style=flat-square" alt="PHP Version: 8.0"></a>
  <img src="https://img.shields.io/static/v1?label=License&message=MIT&color=brightgreen&style=flat-square" alt="License">
  <br>
  <a href="https://packagist.org/packages/pharaonic/php-slugify" target="_blank"><img src="https://img.shields.io/static/v1?label=Packagist&message=pharaonic/php-slugify&color=blue&logo=packagist&logoColor=white" alt="Source"></a>
  <a href="https://packagist.org/packages/pharaonic/php-slugify" target="_blank"><img src="https://poser.pugx.org/pharaonic/php-slugify/v" alt="Packagist Version"></a>
  <a href="https://packagist.org/packages/pharaonic/php-slugify" target="_blank"><img src="https://poser.pugx.org/pharaonic/php-slugify/downloads" alt="Packagist Downloads"></a>
</p>

<h3 align="center">Fast, framework-agnostic slug generation for PHP with Unicode, transliteration, custom separators, and configurable replacement rules.</h3>
<br>

## Overview

```php
use Pharaonic\Slugify\Slugify;

Slugify::make('Hello World');            // hello-world
Slugify::make('مرحبا بالعالم');           // مرحبا-بالعالم
Slugify::make('Crème brûlée', '-', true); // creme-brulee
```

`pharaonic/php-slugify` turns any string into a URL-friendly slug. It keeps Unicode by default, can transliterate to ASCII on demand, and stays out of your way: no framework, no container, no configuration files.

## Features

- **Unicode first**: Arabic, Persian, Greek, Cyrillic, CJK, Hebrew, Devanagari and more are preserved as-is.
- **Optional ASCII mode**: transliteration powered by [voku/portable-ascii](https://github.com/voku/portable-ascii), with language hints (`de` → `ä` = `ae`).
- **Smart word splitting**: `XMLHttpRequest` → `xml-http-request`, `getUserID` → `get-user-id`.
- **Any separator**: `-`, `_`, `.`, `~`, an empty string, or something else.
- **Replacement rules**: package-wide or per call, without leaking state.
- **Fluent, immutable builder** for advanced use, plus a `maxLength()` that cuts on word boundaries.
- **Backward compatible**: `slug()`, `Slugify::get()` and `Slugify::rule()` keep working.

## Requirements

| Package version | PHP   |
|-----------------|-------|
| `8.0.x`         | `8.0` |

- `ext-mbstring`
- `ext-intl` *(optional)*: when installed, decomposed input such as `e` + U+0301 is normalized (NFC) before slugging.

## Installation

```bash
composer require pharaonic/php-slugify
```

## Quick Start

```php
use Pharaonic\Slugify\Slugify;

Slugify::make('Hello World');          // hello-world
Slugify::make('  Hello   World!!  ');  // hello-world
Slugify::make('Hello World', '_');     // hello_world

// Or use the global helper
slug('Hello World');                   // hello-world
```

## Unicode Slugs

Unicode letters, numbers and combining marks are kept. Everything else becomes a separator.

```php
Slugify::make('مرحبا بالعالم');        // مرحبا-بالعالم
Slugify::make('سلام دنیا');            // سلام-دنیا
Slugify::make('Привет, мир!');         // привет-мир
Slugify::make('Γειά σου Κόσμε');       // γειά-σου-κόσμε
Slugify::make('你好，世界');             // 你好-世界
Slugify::make('नमस्ते दुनिया');           // नमस्ते-दुनिया
Slugify::make('Laravel مع PHP');       // laravel-مع-php
```

Arabic diacritics (tashkeel) and the tatweel are removed by default, so vocalized and plain spellings match:

```php
Slugify::make('مُحَمَّد');               // محمد
```

## ASCII Slugs

Pass `true` as the third argument, or call `->ascii()` on the fluent builder:

```php
Slugify::make('Crème brûlée', '-', true);  // creme-brulee
Slugify::make('Привет мир', '-', true);    // privet-mir
Slugify::make('你好世界', '-', true);        // ni-hao-shi-jie

Slugify::of('Crème brûlée')->ascii()->toString(); // creme-brulee
```

An optional language hint enables language-specific transliteration. It accepts any language supported by `voku/portable-ascii` (locale forms like `de-DE` work too). Unknown languages fall back to the generic tables.

```php
Slugify::make('Äpfel und Öl', '-', true);        // apfel-und-ol
Slugify::make('Äpfel und Öl', '-', true, 'de');  // aepfel-und-oel

Slugify::of('Äpfel und Öl')->ascii('de')->toString(); // aepfel-und-oel
```

ASCII mode always returns `[a-z0-9]` plus your separator. Characters that cannot be transliterated (such as emoji) are dropped.

## Custom Separators

Any string without letters, numbers, marks or whitespace can be used as a separator. Existing dashes, underscores and punctuation in the input are normalized to it, and repeats are collapsed.

```php
Slugify::make('Hello World', '-');   // hello-world
Slugify::make('Hello World', '_');   // hello_world
Slugify::make('Hello World', '.');   // hello.world
Slugify::make('Hello World', '~');   // hello~world
Slugify::make('Hello World', '');    // helloworld

Slugify::make('hello - _ world');    // hello-world
```

An invalid separator (for example `'x'` or `' '`) throws `Pharaonic\Slugify\Exceptions\InvalidArgumentException`.

## Custom Rules

Rules replace text before the slug is built. Replacements are literal, so add spaces to make a replacement its own word.

```php
Slugify::addRule('&', ' and ');
Slugify::make('Tom & Jerry');  // tom-and-jerry

Slugify::addRules([
    'ö' => 'oe',
    'ü' => 'ue',
    'ä' => 'ae',
]);
Slugify::make('Äpfel Öl');     // aepfel-oel
```

How rules behave:

- When the slug is lowercased (the default), rules match case-insensitively.
- Rules are applied in a single pass, and the longest search string wins. A replacement is never re-processed by another rule.
- Adding a rule with an existing search string overrides it.
- In ASCII mode, rules whose search string contains non-ASCII characters (e.g. `ö`) run **before** transliteration. Pure-ASCII rules (e.g. `allh` → `allah`) run **after** it, so they also match transliterated text.

The package ships with a small set of default rules: `@` → `at`, plus the Arabic diacritic removal. Remove or override them like any other rule:

```php
Slugify::make('user@host');      // user-at-host

Slugify::removeRule('@');
Slugify::make('user@host');      // user-host

Slugify::resetRules();           // back to the package defaults
Slugify::rules()->all();         // inspect the current package-wide rules
```

Package-wide rules are global, so register them once during bootstrap.

## Fluent API

`Slugify::of()` returns an immutable `Slugger`. Every method returns a new instance, and nothing touches global state.

```php
Slugify::of('Hello World')
    ->separator('_')
    ->lowercase()
    ->toString();                              // hello_world

Slugify::of('500$ Bill')
    ->rule('$', ' dollar ')                    // this slug only
    ->toString();                              // 500-dollar-bill

Slugify::of('The quick brown fox jumps')
    ->maxLength(15)                            // cuts on a word boundary
    ->toString();                              // the-quick-brown

(string) Slugify::of('Hello World');           // hello-world
```

| Method                            | Description                                                                |
|-----------------------------------|----------------------------------------------------------------------------|
| `separator(string $separator)`    | Word separator (default `-`).                                              |
| `lowercase(bool $lowercase = true)` | Lowercase the slug (default on).                                         |
| `ascii(?string $language = null)` | Transliterate to ASCII, with an optional language hint.                    |
| `unicode()`                       | Keep Unicode (the default); turns ASCII mode off.                          |
| `splitCamelCase(bool $split = true)` | Split `camelCase`, `PascalCase` and acronyms (default on).              |
| `maxLength(?int $maxLength)`      | Limit the length in characters. Cuts on a word boundary when possible.     |
| `rule(string $search, string $replace)` | Add a rule for this slug only.                                       |
| `rules(array $rules)`             | Add several rules for this slug only.                                      |
| `withoutRules()`                  | Ignore the package defaults and package-wide rules.                        |
| `transliterator(Transliterator $t)` | Use a custom transliterator for this slug.                               |
| `options(): SlugOptions`          | A copy of the current options.                                             |
| `toString()` / `__toString()`     | Build the slug.                                                            |

Package-wide rules are captured when `Slugify::of()` is called. Rules added afterwards do not affect a builder that already exists.

## CamelCase & Acronyms

```php
Slugify::make('helloWorld');      // hello-world
Slugify::make('XMLHttpRequest');  // xml-http-request
Slugify::make('APIResponse');     // api-response
Slugify::make('getUserID');       // get-user-id
Slugify::make('PharaonicPHP');    // pharaonic-php
Slugify::make('3D Printing');     // 3d-printing

Slugify::of('helloWorld')->splitCamelCase(false)->toString(); // helloworld
```

## Backward Compatibility

The 2.x API keeps working:

```php
slug('Hello World');                    // hello-world
slug('Hello World', '_', true, 'en');   // hello_world

Slugify::get('Hello World');            // alias of Slugify::make()
Slugify::rule('ö', 'oe');               // alias of Slugify::addRule()

use Pharaonic\Slugify\Facades\Slugify;  // deprecated import path, still works
```

`slug()` and `Slugify::get()` also accept any scalar or `Stringable` value. `null` gives an empty slug. See [MIGRATION.md](MIGRATION.md) for the output changes in 8.0.

## Advanced Usage

### Options object

```php
use Pharaonic\Slugify\SlugOptions;

$options = new SlugOptions(separator: '_', ascii: true, language: 'de', maxLength: 60);

Slugify::of('München ist schön', $options)->toString(); // muenchen_ist_schoen
```

### Custom transliterator

Implement `Pharaonic\Slugify\Contracts\Transliterator` to plug in another engine, such as ICU:

```php
use Pharaonic\Slugify\Contracts\Transliterator;

final class IntlTransliterator implements Transliterator
{
    public function transliterate(string $value, ?string $language = null): string
    {
        return \Transliterator::create('Any-Latin; Latin-ASCII')->transliterate($value);
    }
}

Slugify::useTransliterator(new IntlTransliterator());          // package-wide
Slugify::of('Привет')->ascii()->transliterator(new IntlTransliterator()); // one slug
Slugify::useTransliterator(null);                               // back to the default
```

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse   # PHPStan, level 9
composer lint      # PHP_CodeSniffer, PSR-12
composer check     # all of the above
```

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) and the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security

Please do not report security vulnerabilities in public issues. See [SECURITY.md](SECURITY.md).

## Support

See [SUPPORT.md](SUPPORT.md).

## License

This package is open-source software licensed under the [MIT license](LICENSE).
