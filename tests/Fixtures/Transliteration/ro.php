<?php

/*
 * Romanian, comma-below letters and the legacy cedilla forms.
 * Generic result is correct. Pharaonic override: no.
 */

return [
    ['input' => 'Țară Știință', 'expected_ascii' => 'tara-stiinta', 'expected_unicode' => 'țară-știință'],
    ['input' => 'Ţară Ştiinţă', 'expected_ascii' => 'tara-stiinta'],
    ['input' => 'Brașov București Iași', 'expected_ascii' => 'brasov-bucuresti-iasi'],
];
