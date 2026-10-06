## Locales

A locale selects **language-specific behavior**. It never turns ASCII output on by itself:

- **locale**: how language-specific behavior works.
- **ascii**: whether the output must be ASCII.

```php
Slugify::of('Äpfel Straße')->locale('de')->toString();           // "äpfel-straße"
Slugify::of('Äpfel Straße')->locale('de')->ascii()->toString();  // "aepfel-strasse"
Slugify::of('Äpfel Straße')->ascii()->toString();                // "apfel-strasse"
```

`ascii('de')` is a shorthand for `locale('de')->ascii()`, and `make()` takes the locale as its fourth argument:

```php
Slugify::of('München')->ascii('de')->toString();   // "muenchen"
Slugify::make('München', '-', true, 'de');         // "muenchen"
```

Locale codes can include a region or script: `de-DE`, `de_AT`, `sr-Latn` and `uk-UA` all work. If portable-ascii has a regional variant (`de-AT` writes `ß` as `sz`), it is used. Otherwise the base language applies. Unknown or malformed codes fall back to the generic behavior.

### In Unicode Mode

Turkish and Azerbaijani have a dotless `ı`, so their capital `I` lowercases differently:

```php
Slugify::make('IŞIK');                         // "işik"
Slugify::of('IŞIK')->locale('tr')->toString(); // "ışık"
Slugify::of('IŞIQ')->locale('az')->toString(); // "ışıq"
```

### In ASCII Mode

The locale is passed to portable-ascii, which has maps for dozens of languages. Pharaonic adds an override **only** where the generic result is demonstrably wrong:

| Locale | Generic result | With the locale | Pharaonic override |
| --- | --- | --- | --- |
| `de` German | `apfel` | `aepfel` | No (portable-ascii) |
| `sr` Serbian | `shabats`, `dorde` | `sabac`, `djordje` | No (portable-ascii) |
| `uk` Ukrainian | `kiyiv`, `zuk` | `kyiv`, `zhuk` | **Yes**: the official 2010 national system |
| `tr`, `az`, `pl`, `ro`, `vi` | already correct | — | No |
| `ru`, `el`, `ar`, `fa`, `he`, `hy`, `ka`, `zh`, `ja`, `ko`, `my` | deterministic and readable | — | No |

```php
Slugify::of('Київ Запоріжжя Щастя')->ascii('uk')->toString();
// "kyiv-zaporizhzhia-shchastia"
```

Your own [rules](#rules) always win over locale overrides, which win over the generic transliterator:

```php
Slugify::of('Київ')->ascii('uk')->rule('київ', 'kiev')->toString();   // "kiev"
```

:::info Requesting a locale override
Overrides live in `src/Resources/locales/` and each needs a sample in the test corpus (`tests/Fixtures/Transliteration/`) where the generic result fails. The test suite rejects an override that the corpus doesn't justify.
:::
