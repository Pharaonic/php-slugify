<?php

/*
 * Azerbaijani.
 *
 * Like Turkish, Azerbaijani has dotted (İ/i) and dotless (I/ı) "i" letters,
 * so "IŞIQ" lowercases to "ışıq", not "işiq".
 *
 * ASCII transliteration needs no override: the generic result is correct
 * (see tests/Fixtures/Transliteration/az.php).
 */

return [
    'lowercase' => [
        'I' => 'ı',
        'İ' => 'i',
    ],
];
