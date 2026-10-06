<?php

/*
 * Armenian. Generic omits the word-initial "Ե" => "ye" rule ("erevan", not
 * "yerevan"), still readable and deterministic. Candidate for a future override
 * if users report it. Pharaonic override: no.
 */

return [
    ['input' => 'Երևան Հայաստան', 'expected_ascii' => 'erevan-hayastan', 'expected_unicode' => 'երևան-հայաստան'],
];
