<?php

namespace Pharaonic\Slugify\Transliteration;

use Pharaonic\Slugify\Contracts\Transliterator;
use voku\helper\ASCII;

/**
 * Transliterates through voku/portable-ascii, including its generic
 * fallback tables for scripts without a language map (CJK, Hebrew, ...).
 */
final class PortableAsciiTransliterator implements Transliterator
{
    public function transliterate(string $value, ?string $language = null): string
    {
        return ASCII::to_ascii($value, $language ?? ASCII::ENGLISH_LANGUAGE_CODE, true, false, true);
    }
}
