<?php

namespace Pharaonic\Slugify\Tests\Fixtures;

use Pharaonic\Slugify\Contracts\Transliterator;

final class UppercaseTransliterator implements Transliterator
{
    public function transliterate(string $value, ?string $language = null): string
    {
        return strtoupper($value) . ($language !== null ? ' ' . $language : '');
    }
}
