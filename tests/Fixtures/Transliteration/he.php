<?php

/*
 * Hebrew. Points (niqqud) are removed in every mode, so vocalized and
 * unvocalized text give the same slug. Generic consonantal output is lossy but
 * deterministic. Pharaonic override: no.
 */

return [
    ['input' => 'שלום עולם', 'expected_ascii' => 'shlvm-vlm', 'expected_unicode' => 'שלום-עולם'],
    ['input' => 'שָׁלוֹם', 'expected_ascii' => 'shlvm', 'expected_unicode' => 'שלום'],
];
