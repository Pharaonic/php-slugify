<?php

/*
 * Symbol replacements applied by default.
 *
 * Kept deliberately small: symbols are language-specific, so only the
 * historical "@" => "at" rule is shipped. Add or override rules with
 * Slugify::addRule() / Slugify::removeRule().
 */

return [
    '@' => ' at ',
];
