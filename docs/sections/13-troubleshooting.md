## Troubleshooting

### Cyrillic, Greek or Chinese titles are no longer romanized

Since the 8.x line, Unicode mode keeps every script. For Latin output, enable ASCII mode:

```php
Slugify::make('Привет мир', '-', true);   // "privet-mir"
```

### German umlauts become `a`/`o`/`u` instead of `ae`/`oe`/`ue`

Without a language hint, transliteration uses the generic tables. Pass `de`:

```php
Slugify::make('Äpfel', '-', true, 'de');  // "aepfel"
```

Or add rules: `Slugify::addRules(['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue'])`.

### My rule's replacement sticks to the next word

Replacements are literal. Add spaces around the replacement to make it a separate word:

```php
Slugify::of('500$')->rule('$', 'dollar')->toString();     // "500dollar"
Slugify::of('500$')->rule('$', ' dollar ')->toString();   // "500-dollar"
```

### A rule added at runtime doesn't apply

`Slugify::of()` captures the package-wide rules when it is called. Register rules before you create the builder, or add the rule to the builder with `->rule()`.

### Rules leak between tests

Package-wide rules are static. Call `Slugify::resetRules()` and `Slugify::useTransliterator(null)` in your test's `setUp()`/`tearDown()`.

### `InvalidArgumentException` about the separator

The separator contains a letter, number, combining mark or whitespace. Use punctuation such as `-`, `_`, `.` or `~`, or an empty string.

### Decomposed and composed input give different Unicode slugs

`e` + U+0301 and `é` only match in Unicode mode when `ext-intl` is installed, because that's what provides NFC normalization. Install `ext-intl`, or use ASCII mode, where both give `e`.
