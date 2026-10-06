<?php

/*
 * Chinese. Generic gives toneless pinyin, one word per character.
 * Pharaonic override: no.
 */

return [
    ['input' => '你好世界', 'expected_ascii' => 'ni-hao-shi-jie', 'expected_unicode' => '你好世界'],
    ['input' => '北京', 'expected_ascii' => 'bei-jing'],
];
