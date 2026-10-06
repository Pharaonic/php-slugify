:::badges
- PHP Package {color=blue}
- {release.label} {color=green}
- {package.license} License {color=purple}
:::

# Slugify

Fast, framework-agnostic slug generation for PHP. Turn any string into a URL-friendly slug. By default it keeps Unicode letters as they are. You can also transliterate to ASCII, use any separator, split camelCase words and acronyms, and plug in your own replacement rules.

:::features
### Unicode First {icon="translate"}
`مرحبا بالعالم` → `مرحبا-بالعالم`, `Привет мир` → `привет-мир`.

### ASCII on Demand {icon="globe"}
`Crème brûlée` → `creme-brulee`, with language hints such as `de`.

### Replacement Rules {icon="pencil"}
Package-wide or per slug, with no state leaking between calls.
:::

:::info Quick Tip
`Slugify::make()` covers most needs. Reach for `Slugify::of()` when you want a fluent, immutable builder with `maxLength()`, per-slug rules, or a custom transliterator.
:::
