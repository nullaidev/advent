<?php
namespace Ezra\Essence\Http;

use Ezra\Essence\Structure\Data;

/**
 * WebInput
 *
 * A common sense API for web request input.
 */
class WebInput
{
    protected static string|false $body = false;
    protected static ?array $json = null;

    /**
     * Static Constructor
     *
     * Can not fetch multipart/form-data-encoded input.
     *
     * @link https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input
     */
    public static function __staticConstructor()
    {
        static::$body = file_get_contents('php://input');

        if(Data::isJson(static::$body)) {
            static::$json = json_decode($input, true, 512, JSON_BIGINT_AS_STRING);
        }
    }

    /**
     * Lookup Input
     *
     * Get data from input, json first then form, using dot notation with wilds.
     *
     * @param string|array $needle Value to check in dot notation, or an array of string values.
     * @param mixed $default Fallback if value is null.
     */
    public static function lookup(array|string $needle, mixed $default = null) : mixed
    {
        return Data::lookup($needle, static::$json ?? static::formData(), $default);
    }

    /**
     * Post Data
     *
     * The data from a request that is 'application/x-www-form-urlencoded' or
     * 'multipart/form-data'.
     */
    public static function formData() : array
    {
        return $_POST ?? [];
    }

    /**
     * Input Body
     */
    public static function body() : string|false
    {
        return static::$body;
    }

    /**
     * JSON Body
     *
     * The JSON body if there was one.
     */
    public static function json() : ?array
    {
        return static::$json;
    }
}

WebInput::__staticConstructor();