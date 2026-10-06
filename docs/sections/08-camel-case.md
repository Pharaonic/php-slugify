## CamelCase & Acronyms

Words written in camelCase or PascalCase are split before slugging. Runs of capitals are kept together as acronyms.

```php
Slugify::make('helloWorld');        // "hello-world"
Slugify::make('HelloWorld');        // "hello-world"
Slugify::make('XMLHttpRequest');    // "xml-http-request"
Slugify::make('APIResponse');       // "api-response"
Slugify::make('getUserID');         // "get-user-id"
Slugify::make('PharaonicPHP');      // "pharaonic-php"
Slugify::make('Version2Beta');      // "version2-beta"
Slugify::make('3D Printing');       // "3d-printing"
Slugify::make('ÉtéÀParis');         // "été-à-paris"
```

The splitting rules are:

- A lowercase letter followed by an uppercase letter starts a new word (`helloWorld`).
- An uppercase letter or digit followed by a capitalized word starts a new word (`XMLHttp`, `2Beta`).
- A digit followed by a lone capital doesn't split (`3D`, `5G`).

Turn splitting off for a single slug with `splitCamelCase(false)`:

```php
Slugify::of('helloWorld')->splitCamelCase(false)->toString();   // "helloworld"
```
