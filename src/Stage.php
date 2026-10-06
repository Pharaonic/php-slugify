<?php

namespace Pharaonic\Slugify;

/**
 * The slug pipeline stages, in order. The values are the keys returned by Slugger::explain().
 *
 * Emoji run before symbols: "©️" is an emoji, "©" is a symbol. Lowercasing runs
 * before transliteration so the result does not depend on the input's case
 * (the generic transliterator maps "Χ" to "X" but "χ" to "kh").
 *
 * @internal
 */
enum Stage: string
{
    case UnicodeNormalized = 'unicode_normalized';
    case CamelCaseSplit = 'camel_case_split';
    case CustomReplacements = 'custom_replacements';
    case NumbersNormalized = 'numbers_normalized';
    case EmojiProcessed = 'emoji_processed';
    case SymbolsProcessed = 'symbols_processed';
    case Lowercased = 'lowercased';
    case Transliterated = 'transliterated';
    case AsciiReplacements = 'ascii_replacements';
    case Filtered = 'filtered';
    case Final = 'final';
}
