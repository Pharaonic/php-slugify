<?php

namespace Pharaonic\Slugify\Exceptions;

final class InvalidArgumentException extends \InvalidArgumentException
{
    public static function separator(string $separator): self
    {
        return new self(sprintf(
            'The slug separator [%s] is invalid: it must not contain letters, numbers, marks or whitespace.',
            $separator
        ));
    }

    public static function maxLength(int $maxLength): self
    {
        return new self(sprintf('The slug max length must be at least 1, [%d] given.', $maxLength));
    }

    public static function emptyRule(): self
    {
        return new self('A slug rule must have a non-empty search string.');
    }

    /**
     * @param mixed $value
     */
    public static function unsupportedValue($value): self
    {
        return new self(sprintf('Cannot generate a slug from a value of type [%s].', get_debug_type($value)));
    }
}
