<?php

/*
 * Greek. The generic tables were case-dependent ("Χ" => "X", "χ" => "kh");
 * lowercasing before transliteration fixed that for every script. With "el",
 * portable-ascii follows modern pronunciation ("ει" => "i", "ου" => "u").
 * Not ELOT 743 ("psychi"), but readable and deterministic. Pharaonic override: no.
 */

return [
    [
        'input' => 'Αθήνα Θεσσαλονίκη',
        'expected_ascii' => 'athina-thessaloniki',
        'expected_unicode' => 'αθήνα-θεσσαλονίκη',
    ],
    ['input' => 'Ψυχή Χαρά', 'expected_ascii' => 'psikhi-khara'],
    ['input' => 'ΧΑΡΆ', 'expected_ascii' => 'khara'],
    ['input' => 'Γειά σου', 'expected_ascii' => 'ghia-su'],
];
