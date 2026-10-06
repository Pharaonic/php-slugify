<?php

/*
 * Russian. Generic: "Щ" => "shch", "Х" => "kh", "Ё" => "io". With "ru",
 * portable-ascii's map uses "sch", "h", "yo". Both are established, readable
 * romanizations; neither fails. Pharaonic override: no.
 */

return [
    ['input' => 'Привет мир', 'expected_ascii' => 'privet-mir', 'expected_unicode' => 'привет-мир'],
    ['input' => 'Щука Жизнь Ёлка', 'expected_ascii' => 'schuka-zhizn-yolka'],
    ['input' => 'Хабаровск', 'expected_ascii' => 'habarovsk'],
];
