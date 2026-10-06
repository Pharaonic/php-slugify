---
view: components.packages.features
variant: compact
badge: Key Features
title: Everything you need to build URL slugs
subtitle: One static call for everyday use, plus a fluent builder when you need more control.
items:
  - icon: translate
    title: Unicode First
    text: Arabic, Persian, Cyrillic, Greek, CJK and Indic scripts are kept as-is, and Arabic diacritics are stripped.
  - icon: globe
    title: ASCII Transliteration
    text: Call `->ascii()` or `->ascii('de')` for Latin-only slugs, powered by voku/portable-ascii.
  - icon: code-brackets
    title: Smart Word Splitting
    text: "`XMLHttpRequest` becomes `xml-http-request`, and `getUserID` becomes `get-user-id`."
  - icon: lines
    title: Any Separator
    text: Use `-`, `_`, `.`, `~`, an empty string, or your own. Repeats are collapsed automatically.
  - icon: pencil
    title: Replacement Rules
    text: Add package-wide rules with `Slugify::addRule()`, or rules for one slug only with `->rule()`.
  - icon: shield-check
    title: Backward Compatible
    text: "`slug()`, `Slugify::get()` and `Slugify::rule()` from 2.x keep working."
---
