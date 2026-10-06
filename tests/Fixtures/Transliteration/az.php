<?php

/*
 * Azerbaijani. Generic ASCII is correct ("ə" => "e"). Pharaonic override:
 * lowercasing only, as in Turkish ("IŞIQ" => "ışıq").
 */

return [
    [
        'input' => 'Azərbaycan Şəki Gəncə',
        'expected_ascii' => 'azerbaycan-seki-gence',
        'expected_unicode' => 'azərbaycan-şəki-gəncə',
    ],
    ['input' => 'Xırdalan İlqar Ağdam', 'expected_ascii' => 'xirdalan-ilqar-agdam'],
    ['input' => 'IŞIQ', 'expected_ascii' => 'isiq', 'expected_unicode' => 'ışıq'],
];
