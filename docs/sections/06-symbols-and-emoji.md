## Symbols, Emoji & Numbers

Symbols, emoji and numbers each have their own explicit, independent policy.

### Symbols

By default, symbols are removed and act as word breaks:

```php
Slugify::make('R&D');           // "r-d"
Slugify::make('50% off');       // "50-off"
Slugify::make('Acme™ Pro');     // "acme-pro"
```

The one exception is the historical `@` → `at` [default rule](#rules): `user@host` → `user-at-host`.

Choose another policy with `symbols()`:

```php
use Pharaonic\Slugify\Policies\SymbolPolicy;

Slugify::of('R&D')->symbols(SymbolPolicy::words())->toString();       // "r-and-d"
Slugify::of('50% off')->symbols(SymbolPolicy::words())->toString();   // "50-percent-off"
Slugify::of('€100')->symbols(SymbolPolicy::words())->toString();      // "euro-100"

Slugify::of('R&D')->locale('de')->symbols(SymbolPolicy::words())->toString();   // "r-und-d"

Slugify::of('C# & C++')
    ->symbols(SymbolPolicy::custom(['#' => 'sharp', '+' => 'plus']))
    ->toString();                                                       // "c-sharp-c-plus-plus"
```

| Policy | Behavior |
| --- | --- |
| `SymbolPolicy::remove()` | Default. Symbols become word breaks. |
| `SymbolPolicy::words()` | Spells symbols out in the slug's locale (English fallback): `& + = %`, currencies, `© ® ™ @`. Words come from portable-ascii. `#` is not spelled out because its meaning varies (`C#`, `#1`). |
| `SymbolPolicy::custom(array $map)` | Spells out only the symbols you list. Others are removed. |

Replacement words are always separate words in the slug.

### Emoji

Emoji are removed by default, always as whole sequences. Skin tones, ZWJ families, flags, keycaps and variation selectors never leave invisible characters behind:

```php
Slugify::make('PHP 🚀 Rocks');     // "php-rocks"
Slugify::make('I ❤️ PHP');          // "i-php"
Slugify::make('👨‍👩‍👧‍👦 family');      // "family"
Slugify::make('Egypt 🇪🇬');        // "egypt"
```

To turn emoji into words, provide your own words. The package ships no emoji names:

```php
use Pharaonic\Slugify\Policies\EmojiPolicy;

$emoji = EmojiPolicy::custom(['🚀' => 'rocket', '👍' => 'thumbs up', '🇪🇬' => 'egypt']);

Slugify::of('PHP 🚀 Rocks')->emoji($emoji)->toString();   // "php-rocket-rocks"
Slugify::of('👍🏽')->emoji($emoji)->toString();            // "thumbs-up"
```

A custom word matches an emoji with any skin tone or presentation selector. Emoji you don't list are removed.

:::info "©" vs "©️"
`©` is a symbol and follows the symbol policy. `©️` (with the emoji variation selector) is an emoji and follows the emoji policy.
:::

### Numbers

Digits from any script, and unambiguous numeric forms, are converted to ASCII digits, in both Unicode and ASCII mode:

```php
Slugify::make('الإصدار ١٢');   // "الإصدار-12"  (Arabic-Indic)
Slugify::make('نسخه ۱۲');      // "نسخه-12"     (Persian)
Slugify::make('１２３');        // "123"         (full-width)
Slugify::make('①②③');         // "123"         (circled)
Slugify::make('H₂O');          // "h2o"         (subscript)
Slugify::make('10 m²');        // "10-m2"       (superscript)
Slugify::make('Top 1️⃣');       // "top-1"       (keycap)
```

Arabic-Indic `٣` and Persian `۳` look alike but are different characters. Normalizing both gives one slug for the same title.

Forms whose meaning isn't a plain number are left alone: fractions (`½`), Roman numerals (`Ⅻ`) and numbers without a decomposition (`❶`). An exponent never merges into its base: `10²` → `10-2`, not `102`.

Turn the conversion off with `normalizeNumbers(false)`:

```php
Slugify::of('الفصل ٣')->normalizeNumbers(false)->toString();   // "الفصل-٣"
```
