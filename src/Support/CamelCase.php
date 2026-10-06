<?php

namespace Pharaonic\Slugify\Support;

/**
 * @internal
 */
final class CamelCase
{
    /**
     * Insert a space at camelCase and acronym boundaries.
     *
     *   helloWorld     -> hello World
     *   XMLHttpRequest -> XML Http Request
     *   getUserID      -> get User ID
     *   Version2Beta   -> Version2 Beta
     *   3D             -> 3D
     */
    public static function split(string $value): string
    {
        $result = preg_replace(
            [
                // lowercase letter followed by an uppercase letter: "helloWorld"
                '/(?<=\p{Ll})(?=\p{Lu})/u',
                // acronym or digit followed by a capitalised word: "XMLHttp", "Version2Beta" (but not "3D")
                '/(?<=[\p{Lu}\p{Nd}])(?=\p{Lu}\p{Ll})/u',
            ],
            ' ',
            $value
        );

        return $result ?? $value;
    }
}
