# Changelog

All notable changes to this project will be documented in this file.

## 8.0.4 - 2026-10-07

### Added

- Support for `voku/portable-ascii` 1.x (`^1.6.1 || ^2.0`), so the package installs alongside Laravel 7 and 8, which require portable-ascii 1.x.

### Fixed

- With portable-ascii 1.x, the letters it transliterates differently from 2.x are given the 2.x result, so a slug is the same with either version: Persian `پ` (`p`, was `b`) and `ج` (`j`), the generic Cyrillic `ё ъ ы э ю я` (`Мэр` → `mer`, was `me-r`), and a few Ukrainian and Latin map entries. The list comes from comparing every BMP letter across every portable-ascii language.

## 8.0.3 - 2026-10-06

### Fixed

- Digits from the scripts added in Unicode 15 to 17 (Kawi, Nag Mundari, Garay, Sunuwar, Gurung Khema, Kirat Rai, Ol Onal, Tolong Siki, Myanmar Pa-O and Eastern Pwo Karen, and outlined digits) are converted to ASCII digits.
- Unicode normalization no longer skips Tulu-Tigalari, Gurung Khema and Kirat Rai text when PHP's PCRE library uses older Unicode data than ext-intl.

## 8.0.1 - 2026-10-06

The slug engine is now an explicit, documented pipeline: Unicode normalization, custom replacements, numbers, emoji, symbols, locale-aware lowercasing, transliteration, filtering and joining. Each concern is configured separately, and `explain()` exposes every step.

### Added

- `->locale()`. A locale selects language-specific behavior and never enables ASCII output by itself. `->ascii('de')` remains a shorthand for `->locale('de')->ascii()`.
- `SymbolPolicy` (`remove()` by default, `words()`, `custom()`) and `->symbols()`. `words()` uses portable-ascii's per-language symbol words (`&` → `and` / `und` / `ve`, currencies), falling back to English.
- `EmojiPolicy` (`remove()` by default, `custom()`) and `->emoji()`. Emoji are matched as whole grapheme clusters (ZWJ sequences, skin tones, flags, keycaps, tag sequences), so no joiner, selector or modifier is ever left behind.
- Number normalization, on by default in both modes, with `->normalizeNumbers(false)` to opt out. Decimal digits of every script (`١٢`, `۱۲`, `१२`, `１２`) and unambiguous compatibility numerals (`¹²`, `₁₂`, `①②`, `⑴`, `⒈`, `1️⃣`) become ASCII digits. Fractions and Roman numerals are left alone, and an exponent never merges into its base (`10²` → `10-2`).
- `->explain()`, which returns the text after every pipeline stage as structured data.
- `LocaleAwareTransliterator`, now the default transliterator. It applies curated locale overrides, then delegates to a wrapped generic transliterator.
- A Ukrainian ASCII override implementing the official 2010 national transliteration (`Київ` → `kyiv`, `Запоріжжя` → `zaporizhzhia`). portable-ascii maps `ж` to `z` and has no word-initial forms.
- Turkish and Azerbaijani lowercasing (`IŞIK` → `ışık` with `locale('tr')`).
- A locale evaluation corpus (`tests/Fixtures/Transliteration`, 19 languages). The suite fails if a locale override isn't justified by a failing generic result.
- `SlugOptions::$symbols`, `SlugOptions::$emoji` and `SlugOptions::$normalizeNumbers`, plus `RuleSet::splitForTransliteration()`.
- `symfony/polyfill-intl-normalizer` dependency. NFC normalization is now always applied, with or without `ext-intl`.

### Changed

- **Unicode digits are normalized to ASCII by default**: `الفصل ٣` → `الفصل-3` (was `الفصل-٣`). Use `->normalizeNumbers(false)` to keep native digits.
- **Text is lowercased before transliteration**, so ASCII output no longer depends on the input's case. Greek was the visible case: `Γειά σου` gave `geia-soy` but `γειά σου` gave `gheia-soy`; both now give `gheia-soy`.
- `e` + U+0301 and `é` now give the same slug in Unicode mode without `ext-intl` too.
- Presentation-only compatibility forms are folded: ligatures (`ﬁ`), full/half-width forms, Arabic presentation forms and mathematical alphanumerics.
- Invisible format characters (ZWJ, ZWNJ, soft hyphen, bidi marks, BOM, word joiner) are removed instead of splitting a word. The zero-width space, control characters and null bytes are word breaks.
- Hebrew points (niqqud) are removed like Arabic tashkeel, so vocalized and plain spellings match.
- The Arabic alef wasla (`ٱ`) becomes a plain alef, so Quranic and everyday spellings match: `ٱلْحَمْدُ` → `الحمد` (Unicode) / `alhmd` (ASCII, was `lhmd`).
- Arabic tashkeel stripping moved from the default rules into Unicode normalization. `RuleSet::defaults()` / `Slugify::rules()` now contain only `@` → `at`.
- In ASCII mode, only rules made of ASCII letters, digits, spaces, `_`, `.` and `-` run after transliteration. Rules containing other ASCII symbols (`$`, `c++`) now run first, so they always take precedence over the symbol policy.
- `->ascii()` without an argument keeps the current locale, and `->unicode()` no longer clears it.
- Locales with a region or script subtag (`uk-UA`, `fr-CA`, `sr-Latn`) now fall back to their base language for portable-ascii, instead of being treated as unknown.
- `maxLength()` never cuts a word inside a grapheme cluster.
- `MIGRATION.md` is renamed to `UPGRADE.md`.

### Fixed

- Keycap sequences (`1️⃣`) left the enclosing keycap mark in Unicode slugs.
- A stray variation selector or ZWJ after a letter (`a\u{FE0F}`) stayed in the Unicode slug.

## 8.0.0 - 2026-10-06

The `8.0.x` line is a full rebuild of the package for PHP 8.0. The package version now tracks the targeted PHP version. See [UPGRADE.md](UPGRADE.md) for upgrade notes.

### Added

- `Slugify::make()` as the primary API, with signature `make(string $value, string $separator = '-', bool $ascii = false, ?string $language = null)`.
- `Slugify::of()`, which returns an immutable fluent `Slugger`. Its methods are `separator()`, `lowercase()`, `ascii()`, `unicode()`, `splitCamelCase()`, `maxLength()`, `rule()`, `rules()`, `withoutRules()`, `transliterator()`, `options()`, `toString()` and `__toString()`.
- Per-slug rules (`->rule()`, `->rules()`) that never touch global state.
- `Slugify::addRule()`, `Slugify::addRules()`, `Slugify::removeRule()`, `Slugify::rules()` and `Slugify::resetRules()`.
- A `SlugOptions` options object.
- `maxLength()`, which cuts on a word boundary when possible.
- The `Transliterator` contract, with a default `PortableAsciiTransliterator`, and `Slugify::useTransliterator()`.
- ASCII mode now transliterates CJK, Korean, Japanese, Hebrew and Persian digits through portable-ascii's generic tables. Previously these were dropped.
- Optional Unicode NFC normalization when `ext-intl` is installed.
- `InvalidArgumentException` for invalid separators, max lengths, empty rules and unsupported values.
- A PHPUnit 9.6 test suite (unit, feature and regression fixtures), PHPStan level 9, PHP_CodeSniffer (PSR-12), a benchmark script and Composer scripts (`test`, `analyse`, `lint`, `format`, `check`, `benchmark`).
- Repository files: `CODE_OF_CONDUCT.md`, `CONTRIBUTING.md`, `SECURITY.md`, `MIGRATION.md`, issue and pull request templates, Dependabot, `.editorconfig` and `.gitattributes`.

### Changed

- **Unicode mode now preserves all scripts.** 2.x silently romanized some scripts (Greek, Cyrillic, Chinese, German umlauts) and kept others. For example, `Привет` is now `привет` instead of `privet`. Use ASCII mode for Latin output.
- CamelCase and acronym splitting is Unicode-aware and keeps acronyms together: `XMLHttpRequest` → `xml-http-request` (was `x-mlhttp-request`).
- Lowercasing happens after rules and transliteration, so rule replacements and transliterated text are lowercased too.
- Rules are applied in a single pass, longest search string first. Overlapping rules no longer cascade.
- In ASCII mode, rules with non-ASCII search strings (e.g. `ö`) run before transliteration, so they now take effect. Pure-ASCII rules still run after it.
- In ASCII mode the output is guaranteed to contain only `[a-z0-9]` and the separator.
- Separators are validated. Any string without letters, numbers, marks or whitespace is accepted (including `.`, `~` and `''`).
- The `@` → `at` default rule now also applies in ASCII mode (`user@host` → `user-at-host`).
- `Slugify` is a plain static class; the singleton facade layer is gone.
- Up to ~20× faster for short input, because the 7,686-entry dictionary is no longer scanned on every call.

### Fixed

- `'0'` produced an empty slug.
- Persian letters (`پ چ ژ گ`) were rewritten to different Arabic letters in Unicode mode.
- Combining marks split words apart, breaking Devanagari and other Indic scripts (`नमस्ते` → `NaMaSa-Ta`), and uppercase transliteration leaked into slugs.
- `İ` produced `i-stanbul`.
- 263 duplicate and 246 unreachable (uppercase) dictionary entries.
- Emoji variation selectors are no longer left in slugs as invisible characters.
- Invalid UTF-8 input is sanitized instead of breaking the regular expressions.

### Deprecated

- The `Pharaonic\Slugify\Facades\Slugify` import path. Use `Pharaonic\Slugify\Slugify`.

### Removed

- `Pharaonic\Slugify\Facades\Facade` and `Pharaonic\Slugify\Services\SlugifyService` (internal singleton plumbing).
- `src/resources/rules.php`. It is replaced by `voku/portable-ascii` plus two small default rule files (symbols and Arabic diacritics).
- `minimum-stability: dev`.

### Compatibility

- Requires PHP `>=8.0 <8.1`. Newer PHP versions are served by the matching `8.x` release lines.
- `slug()`, `Slugify::get()` and `Slugify::rule()` keep their 2.x signatures and behavior, except for the output changes listed above.
