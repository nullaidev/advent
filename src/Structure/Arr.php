<?php
namespace Ezra\Essence\Structure;

use ArrayAccess;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;

/**
 * Arr
 *
 * A common sense API for arrays.
 */
class Arr
{
    /**
     * Is Array Accessible
     *
     * @param mixed $var The variable being evaluated.
     */
    public static function isAccessible(mixed $var) : bool
    {
        return is_array($var) || $var instanceof ArrayAccess;
    }

    /**
     * Array Partition
     *
     * Partition and spread array semi-evenly across a number of groups.
     *
     * @param array $array The array being evaluated.
     * @param int $groups The number of groups.
     */
    public static function partition(array $array, int $groups) : array
    {
        $count = count( $array );
        $parts = floor( $count / $groups );
        $rem = $count % $groups;
        $partition = [];
        $mark = 0;
        for ($index = 0; $index < $groups; $index++)
        {
            $incr = ($index < $rem) ? $parts + 1 : $parts;
            $partition[$index] = array_slice( $array, $mark, $incr );
            $mark += $incr;
        }
        return $partition;
    }

    /**
     * Array Strict Lookup
     *
     * Strictly get a value from an array using dot notation without wilds.
     *
     * @param string|array $needle Value to check in dot notation, or an array of string values.
     * @param array|ArrayAccess $haystack Array to search.
     * @param mixed $default Fallback if value is null.
     */
    public static function lookupStrict(string|array $needle, array|ArrayAccess $haystack, mixed $default = null) : mixed
    {
        $search = is_array($needle) ? $needle : explode('.', $needle);

        foreach ($search as $index) {
            if(isset($haystack[$index])) {
                $haystack = $haystack[$index];
            }
            else {
                return $default;
            }
        }

        return $haystack ?? $default;
    }

    /**
     * Array Dot
     *
     * Flatten array dimensions to one level and meld keys into dot notation.
     * Resolves a deeply nested array to ['key.child' => 'value'].
     *
     * @param array $array the array to compress into dot notation.
     */
    public static function dotFlatten(array $array) : array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
        $result = [];
        foreach ($iterator as $value) {
            $keys = [];
            $depth = range(0, $iterator->getDepth());
            foreach ($depth as $step) {
                $keys[] = $iterator->getSubIterator($step)->key();
            }
            $result[ implode('.', $keys) ] = $value;
        }

        return $result;
    }
}