<?php

/*
 * Persian. Like Arabic, lossy by nature; generic result is deterministic.
 * The ZWNJ inside a word is removed, never a word boundary. Pharaonic override: no.
 */

return [
    ['input' => 'سلام دنیا', 'expected_ascii' => 'slam-dnya', 'expected_unicode' => 'سلام-دنیا'],
    ['input' => 'پژوهش', 'expected_ascii' => 'pzhohsh'],
    ['input' => "می\u{200C}خواهم ۱۲", 'expected_ascii' => 'mykhoahm-12', 'expected_unicode' => 'میخواهم-12'],
];
