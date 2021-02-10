<?php
namespace Ezra\Essence\Structure;

use Ezra\Essence\Structure\Arr;

/**
 * Data
 *
 * A common sense API for mixed data types.
 */
class Data
{
    /**
     * Data Lookup
     *
     * Get data from an array or object using dot notation with wilds.
     *
     * @param string|array $needle Value to check in dot notation, or an array of string values.
     * @param array|object|\ArrayAccess $haystack Data to search.
     * @param mixed $default Fallback if value is null.
     */
    public static function lookup(array|string $needle, array|object|\ArrayAccess $haystack, mixed $default = null) : mixed
    {
        if( empty($needle) ) {
            return $haystack ?? $default;
        }

        $search = is_array($needle) ? $needle : explode('.', $needle);

        foreach($search as $i => $index) {
            unset($search[$i]);

            if($index === '*') {
                if(!is_iterable($haystack)) {
                    return $default;
                }

                $list = [];

                foreach ($haystack as $stack) {
                    $list[] = static::dotGet($search, $stack, $default);
                }

                return $list;
            }

            if(Arr::isAccessible($haystack) && isset($haystack[$index])) {
                $haystack = $haystack[$index];
            }
            elseif(is_object($haystack) && isset($haystack->{$index})) {
                $haystack = $haystack->{$index};
            }
            else {
                return $default;
            }
        }

        return $haystack ?? $default;
    }

    /**
     * Value Is "blank"
     *
     * This function treats some values differently than you might expect.
     *
     * Not blank: 0
     * Is blank: ' '
     *
     * @param mixed $var The variable being evaluated.
     */
    public static function isBlank(mixed $var) : bool
    {
        if (is_null($var)) {
            return true;
        }
        elseif (is_string($var)) {
            return trim($var) === '';
        }
        elseif (is_numeric($var) || is_bool($var)) {
            return false;
        }
        elseif ($var instanceof Countable) {
            return count($var) === 0;
        }

        return empty($var);
    }

    /**
     * Value Is "filled"
     *
     * This function treats some values differently than you might expect.
     *
     * Is filled: 0
     * Not filled: ' '
     *
     * @param mixed $var The variable being evaluated.
     */
    public static function isFilled(mixed $var) : bool
    {
        return !static::isBlank($var);
    }

    /**
     * Value Is JSON
     *
     * Is JSON encoded value.
     *
     * @param mixed $value The value to check.
     */
    public static function isJson(mixed $value) : bool
    {
        if(!is_string($value)) {
            return false;
        }

        if (trim($value) === '') {
            return false;
        }

        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}