## Installation

Install the package with Composer. There is nothing to register or publish.

### Requirements

- PHP 8.0.x (each `8.x` release line targets the matching PHP version)
- `ext-mbstring`
- `voku/portable-ascii` ^2.0 (installed automatically)
- `ext-intl` *(optional)*: normalizes decomposed Unicode input (NFC), so `e` + U+0301 and `é` produce the same slug.

### Composer Installation

```bash title="Terminal" no-line-numbers
composer require pharaonic/php-slugify
```

Composer autoloads the `Pharaonic\Slugify` namespace and the global `slug()` helper.

:::success Installation Complete
You're all set! Try `Slugify::make('Hello World')`, which returns `hello-world`.
:::
