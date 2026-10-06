## Fluent API

`Slugify::of()` returns a `Pharaonic\Slugify\Slugger`. The builder is immutable: every method returns a new instance, so you can configure one base builder and reuse it.

```php
use Pharaonic\Slugify\Slugify;

Slugify::of('Hello World')
    ->separator('_')
    ->lowercase()
    ->toString();                       // "hello_world"

(string) Slugify::of('Hello World');    // "hello-world"
```

### Reusing a Builder

```php
$base = Slugify::of('Hello World');

$base->separator('_')->toString();      // "hello_world"
$base->ascii()->toString();             // "hello-world"
$base->toString();                      // "hello-world" (unchanged)
```

### Limiting the Length

`maxLength()` limits the slug to a number of characters, cutting on a word boundary. A single word longer than the limit is truncated.

```php
Slugify::of('The quick brown fox jumps')->maxLength(15)->toString();   // "the-quick-brown"
Slugify::of('The quick brown fox jumps')->maxLength(14)->toString();   // "the-quick"
Slugify::of('Supercalifragilistic')->maxLength(5)->toString();         // "super"
```

### Keeping Case

```php
Slugify::of('Hello World')->lowercase(false)->toString();             // "Hello-World"
Slugify::of('Crème Brûlée')->lowercase(false)->ascii()->toString();   // "Creme-Brulee"
```

### Options Object

All options can be passed at once with `SlugOptions`. Named arguments keep it readable:

```php
use Pharaonic\Slugify\SlugOptions;

$options = new SlugOptions(separator: '_', ascii: true, language: 'de', maxLength: 60);

Slugify::of('München ist schön', $options)->toString();   // "muenchen_ist_schoen"
```

:::info Global Rules Are Captured
`Slugify::of()` captures the package-wide rules when it is called. Rules added later with `Slugify::addRule()` don't affect builders that already exist.
:::
