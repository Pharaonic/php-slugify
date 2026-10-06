## Replacement Rules

Rules replace text before the slug is built. Replacements are literal, so add spaces when the replacement should be its own word.

### Package-wide Rules

```php
Slugify::addRule('&', ' and ');
Slugify::make('Tom & Jerry');    // "tom-and-jerry"

Slugify::addRules([
    'ö' => 'oe',
    'ü' => 'ue',
    'ä' => 'ae',
]);
Slugify::make('Äpfel Öl');       // "aepfel-oel"
```

Package-wide rules are global static state. Register them once during your app's bootstrap, for example in a Laravel service provider's `boot()` method.

### Per-slug Rules

Rules added on the fluent builder apply to that slug only:

```php
Slugify::of('500$ Bill')->rule('$', ' dollar ')->toString();   // "500-dollar-bill"
Slugify::make('500$ Bill');                                      // "500-bill"

Slugify::of('C++ & C#')
    ->rules(['c++' => 'cpp', '&' => ' and ', 'c#' => 'csharp'])
    ->toString();                                                // "cpp-and-csharp"
```

### Default Rule

The package ships with one default rule, kept from 2.x:

| Rule | Purpose |
| --- | --- |
| `@` → ` at ` | `user@host` → `user-at-host` |

Override or remove it like any other rule:

```php
Slugify::addRule('@', ' chez ');   // override
Slugify::removeRule('@');          // remove
Slugify::resetRules();             // restore the package defaults
Slugify::rules()->all();           // inspect: ['@' => ' at ']
```

Other symbols are handled by the [symbol policy](#symbols-and-emoji). Arabic tashkeel and Hebrew points are removed by [Unicode normalization](#unicode-and-ascii), not by rules.

To ignore every default and package-wide rule for one slug, call `withoutRules()`:

```php
Slugify::of('user@host')->withoutRules()->toString();   // "user-host"
```

### How Rules Match

- **Case**: when the slug is lowercased (the default), rules match case-insensitively. With `lowercase(false)` they match exactly.
- **Single pass**: all rules are applied at once and the longest search string wins. A replacement is never re-processed by another rule.
- **Overrides**: adding a rule with an existing search string replaces it, and per-slug rules override package-wide ones.
- **Precedence**: rules run before numbers, emoji, symbols, locale overrides and transliteration, so your rules always win.
- **ASCII mode**: rules made only of ASCII letters, digits, spaces, `_`, `.` and `-` (`allh` → `allah`) run *after* transliteration, so they also match transliterated text. Every other rule (`ö`, `$`, `c++`) runs first.

```php
Slugify::addRule('allh', 'allah');
Slugify::make('بسم الله', '-', true);   // "bsm-allah"
```
