<?php

namespace Pharaonic\Slugify\Facades;

use Pharaonic\Slugify\Services\SlugifyService;

/**
 * @method static string get(string $value, string $separator = '-', bool $ascii_only = false, string $ascii_lang = 'en')
 * @method static void rule(string $key, string $value)
 */
class Slugify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SlugifyService::class;
    }
}
