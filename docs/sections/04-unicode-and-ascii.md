## Unicode & ASCII

### Unicode Slugs (Default)

Unicode letters, numbers and combining marks are kept. Punctuation, symbols, emoji and whitespace become separators.

```php
Slugify::make('مرحبا بالعالم');      // "مرحبا-بالعالم"
Slugify::make('سلام دنیا');          // "سلام-دنیا"
Slugify::make('Привет, мир!');       // "привет-мир"
Slugify::make('Γειά σου Κόσμε');     // "γειά-σου-κόσμε"
Slugify::make('你好，世界');           // "你好-世界"
Slugify::make('नमस्ते दुनिया');         // "नमस्ते-दुनिया"
Slugify::make('Laravel مع PHP');     // "laravel-مع-php"
Slugify::make('I ❤️ PHP');            // "i-php"
```

Arabic diacritics (tashkeel) and the tatweel are removed by default, so vocalized and plain spellings give the same slug:

```php
Slugify::make('مُحَمَّد');             // "محمد"
Slugify::make('مـحـمـد');             // "محمد"
```

### ASCII Slugs

Pass `true` as the third argument, or call `->ascii()` on the fluent builder. Transliteration is handled by [voku/portable-ascii](https://github.com/voku/portable-ascii).

```php
Slugify::make('Crème brûlée', '-', true);   // "creme-brulee"
Slugify::make('Привет мир', '-', true);     // "privet-mir"
Slugify::make('مرحبا', '-', true);          // "mrhba"
Slugify::make('你好世界', '-', true);         // "ni-hao-shi-jie"
Slugify::make('한국어', '-', true);           // "hangugeo"

Slugify::of('Crème brûlée')->ascii()->toString();   // "creme-brulee"
```

ASCII mode always returns lowercase `[a-z0-9]` joined by your separator. Characters that can't be transliterated, such as emoji, are dropped.

### Language Hints

Some languages transliterate letters differently. Pass a language code as the fourth argument, or to `ascii()`:

```php
Slugify::make('München', '-', true);          // "munchen"
Slugify::make('München', '-', true, 'de');    // "muenchen"
Slugify::of('Äpfel und Öl')->ascii('de')->toString();   // "aepfel-und-oel"
```

Any language supported by portable-ascii works, and locale forms such as `de-DE` are accepted. Unknown codes fall back to the generic tables.

:::info Combining Characters
With `ext-intl` installed, decomposed input (`e` + U+0301) is composed to `é` first. Without it, the combining mark stays attached to its letter in Unicode mode. In ASCII mode both forms produce `e`.
:::
