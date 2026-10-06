## Extending

### Custom Transliterator

ASCII transliteration goes through the `Pharaonic\Slugify\Contracts\Transliterator` interface. The default implementation is `PortableAsciiTransliterator`. Implement the interface to use another engine, such as ICU:

```php title="app/Support/IntlTransliterator.php"
namespace App\Support;

use Pharaonic\Slugify\Contracts\Transliterator;

final class IntlTransliterator implements Transliterator
{
    public function transliterate(string $value, ?string $language = null): string
    {
        return \Transliterator::create('Any-Latin; Latin-ASCII')->transliterate($value);
    }
}
```

Use it for every slug, or for a single one:

```php
use App\Support\IntlTransliterator;
use Pharaonic\Slugify\Slugify;

Slugify::useTransliterator(new IntlTransliterator());   // package-wide

Slugify::of('Привет')
    ->ascii()
    ->transliterator(new IntlTransliterator())          // this slug only
    ->toString();

Slugify::useTransliterator(null);                       // restore the default
```

The transliterator only runs in ASCII mode. In ASCII mode, anything it returns outside `[A-Za-z0-9]` is treated as a word break.

### Using `Slugger` Directly

`Slugger` can be built without the static entry point. In that case no default or package-wide rules are applied:

```php
use Pharaonic\Slugify\Rules\RuleSet;
use Pharaonic\Slugify\Slugger;

(new Slugger('user@host'))->toString();                          // "user-host"
(new Slugger('user@host', null, RuleSet::defaults()))->toString(); // "user-at-host"
```
