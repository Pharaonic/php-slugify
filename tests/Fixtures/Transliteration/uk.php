<?php

/*
 * Ukrainian, official national transliteration (KMU Resolution No. 55, 2010).
 *
 * Generic (even with "uk"): "ж" => "z" ("Їжак" => "yizak", "Запоріжжя" => "zaporizzia"),
 * no word-initial forms ("Київ" => "kyyiv", "Юрій" => "iuriy"). Pharaonic override: yes.
 */

return [
    ['input' => 'Київ Україна', 'expected_ascii' => 'kyiv-ukraina', 'expected_unicode' => 'київ-україна'],
    ['input' => 'Щастя Ґанок Їжак Євген', 'expected_ascii' => 'shchastia-ganok-yizhak-yevhen'],
    ['input' => 'Харків Запоріжжя Гоголь', 'expected_ascii' => 'kharkiv-zaporizhzhia-hohol'],
    ['input' => 'Юрій Яготин Зайці', 'expected_ascii' => 'yurii-yahotyn-zaitsi'],
    ['input' => "Згорани Знам'янка Знамʼянка", 'expected_ascii' => 'zghorany-znamianka-znamianka'],
];
