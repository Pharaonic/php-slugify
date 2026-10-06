<?php

namespace Pharaonic\Slugify;

use Pharaonic\Slugify\Exceptions\InvalidArgumentException;

/**
 * Plain options bag describing how a slug is generated.
 */
final class SlugOptions
{
    /**
     * @param string      $separator      Joins the words of the slug. May be empty.
     * @param bool        $lowercase      Lowercase the result (multibyte aware).
     * @param bool        $ascii          Transliterate the result to ASCII.
     * @param string|null $language       Language hint for ASCII transliteration (e.g. "de").
     * @param bool        $splitCamelCase Split "camelCase" and "PascalCase" words.
     * @param int|null    $maxLength      Maximum length in characters, or null for no limit.
     */
    public function __construct(
        public string $separator = '-',
        public bool $lowercase = true,
        public bool $ascii = false,
        public ?string $language = null,
        public bool $splitCamelCase = true,
        public ?int $maxLength = null
    ) {
    }

    /**
     * Ensure the options can produce a well-formed slug.
     *
     * @throws InvalidArgumentException
     */
    public function validate(): void
    {
        if (preg_match('/[\pL\pN\pM\s]/u', $this->separator) !== 0) {
            throw InvalidArgumentException::separator($this->separator);
        }

        if ($this->maxLength !== null && $this->maxLength < 1) {
            throw InvalidArgumentException::maxLength($this->maxLength);
        }
    }
}
