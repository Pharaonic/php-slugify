<?php

/*
 * Turkish.
 *
 * Turkish has two distinct "i" letters: dotted (İ/i) and dotless (I/ı), so
 * "IŞIK" lowercases to "ışık", not "işik". Generic lowercasing gets this wrong.
 *
 * ASCII transliteration needs no override: the generic result is correct
 * (see tests/Fixtures/Transliteration/tr.php).
 */

return [
    'lowercase' => [
        'I' => 'ı',
        'İ' => 'i',
    ],
];
