<?php

namespace Pharaonic\Slugify\Policies;

/**
 * How a SymbolPolicy replaces symbols.
 *
 * @internal
 */
enum SymbolMode
{
    case Remove;
    case Words;
    case Custom;
}
