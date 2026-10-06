## Examples

### 1. Blog Post Permalinks

Generate an SEO-friendly, length-limited permalink from a post title:

```php title="app/Posts/PermalinkGenerator.php"
use Pharaonic\Slugify\Slugify;

final class PermalinkGenerator
{
    public function forTitle(string $title): string
    {
        return Slugify::of($title)->maxLength(60)->toString();
    }
}

(new PermalinkGenerator())->forTitle('10 Tips for Writing Clean PHP Code in 2026');
// "10-tips-for-writing-clean-php-code-in-2026"
```

### 2. Multilingual Site

Keep native-script slugs for Arabic content, and use ASCII slugs with the locale for German:

```php
use Pharaonic\Slugify\Slugify;

$slug = match ($locale) {
    'ar', 'fa' => Slugify::make($title),
    'de'       => Slugify::make($title, '-', true, 'de'),
    default    => Slugify::make($title, '-', true),
};

// ar: "دليل-البرمجة"     from "دليل البرمجة"
// de: "schoene-gruesse"  from "Schöne Grüße"
```

### 3. Domain-specific Rules

Register package-wide rules once at boot, then use per-slug rules for one-off cases:

- ===Service Provider

  ```php title="app/Providers/AppServiceProvider.php"
  use Pharaonic\Slugify\Slugify;

  public function boot(): void
  {
      Slugify::addRules([
          'c++' => 'cpp',
          'c#'  => 'csharp',
          '&'   => ' and ',
      ]);
  }
  ```

- ===Usage

  ```php title="app/Http/Controllers/CourseController.php"
  use Pharaonic\Slugify\Slugify;

  Slugify::make('C++ & C# Fundamentals');   // "cpp-and-csharp-fundamentals"

  Slugify::of('Price: 100%')
      ->rule('%', ' percent ')
      ->toString();                         // "price-100-percent"
  ```

### 4. File Names and Keys

Use another separator for cache keys or file names:

```php
use Pharaonic\Slugify\Slugify;

Slugify::make('Quarterly Report Q3', '_') . '.pdf';   // "quarterly_report_q3.pdf"
Slugify::make('UserProfileSettings', '.');            // "user.profile.settings"
```

### 5. Reusable Configuration

Build one configured builder and reuse it for every value:

```php
use Pharaonic\Slugify\SlugOptions;
use Pharaonic\Slugify\Slugify;

$options = new SlugOptions(separator: '_', ascii: true, maxLength: 32);

$keys = array_map(
    fn (string $name) => Slugify::of($name, $options)->toString(),
    ['Café Latte', 'Crème Brûlée', 'Pain au Chocolat']
);
// ["cafe_latte", "creme_brulee", "pain_au_chocolat"]
```
