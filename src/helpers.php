<?php

use Pharaonic\Slugify\Facades\Slugify;

/**
 * Get Slug from String
 *
 * @param mixed $value
 * @param string $separator
 * @param bool $ascii_only
 * @param string $ascii_lang
 * @return string
 */
function slug($value, string $separator = '-', bool $ascii_only = false, string $ascii_lang = 'en')
{
    return Slugify::get((string)$value, $separator, $ascii_only, $ascii_lang);
}
