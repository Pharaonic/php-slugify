<?php

/*
 * Japanese. Kana are romanized correctly. Kanji are read as Chinese
 * ("東京" => "dong-jing"): fixing that needs a dictionary, not a rule.
 * Pharaonic override: no.
 */

return [
    ['input' => 'こんにちは カタカナ', 'expected_ascii' => 'konnichiha-katakana', 'expected_unicode' => 'こんにちは-カタカナ'],
    ['input' => 'ｶﾀｶﾅ', 'expected_ascii' => 'katakana', 'expected_unicode' => 'カタカナ'],
];
