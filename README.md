<p align="center"><a href="https://pharaonic.io" target="_blank"><img src="https://raw.githubusercontent.com/Pharaonic/logos/main/php/slugify.jpg" width="470"></a></p>

<p align="center">
<a href="https://github.com/Pharaonic/php-slugify/actions/workflows/build.yml" target="_blank"><img src="https://github.com/Pharaonic/php-slugify/actions/workflows/build.yml/badge.svg?branch=main" alt="source"></a>
<a href="https://github.com/Pharaonic/php-slugify" target="_blank"><img src="http://img.shields.io/badge/source-pharaonic/php--slugify-blue.svg?style=flat-square" alt="Source"></a> <a href="https://packagist.org/packages/pharaonic/php-slugify" target="_blank"><img src="https://img.shields.io/packagist/v/pharaonic/php-slugify?style=flat-square" alt="Packagist Version"></a>
<img src="https://img.shields.io/packagist/dt/pharaonic/php-slugify?style=flat-square" alt="Packagist Downloads"> <img src="http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Source">
</p>


<h1 align="center">Simplest Slugify for PHP.</h1>


## Install

Slugify requires the Multibyte String `mbstring` extension from PHP.<br>
Install the latest version using [Composer](https://getcomposer.org/):

```bash
$ composer require pharaonic/php-slugify
```



## Usage

- [Get Slug](#GS)
- [Rules Manipulation](#RM)



<a name="GS"></a>

#### Get Slug

```php
use Pharaonic\Slugify\Slugify;
echo Slugify::get('Moamen Eltouny');

// OR
echo slug('Moamen Eltouny');
```





<a name="RM"></a>

#### Rules Manipulation

```php
use Pharaonic\Slugify\Slugify;
Slugify::rule('ö', 'oe');
```






## License

[MIT license](LICENSE.md)
