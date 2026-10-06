---
view: components.packages.features
variant: compact
badge: Key Features
title: Everything you need to build URL slugs
subtitle: One static call for everyday use, plus a fluent builder when you need more control.
items:
  - icon: translate
    title: Unicode First
    text: Arabic, Persian, Cyrillic, Greek, CJK and Indic scripts are kept as-is, with deterministic Unicode normalization.
  - icon: globe
    title: ASCII on Request
    text: Call `->ascii()` for Latin-only slugs. A locale such as `de` or `uk` adds language-specific rules, but never forces ASCII.
  - icon: shield-check
    title: Explicit Policies
    text: Symbols, emoji and Unicode digits each follow a deliberate, configurable policy, with no invisible residue.
  - icon: code-brackets
    title: Smart Word Splitting
    text: "`XMLHttpRequest` becomes `xml-http-request`, and `getUserID` becomes `get-user-id`."
  - icon: lines
    title: Any Separator
    text: Use `-`, `_`, `.`, `~`, an empty string, or your own. Repeats are collapsed automatically.
  - icon: pencil
    title: Replacement Rules
    text: Add package-wide rules with `Slugify::addRule()`, or rules for one slug only with `->rule()`.
---
