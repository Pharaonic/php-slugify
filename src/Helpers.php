<?php

use Pharaonic\Slugify\Slugify;

/**
 * Get Slug from String
 *
 * @param string $string
 * @return string
 */
function slug(string $string = null)
{
    return Slugify::get($string);
}
