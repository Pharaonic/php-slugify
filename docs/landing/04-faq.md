---
view: components.home.faq
badge: FAQ
title: "{package.name}"
highlight: Questions
subtitle: "Quick answers about installing and using {package.name}."
---

## What is {package.name}?

{card.description} It's a free, open-source {technology.name} package by Pharaonic.

## How do I install {package.name}?

Run `composer require {package.composer}` in your project's root directory.

## What does {package.name} require?

The latest release requires {package.requiresText}.

## Does {package.name} support Arabic and other non-Latin scripts?

Yes. By default, slugs keep Unicode letters, so `مرحبا بالعالم` becomes `مرحبا-بالعالم`. Pass `true` as the third argument to `Slugify::make()` to transliterate to ASCII instead.

## Does it depend on Laravel or another framework?

No. {package.name} is plain PHP with no framework, container or config files. For Eloquent models, use the companion package `pharaonic/laravel-sluggable`.

## How do I customize how characters are replaced?

Add package-wide rules with `Slugify::addRule('&', ' and ')`, or rules for one slug only with `Slugify::of($title)->rule('&', ' and ')`.

## Is {package.name} free to use?

Yes. {package.name} is open source under the {package.license} license, so you can use it in personal and commercial projects.

## Where can I find the {package.name} documentation?

Read the [{package.name} documentation]({package.docsUrl}) for setup and usage examples.

## How do I report a bug or contribute to {package.name}?

Open an issue or a pull request on [GitHub]({package.githubUrl}), or ask in the [Pharaonic Discord](https://discord.gg/XQG9RhvEvf).
