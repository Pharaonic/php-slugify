<?php

/*
 * Cross-language canonical replacements, applied in every mode.
 *
 * Only script-level marks and variants that never change which word is written
 * belong here: Arabic diacritics (tashkeel), the tatweel (kashida), the alef wasla
 * and Hebrew points, so the vocalized and unvocalized spellings of a word produce
 * the same slug.
 *
 * Language-specific transliteration does NOT belong here (see locales/).
 */

$marks = [
    "\u{064B}" => '', // Arabic fathatan
    "\u{064C}" => '', // Arabic dammatan
    "\u{064D}" => '', // Arabic kasratan
    "\u{064E}" => '', // Arabic fatha
    "\u{064F}" => '', // Arabic damma
    "\u{0650}" => '', // Arabic kasra
    "\u{0651}" => '', // Arabic shadda
    "\u{0652}" => '', // Arabic sukun
    "\u{0670}" => '', // Arabic superscript alef
    "\u{0640}" => '', // Arabic tatweel
    "\u{0671}" => "\u{0627}", // Arabic alef wasla => alef ("ٱلحمد" and "الحمد" are one word)
];

// Hebrew cantillation marks and points (niqqud), skipping the punctuation
// in between (maqaf U+05BE, paseq U+05C0, sof pasuq U+05C3, nun hafukha U+05C6).
foreach ([[0x0591, 0x05BD], [0x05BF, 0x05BF], [0x05C1, 0x05C2], [0x05C4, 0x05C5], [0x05C7, 0x05C7]] as [$from, $to]) {
    for ($codePoint = $from; $codePoint <= $to; $codePoint++) {
        $marks[(string) mb_chr($codePoint, 'UTF-8')] = '';
    }
}

return $marks;
