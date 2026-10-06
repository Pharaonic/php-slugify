:::badges
- PHP Package {color=blue}
- {release.label} {color=green}
- {package.license} License {color=purple}
:::

# Slugify

Fast, framework-agnostic slug generation for PHP. Turn any string into a URL-friendly slug. By default it keeps Unicode letters as they are. ASCII is opt-in, locales only change language-specific behavior, and symbols, emoji and numbers each follow an explicit policy. Same input, same slug, every time.

:::features
### Unicode First {icon="translate"}
Arabic, Cyrillic, Greek, CJK and other scripts stay as they are, so slugs read naturally in their own language.

### ASCII on Demand {icon="globe"}
Transliterate to Latin-only slugs when you need them, with optional locale rules for languages such as German or Ukrainian.

### Explicit Policies {icon="shield-check"}
Symbols, emoji and Unicode numbers are handled on purpose, not by accident, with no invisible residue.
:::

:::info Quick Tip
`Slugify::make()` covers most needs. Reach for `Slugify::of()` when you want a fluent, immutable builder with `maxLength()`, per-slug rules, or a custom transliterator.
:::
