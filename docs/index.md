---
name: Slugify

action:
  label: View on Packagist
  href: "{package.packagistUrl}"

views: components.packages

breadcrumbs:
  - label: Home
    href: route:home
  - label: Packages
    href: route:packages.index
  - label: "{technology.name} Packages"
    href: "url:/packages/{technology.slug}"
  - label: "{package.name}"

card:
  topic: seo
  icon: globe
  tags: slug slugify url permalink unicode transliteration ascii arabic seo
  description: Framework-agnostic slug generation for PHP. Keep Unicode or transliterate to ASCII, use any separator, split camelCase and acronyms, and add your own replacement rules.

seo:
  title: "{package.fullName} - Unicode & ASCII Slug Generator for PHP"
  description: "{package.name} is a PHP package that turns any string into a URL-friendly slug, with Unicode support, ASCII transliteration, custom separators, and replacement rules. {package.downloadsShort}+ downloads, {package.license} licensed."
  keywords: php slug, slugify, php slugify, unicode slug, arabic slug, ascii transliteration, url slug, permalink php
  author: Pharaonic
  images:
    - "{package.cover}"
  openGraph:
    type: website
    siteName: Pharaonic
  twitter:
    card: summary_large_image

schema:
  "@type": SoftwareSourceCode
  name: "{package.name}"
  description: "{package.name} is a PHP package that turns any string into a URL-friendly slug, with Unicode support, ASCII transliteration, custom separators, and replacement rules."
  image: "{package.cover}"
  codeRepository: "{package.githubUrl}"
  programmingLanguage: PHP
  runtimePlatform: "{technology.name}"
  version: "{package.version}"
  datePublished: "{package.publishedAt}"
  dateModified: "{package.updatedAt}"
  license: "https://opensource.org/licenses/{package.license}"
  isAccessibleForFree: true
  sameAs:
    - "{package.githubUrl}"
    - "{package.packagistUrl}"
  author:
    "@id": url:/#organization
  publisher:
    "@id": url:/#organization
  interactionStatistic:
    "@type": InteractionCounter
    interactionType: https://schema.org/DownloadAction
    userInteractionCount: "{package.downloads}"
---
