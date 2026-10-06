:::badges
- PHP Package {color=blue}
- {release.label} {color=green}
- {package.license} License {color=purple}
:::

# Slugify

Fast, framework-agnostic slug generation for PHP. Turn any string into a URL-friendly slug. By default it keeps Unicode letters as they are. ASCII is opt-in, locales only change language-specific behavior, and symbols, emoji and numbers each follow an explicit policy. Same input, same slug, every time.

:::features
### Unicode First {icon="translate"}
`مرحبا بالعالم` → `مرحبا-بالعالم`, `Привет мир` → `привет-мир`.

### ASCII on Demand {icon="globe"}
`Crème brûlée` → `creme-brulee`. Add a locale such as `de` or `uk` for language-specific results.

### Explicit Policies {icon="shield-check"}
Symbols, emoji and Unicode numbers are handled on purpose, not by accident: `الإصدار ١٢` → `الإصدار-12`.

### Replacement Rules {icon="pencil"}
Package-wide or per slug, with no state leaking between calls.
:::

:::info Quick Tip
`Slugify::make()` covers most needs. Reach for `Slugify::of()` when you want a fluent, immutable builder with `maxLength()`, per-slug rules, or a custom transliterator.
:::
