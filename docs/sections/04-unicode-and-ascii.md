## Unicode & ASCII

Slugify is **Unicode-first**: by default a slug keeps the letters of every script. ASCII output is something you ask for explicitly.

### Unicode Slugs (Default)

Unicode letters, numbers and combining marks are kept. Punctuation, symbols and whitespace become separators, and emoji are removed.

```php
Slugify::make('مرحبا بالعالم');      // "مرحبا-بالعالم"
Slugify::make('سلام دنیا');          // "سلام-دنیا"
Slugify::make('Привет, мир!');       // "привет-мир"
Slugify::make('Γειά σου Κόσμε');     // "γειά-σου-κόσμε"
Slugify::make('你好，世界');           // "你好-世界"
Slugify::make('नमस्ते दुनिया');         // "नमस्ते-दुनिया"
Slugify::make('Laravel مع PHP');     // "laravel-مع-php"
Slugify::make('PHP 🚀 Rocks');        // "php-rocks"
```

Arabic diacritics (tashkeel), the tatweel and Hebrew points (niqqud) are removed in every mode, and the alef wasla (`ٱ`) becomes a plain alef, so vocalized and plain spellings give the same slug:

```php
Slugify::make('مُحَمَّد');             // "محمد"
Slugify::make('مـحـمـد');             // "محمد"
Slugify::make('ٱلْحَمْدُ');            // "الحمد"
Slugify::make('שָׁלוֹם');              // "שלום"
```

### ASCII Slugs

Pass `true` as the third argument, or call `->ascii()` on the fluent builder. Transliteration is handled by [voku/portable-ascii](https://github.com/voku/portable-ascii), plus a few curated [locale overrides](#locales).

```php
Slugify::make('Crème brûlée', '-', true);   // "creme-brulee"
Slugify::make('Привет мир', '-', true);     // "privet-mir"
Slugify::make('مرحبا', '-', true);          // "mrhba"
Slugify::make('你好世界', '-', true);         // "ni-hao-shi-jie"
Slugify::make('한국어', '-', true);           // "hangugeo"

Slugify::of('Crème brûlée')->ascii()->toString();   // "creme-brulee"
```

ASCII mode always returns `[a-z0-9]` words joined by your separator (`[A-Za-z0-9]` with `lowercase(false)`). Characters that can't be transliterated are dropped.

The text is lowercased **before** it is transliterated, so the result never depends on the input's case: `Χαρά`, `χαρά` and `ΧΑΡΆ` all give the same slug.

:::info Transliteration is not romanization
ASCII mode makes readable, deterministic slugs. It is not a linguistic romanization engine: unvocalized scripts such as Arabic and Hebrew lose information, and Japanese kanji are read as Chinese. If you need a specific standard, add [rules](#rules) or a [custom transliterator](#extending).
:::

### Unicode Normalization

Every slug starts from well-formed, canonical text. This works the same with or without `ext-intl`, because the package requires `symfony/polyfill-intl-normalizer`.

- **NFC**: `e` + U+0301 and `é` give the same slug in both modes.
- **Presentation forms** are folded to the letters they display: ligatures (`ﬁ` → `fi`), full-width and half-width forms (`Ｈｅｌｌｏ` → `hello`, `ｶﾀｶﾅ` → `カタカナ`), Arabic presentation forms, and mathematical alphanumerics (`𝐇𝐞𝐥𝐥𝐨` → `hello`).
- Symbols with a compatibility form, such as `™` or `½`, are **not** folded. They follow the [symbol policy](#symbols-and-emoji).
- **Invalid UTF-8**, control characters, null bytes and the zero-width space become word breaks.
- **Invisible characters** never reach the slug: zero-width joiners, soft hyphens, bidi marks, the BOM and variation selectors are removed. A soft hyphen or a Persian ZWNJ inside a word doesn't split it: `می‌خواهم` → `میخواهم`.
