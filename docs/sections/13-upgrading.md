## Upgrading from 8.0

{release.label} targets PHP 8.1. The public API and slug output are the same as in 8.0, so no code changes are needed: require PHP `>=8.1 <8.2`, or stay on `8.0.x` while you run PHP 8.0.

## Upgrading from 2.x

The 2.x API keeps working, but some slugs come out differently. Slugs you've already stored are not affected; only newly generated slugs can change.

### API Mapping

| 2.x | {release.label} | Status |
| --- | --- | --- |
| `slug($value, $sep, $ascii, $lang)` | unchanged | Supported |
| `Slugify::get(...)` | `Slugify::make(...)` | Supported alias |
| `Slugify::rule($key, $value)` | `Slugify::addRule($search, $replace)` | Supported alias |
| `use Pharaonic\Slugify\Facades\Slugify;` | `use Pharaonic\Slugify\Slugify;` | Deprecated, still works |
| `Services\SlugifyService`, `Facades\Facade` | none | Removed (internal) |

### Output Changes

**Unicode mode keeps every script.** 2.x romanized some scripts and kept others:

| Input | 2.x | Now |
| --- | --- | --- |
| `Привет мир` | `privet-mir` | `привет-мир` |
| `München` | `muenchen` | `münchen` |
| `你好世界` | `nihaoshijie` | `你好世界` |
| `پژوهش` | `بزوهش` | `پژوهش` |

To get Latin output, use ASCII mode: `Slugify::make($title, '-', true, 'de')`.

**Acronyms stay together.** `XMLHttpRequest` now gives `xml-http-request` (2.x: `x-mlhttp-request`).

**Fixes.** `'0'` now gives `'0'` instead of `''`. `İstanbul` gives `istanbul`. In ASCII mode, rules such as `ö` → `oe` now apply, `@` becomes `at`, and CJK and Korean text is transliterated instead of dropped.

**Rules apply in one pass.** Adding `a` → `b` and `b` → `c` now turns `ab` into `bc` (2.x: `cc`).

:::info Laravel Sluggable
`pharaonic/laravel-sluggable` calls `slug($value, $separator, $ascii_only, $ascii_lang)`, and that signature is unchanged. If your app relied on the 2.x romanization, set `ascii_only` to `true` in its config.
:::
