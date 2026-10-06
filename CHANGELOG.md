# Changelog

All notable changes to this project will be documented in this file.

## 8.0.0 - 2026-10-06

The `8.0.x` line is a full rebuild of the package for PHP 8.0. The package version now tracks the targeted PHP version. See [MIGRATION.md](MIGRATION.md) for upgrade notes.

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
