<?php

namespace Pharaonic\Slugify\Contracts;

interface Transliterator
{
    /**
     * Convert the given text to ASCII.
     *
     * @param string      $value    UTF-8 text.
     * @param string|null $language Optional language hint (e.g. "de"); null means no preference.
     */
    public function transliterate(string $value, ?string $language = null): string;
}
