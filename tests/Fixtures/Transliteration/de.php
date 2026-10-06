<?php

/*
 * German. Generic: "ä" => "a" ("apfel"). portable-ascii's own "de" map gives
 * "ae"/"oe"/"ue", so passing the locale is enough. Pharaonic override: no.
 */

return [
    ['input' => 'Äpfel und Öl', 'expected_ascii' => 'aepfel-und-oel', 'expected_unicode' => 'äpfel-und-öl'],
    ['input' => 'Straße Größe Übermut', 'expected_ascii' => 'strasse-groesse-uebermut'],
    ['input' => 'GROẞE', 'expected_ascii' => 'grosse'],
    ['input' => 'München', 'expected_ascii' => 'muenchen'],
];
