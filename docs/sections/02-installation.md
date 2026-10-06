## Installation

Install the package with Composer. There is nothing to register or publish.

### Requirements

- PHP 8.0.x (each `8.x` release line targets the matching PHP version)
- `ext-mbstring`
- `voku/portable-ascii` ^2.0 and `symfony/polyfill-intl-normalizer` (installed automatically)
- `ext-intl` *(optional)*: Unicode normalization works without it, but the native extension is faster than the polyfill.

### Composer Installation

```bash title="Terminal" no-line-numbers
composer require pharaonic/php-slugify
```

Composer autoloads the `Pharaonic\Slugify` namespace and the global `slug()` helper.

:::success Installation Complete
You're all set! Try `Slugify::make('Hello World')`, which returns `hello-world`.
:::
