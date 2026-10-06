<?php

/*
 * Turkish. Generic ASCII is correct. Pharaonic override: lowercasing only,
 * because dotless "I" lowercases to "ı" ("IŞIK" => "ışık", not "işik").
 */

return [
    [
        'input' => 'İstanbul Işık Çağrı',
        'expected_ascii' => 'istanbul-isik-cagri',
        'expected_unicode' => 'istanbul-ışık-çağrı',
    ],
    ['input' => 'IŞIK', 'expected_ascii' => 'isik', 'expected_unicode' => 'ışık'],
    ['input' => 'Şişli Güneş Öğretmen', 'expected_ascii' => 'sisli-gunes-ogretmen'],
    ['input' => 'IĞDIR', 'expected_ascii' => 'igdir', 'expected_unicode' => 'ığdır'],
];
