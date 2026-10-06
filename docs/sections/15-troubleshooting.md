## Troubleshooting

### Cyrillic, Greek or Chinese titles are no longer romanized

Since the 8.x line, Unicode mode keeps every script. For Latin output, enable ASCII mode:

```php
Slugify::make('Привет мир', '-', true);   // "privet-mir"
```

### German umlauts become `a`/`o`/`u` instead of `ae`/`oe`/`ue`

Without a locale, transliteration uses the generic tables. Pass `de`:

```php
Slugify::make('Äpfel', '-', true, 'de');           // "aepfel"
Slugify::of('Äpfel')->locale('de')->ascii()->toString();   // "aepfel"
```

### `locale('de')` doesn't give an ASCII slug

That's by design: a locale never enables ASCII output. Add `->ascii()`.

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

### Arabic or Persian digits became `0-9`

Unicode digits are normalized to ASCII by default. Keep them with `->normalizeNumbers(false)`.

### `&` disappears from my slug

Symbols are removed by default. Spell them out with `->symbols(SymbolPolicy::words())`, or add a rule such as `->rule('&', ' and ')`.

### A slug looks wrong and I can't tell why

Call `->explain()` on the builder to see the text after every step of the pipeline.
