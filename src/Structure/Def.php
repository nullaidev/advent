<?php
namespace Ezra\Essence\Structure;

/**
 * Def
 *
 * A common sense API for mixed data types.
 */
class Def
{
    /**
     * Get
     *
     * Serves the same purpose as constant() but also provides a default value if
     * the constant is null or not defined.
     *
     * @param string $name The constant variable name.
     * @param mixed $default The default value.
     */
    public static function get(string $name, mixed $default = null) : mixed
    {
        return constant($name) ?? $default;
    }

    /**
     * Set
     *
     * @param string $name The name of the constant.
     * @param int|float|string|bool|null|array $value The value of the constant.
     */
    public static function set(string $name, int|float|string|bool|null|array $value)
    {
        return define($name, $value);
    }

    /**
     * Set If Not Defined
     *
     * Set a constant if it does not exists.
     *
     * @param string $name The name of the constant.
     * @param int|float|string|bool|null|array $value The value of the constant.
     */
    public static function setIfNotDefined(string $name, int|float|string|bool|null|array $value) : bool
    {
        return !constant($name) ? define($name, $value) : false;
    }
}