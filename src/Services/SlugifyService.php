<?php

namespace Pharaonic\Slugify\Services;

use voku\helper\ASCII;

class SlugifyService
{
    /**
     * The list of main slugify rules.
     *
     * @var array
     */
    protected $dictionary = [];

    /**
     * The list of extra slugify rules.
     *
     * @var array
     */
    protected $extra = [];

    public function __construct()
    {
        $this->dictionary = include __DIR__ . '/../resources/rules.php';
    }

    /**
     * Add or update a slugify rule.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function rule(string $key, string $value)
    {
        $this->extra[mb_strtolower($key, 'UTF-8')] = $value;
        $this->dictionary[mb_strtolower($key, 'UTF-8')] = $value;
    }

    /**
     * Generate a slug from the given value.
     *
     * @param  mixed   $value
     * @param  string  $separator
     * @param  boolean $ascii_only
     * @param  string  $ascii_lang
     * @return string
     */
    public function get($value, string $separator = '-', bool $ascii_only = false, string $ascii_lang = 'en')
    {
        if (empty($value)) {
            return '';
        }

        $value = mb_strtolower($this->handleAbbreviations($value), "UTF-8");

        if ($ascii_only) {
            $value = ASCII::to_ascii($value, $ascii_lang);
            $value = str_replace(array_keys($this->extra), $this->extra, $value);
        } else {
            $value = str_replace(array_keys($this->dictionary), $this->dictionary, $value);
        }
        return $this->prepareValue($value, $separator);
    }

    /**
     * Handle abbreviations in the given string.
     *
     * @param  string $value
     * @return string
     */
    private function handleAbbreviations(string $value)
    {
        preg_match_all('/(?<!\s)?[A-Z]+/m', substr($value, 1), $matches, PREG_OFFSET_CAPTURE);

        if (!empty($matches[0])) {
            foreach (array_reverse($matches[0]) as $match) {
                $value = substr_replace($value, ' ', $match[1] + 1, 0);
            }
        }

        return $value;
    }

    /**
     * Prepare the given value.
     *
     * @param  string $value
     * @param  string $separator
     * @return string
     */
    private function prepareValue(string $value, string $separator)
    {
        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';
        $value = preg_replace('!['.preg_quote($flip).']+!u', $separator, $value);

        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $value = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', ' ', $value);

        // Replace all separator characters and whitespace by a single separator
        $value = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $value);

        return trim($value, $separator);
    }
}
