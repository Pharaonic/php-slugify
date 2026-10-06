<?php

/*
 * Serbian, Cyrillic and Latin. Generic: "Ш" => "sh", "Ђ" => "dj" but "Đ" => "d".
 * portable-ascii's own "sr" map is correct for both scripts once the locale
 * ("sr", "sr-Latn", "sr-Cyrl") reaches it. Pharaonic override: no.
 */

return [
    [
        'input' => 'Београд Ђорђе Џон',
        'expected_ascii' => 'beograd-djordje-dzon',
        'expected_unicode' => 'београд-ђорђе-џон',
    ],
    ['input' => 'Љубав Њега Шабац Ћуприја Жика', 'expected_ascii' => 'ljubav-njega-sabac-cuprija-zika'],
    ['input' => 'Đorđe Đoković Džon', 'expected_ascii' => 'djordje-djokovic-dzon'],
];
