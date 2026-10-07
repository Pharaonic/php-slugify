<?php

/*
 * voku/portable-ascii 1.x compatibility.
 *
 * Laravel 7 and 8 pin portable-ascii to ^1.x. These are the only letters whose
 * transliteration differs from portable-ascii 2.x (compared across every BMP
 * letter and every language map). With 1.x installed, they are replaced by the
 * 2.x result first, so a slug is the same whichever version is installed.
 *
 * "generic" applies when the language has no entry of its own for the letter
 * (1.x also turns "э" into "e'", which split words such as "Мэр" into "me-r").
 * The other keys are portable-ascii language codes.
 */

return [
    'generic' => [
        'ё' => 'e',
        'Ё' => 'E',
        'ъ' => 'ie',
        'Ъ' => 'Ie',
        'ы' => 'y',
        'Ы' => 'Y',
        'э' => 'e',
        'Э' => 'E',
        'ю' => 'iu',
        'Ю' => 'Iu',
        'я' => 'ia',
        'Я' => 'Ia',
        'پ' => 'p',
    ],

    'fa' => [
        'پ' => 'p',
        'ج' => 'j',
    ],

    'latin' => [
        'й' => 'i',
        'Й' => 'i',
        'щ' => 'shh',
        'Щ' => 'Shh',
    ],

    'uk' => [
        'г' => 'h',
        'Г' => 'H',
        'и' => 'y',
        'И' => 'Y',
        'й' => 'y',
        'Й' => 'Y',
        'х' => 'kh',
        'Х' => 'Kh',
        'ц' => 'ts',
        'Ц' => 'Ts',
        'ч' => 'ch',
        'Ч' => 'Ch',
        'ш' => 'sh',
        'Ш' => 'Sh',
        'щ' => 'shch',
        'Щ' => 'Shch',
    ],
];
