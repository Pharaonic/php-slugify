<?php

namespace Pharaonic\Slugify\Support;

use Normalizer;

/**
 * @internal
 */
final class Unicode
{
    /**
     * Replace invalid UTF-8 sequences and, when ext-intl is available,
     * compose the text to NFC so "e" + U+0301 and "é" are treated alike.
     */
    public static function normalize(string $value): string
    {
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = (string) mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        if (class_exists(Normalizer::class) && !Normalizer::isNormalized($value)) {
            $normalized = Normalizer::normalize($value);
            $value = is_string($normalized) ? $normalized : $value;
        }

        return $value;
    }
}
