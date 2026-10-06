<?php

/*
 * Arabic. Unvocalized script: any romanization is lossy. The generic result
 * is deterministic and readable. Pharaonic override: no.
 */

return [
    ['input' => 'مرحبا بالعالم', 'expected_ascii' => 'mrhba-balaaalm', 'expected_unicode' => 'مرحبا-بالعالم'],
    ['input' => 'مُحَمَّد', 'expected_ascii' => 'mhmd', 'expected_unicode' => 'محمد'],
    ['input' => 'ٱلْحَمْدُ لِلَّهِ', 'expected_ascii' => 'alhmd-llh', 'expected_unicode' => 'الحمد-لله'],
    ['input' => 'الإصدار ١٢', 'expected_ascii' => 'alasdar-12', 'expected_unicode' => 'الإصدار-12'],
];
