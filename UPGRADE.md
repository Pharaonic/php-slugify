# Upgrade Guide

## From 8.1 to 8.2

Version 8.2 targets PHP 8.2. The public API and slug output are unchanged.

### Requirements

- PHP `>=8.2 <8.3`. Stay on `8.1.x` while you run PHP 8.1.

No code changes are needed.

## From 8.0 to 8.1

Version 8.1 targets PHP 8.1. The public API and slug output are unchanged.

### Requirements

- PHP `>=8.1 <8.2`. Stay on `8.0.x` while you run PHP 8.0.

No code changes are needed.

## From 2.x to 8.0

Version 8.0 rebuilds the package for PHP 8.0. The public 2.x API still works, but some slugs are different. Review the **Output changes** section below.

### Requirements

- PHP `>=8.0 <8.1` (one release line per PHP version: `8.0.x`, `8.1.x`, ...).
- `ext-intl` is optional. Unicode NFC normalization always applies (through `symfony/polyfill-intl-normalizer` when the extension is missing); the extension only makes it faster.

### API mapping

| 2.x                                      | 8.0                                     | Status                                  |
|------------------------------------------|-----------------------------------------|-----------------------------------------|
| `slug($value, $sep, $ascii, $lang)`      | unchanged                               | Supported                               |
| `Slugify::get($value, $sep, $ascii, $lang)` | `Slugify::make($value, $sep, $ascii, $lang)` | Supported alias, no warning        |
| `Slugify::rule($search, $replace)`       | `Slugify::addRule($search, $replace)`   | Supported alias, no warning             |
| several `Slugify::rule()` calls          | `Slugify::addRules([...])`              | New                                     |
| `use Pharaonic\Slugify\Facades\Slugify;` | `use Pharaonic\Slugify\Slugify;`        | Deprecated (works, no runtime warning)  |
| `Pharaonic\Slugify\Services\SlugifyService` | `Slugify` / `Slugger`                | Removed (internal)                      |
| `Pharaonic\Slugify\Facades\Facade`       | none                                    | Removed (internal)                      |

`Slugify::get()` and `slug()` still accept mixed values: scalars, `Stringable`, or `null`, which gives `''`. `Slugify::make()` and `Slugify::of()` require a string. Any other value type now throws `Pharaonic\Slugify\Exceptions\InvalidArgumentException`.

### Output changes

Slugs that were already stored are unaffected. Only newly generated slugs can differ.

#### 1. Unicode mode keeps every script

In 2.x the default (non-ASCII) mode ran a large dictionary that romanized some scripts and left others alone. In 8.0, Unicode mode keeps all letters:

| Input            | 2.x              | 8.0              |
|------------------|------------------|------------------|
| `Привет мир`     | `privet-mir`     | `привет-мир`     |
| `Γειά σου`       | `gia-sou`        | `γειά-σου`       |
| `你好世界`         | `nihaoshijie`    | `你好世界`         |
| `München`        | `muenchen`       | `münchen`        |
| `Crème brûlée`   | `creme-brulée`   | `crème-brûlée`   |
| `پژوهش`          | `بزوهش`          | `پژوهش`          |

If you need Latin output, enable ASCII mode:

```php
Slugify::make('Привет мир', '-', true);        // privet-mir
Slugify::make('München', '-', true, 'de');     // muenchen
```

Or restore specific replacements with rules:

```php
Slugify::addRules(['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
```

#### 2. Acronyms stay together

| Input            | 2.x                | 8.0                |
|------------------|--------------------|--------------------|
| `XMLHttpRequest` | `x-mlhttp-request` | `xml-http-request` |
| `APIResponse`    | `a-piresponse`     | `api-response`     |

`getUserID`, `PharaonicPHP`, `helloWorld` and `There is FAQ module here` are unchanged.

#### 3. ASCII mode

- Rules with non-ASCII search strings now apply in ASCII mode. For example, `Slugify::rule('ö', 'oe')` turns `Öl` into `oel`; 2.x gave `ol`.
- The default `@` rule now applies in ASCII mode: `user@host` gives `user-at-host` (2.x: `user-host`).
- Scripts that 2.x dropped are now transliterated: `你好世界` → `ni-hao-shi-jie`, `한국어` → `hangugeo`, `۱۲۳` → `123`.
- Output is always lowercase ASCII. 2.x could leak uppercase letters, for example from Devanagari.

#### 4. Rules

- Rules are applied in one pass, longest match first. A replacement is not re-processed by later rules. Adding `a → b` and `b → c` turns `ab` into `bc` (2.x: `cc`).
- Replacements are lowercased with the rest of the slug.
- The default rule set is now only `@` → `at` plus Arabic diacritic and tatweel removal. Remove `@` with `Slugify::removeRule('@')`.

#### 5. Other fixes

- `'0'` now gives `'0'` (2.x: `''`).
- `İstanbul` gives `istanbul` (2.x: `i-stanbul`).
- Separators that contain letters, numbers, marks or whitespace throw `InvalidArgumentException`.

### pharaonic/laravel-sluggable

`laravel-sluggable` only calls `slug($value, $separator, $ascii_only, $ascii_lang)`, and that signature is unchanged in 8.0. It currently requires `pharaonic/php-slugify:^2.0`, so it will not pick up 8.0 until its constraint is widened. When widening it, call out the Unicode-mode output change above in its release notes. Projects that relied on 2.x romanization in Unicode mode should set `ascii_only` to `true`.
