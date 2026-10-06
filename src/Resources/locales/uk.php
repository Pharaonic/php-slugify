<?php

/*
 * Ukrainian: the official national transliteration
 * (Resolution of the Cabinet of Ministers of Ukraine No. 55, 2010).
 *
 * The generic transliterator maps "ж" to "z" and ignores the positional rules
 * ("Київ" => "kyyiv", "Їжак" => "yizak"); see tests/Fixtures/Transliteration/uk.php.
 *
 * Keys are lowercase; the case of the source letter is carried over.
 */

return [
    // At the start of a word.
    'ascii_word_initial' => [
        'є' => 'ye',
        'ї' => 'yi',
        'й' => 'y',
        'ю' => 'yu',
        'я' => 'ya',
    ],

    'ascii' => [
        'зг' => 'zgh', // keeps "зг" apart from "ж" (Згорани => Zghorany)
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'h',
        'ґ' => 'g',
        'д' => 'd',
        'е' => 'e',
        'є' => 'ie',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'y',
        'і' => 'i',
        'ї' => 'i',
        'й' => 'i',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'kh',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shch',
        'ь' => '',
        'ю' => 'iu',
        'я' => 'ia',
        // The apostrophe is not transliterated (Знам'янка => Znamianka).
        "'" => '',
        '’' => '',
        'ʼ' => '',
    ],
];
