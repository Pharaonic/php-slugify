## Separators

The second argument of `make()`, or `separator()` on the builder, sets the string that joins words. The default is `-`.

```php
Slugify::make('Hello World', '-');   // "hello-world"
Slugify::make('Hello World', '_');   // "hello_world"
Slugify::make('Hello World', '.');   // "hello.world"
Slugify::make('Hello World', '~');   // "hello~world"
Slugify::make('Hello World', '');    // "helloworld"
```

Dashes, underscores, dots and other punctuation already in the input are treated as word breaks. Repeats are collapsed, and the slug never starts or ends with a separator:

```php
Slugify::make('-hello__big.. ~world-', '_');   // "hello_big_world"
Slugify::make('hello---world');                // "hello-world"
Slugify::make('---');                          // ""
```

:::warning Invalid Separators
A separator must not contain letters, numbers, combining marks or whitespace. For example, `'x'`, `'1'` and `' '` throw `Pharaonic\Slugify\Exceptions\InvalidArgumentException`.
:::
