<?php

/*
 * Arabic diacritics (tashkeel) and the tatweel (kashida) are stripped so that
 * vocalized and unvocalized spellings of a word produce the same slug.
 */

return [
    "\u{064B}" => '', // fathatan
    "\u{064C}" => '', // dammatan
    "\u{064D}" => '', // kasratan
    "\u{064E}" => '', // fatha
    "\u{064F}" => '', // damma
    "\u{0650}" => '', // kasra
    "\u{0651}" => '', // shadda
    "\u{0652}" => '', // sukun
    "\u{0670}" => '', // superscript alef
    "\u{0640}" => '', // tatweel
];
