<?php
namespace Ezra\Essence\Structure;

/**
 * Env
 *
 * A common sense API for environment variables.
 */
class Env
{
    /**
     * Get Environment Variable
     *
     * @link https://mattallan.me/posts/how-php-environment-variables-actually-work/
     *
     * @param string $name The environment variable name.
     * @param mixed $default The default value.
     * @param mixed $localOnly Excluded web server configuration.
     */
    public static function get(string $name, mixed $default = null, bool $localOnly = false) : mixed
    {
        return getenv($name, $localOnly) ?: ($_ENV[$name] ?? $default);
    }
}