<?php

use Pharaonic\Slugify\Slugify;

if (!function_exists('slug')) {
    /**
     * Generate a slug from the given value.
     *
     * @param mixed       $value      Any scalar or Stringable value; null yields "".
     * @param string      $separator  Joins the words; may be empty.
     * @param bool        $ascii_only Transliterate the slug to ASCII.
     * @param string|null $ascii_lang Language hint for ASCII transliteration.
     */
    function slug(mixed $value, string $separator = '-', bool $ascii_only = false, ?string $ascii_lang = 'en'): string
    {
        return Slugify::get($value, $separator, $ascii_only, $ascii_lang);
    }
}
