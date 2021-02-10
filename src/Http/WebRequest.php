<?php
namespace Ezra\Essence\Http;

use Ezra\Essence\Http\WebInput;

/**
 * WebRequest
 *
 * This class provides access to common web request information.
 *
 * @link https://www.php.net/manual/en/reserved.variables.server.php
 */
class WebRequest
{
    protected static ?string $path;
    protected static ?bool $secure;

    /**
     * Static Constructor
     */
    public static function __staticConstructor()
    {
        static::$path = $_SERVER['REQUEST_URI'] ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : null;
        static::$secure = static::isHttps();
    }

    /**
     * Method
     *
     * Valid possibilities: GET, HEAD, POST, PUT, DELETE, CONNECT, OPTIONS,
     * PATCH, and TRACE.
     *
     * @link https://tools.ietf.org/html/rfc7231#section-4
     * @link https://tools.ietf.org/html/rfc5789#section-2
     */
    public static function method() : ?string
    {
        return $_SERVER['REQUEST_METHOD'] ?? null;
    }

    /**
     * Host
     *
     * The host name will incude the port number if the request contiants it
     * withing the URI explicitly.
     */
    public static function host() : ?string
    {
        return $_SERVER['HTTP_HOST'] ?? null;
    }

    /**
     * URI
     */
    public static function uri() : ?string
    {
        return $_SERVER['REQUEST_URI'] ?? null;
    }

    /**
     * Path
     */
    public static function path() : ?string
    {
        return static::$path;
    }

    /**
     * Query
     *
     * The data from the $_GET super global.
     */
    public static function query() : array
    {
        return $_GET ?? [];
    }

    /**
     * Get Form Input
     */
    public function formData() : array
    {
        return WebInput::formData();
    }

    /**
     * Body
     */
    public function body() : string|false
    {
        return WebInput::body();
    }

    /**
     * Lookup Input
     *
     * Get data from input, json first then form, using dot notation with wilds.
     *
     * @param string|array $needle Value to check in dot notation, or an array of string values.
     * @param mixed $default Fallback if value is null.
     */
    public static function inputLookup(array|string $needle, mixed $default = null) : mixed
    {
        return WebInput::lookup($needle, $default);
    }

    /**
     * Full URL
     */
    public static function url() : string
    {
        return (static::isHttps() ? 'https' : 'http').'://'.static::host().static::uri();
    }

    /**
     * Auth Username & Passowrd
     */
    public static function auth() : array
    {
        return [
            'user' => $_SERVER['PHP_AUTH_USER'],
            'pass' => $_SERVER['PHP_AUTH_PW'],
        ];
    }

    /**
     * User Agent
     */
    public static function userAganet() : ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Time Spent Since Request Started
     *
     * @param bool $milliseconds Get the run time in milliseconds.
     */
    public static function requestTimeSpent(bool $milliseconds = true) : int
    {
        $run = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        if( $milliseconds ) { return $run * 1000; }
        return $run;
    }

    /**
     * Server Port
     *
     * Typically port 80 or 443.
     */
    public static function port() : ?int
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Is HTTPS
     *
     * The X-Forwarded-Proto header is not checked because it can be spoofed
     * making it an insecure header to check. It is best to mark a request
     * as secure at the server level.
     *
     * @link https://core.trac.wordpress.org/ticket/31288
     */
    public static function isHttps() : bool
    {
        if(is_bool(static::$secure)) {
            return static::$secure;
        }

        static::$secure = false;

        if ( isset( $_SERVER['HTTPS'] ) ) {
            if ( 'on' === strtolower( $_SERVER['HTTPS'] ) ) {
                static::$secure = true;
            }

            if ( '1' == $_SERVER['HTTPS'] ) {
                static::$secure = true;
            }
        } elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
            static::$secure = true;
        }

        return static::$secure;
    }

    /**
     * Forward Protocal
     */
    public static function forwardProtocal() : ?string
    {
        return $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
    }

    /**
     * Http Referer
     *
     * Get the request referer.
     */
    public static function referer() : ?string
    {
        return $_SERVER['HTTP_REFERER'] ?? null;
    }

    /**
     * Remote IP
     *
     * Do not trust a 'HTTP_X_FORWARDED_FOR' IP address.
     *
     * @return string|null
     */
    public static function ip() : ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * Script File
     *
     * Get the entry point PHP script file name.
     */
    public static function scriptFile() : ?string
    {
        return $_SERVER['PHP_SELF'] ?? null;
    }

    /**
     * HTTPS Plea
     *
     * Is there a plea for an HTTPS response? Check the custom header
     * 'HTTP_X_FORWARDED_PROTO'.
     */
    public static function httpsPlea() : bool
    {
        if ( static::isHttps() || static::forwardProtocal() === 'https' ) {
            return true;
        }

        return false;
    }

    /**
     * Ajax Plea
     *
     * Is there a plea for a custom AJAX response? Check the custom header
     * 'HTTP_X_REQUESTED_WITH'.
     */
    public static function ajaxPlea() : bool
    {
        $ajax = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;

        if($ajax && strtolower($ajax) === 'xmlhttprequest' ) {
            return true;
        }

        return false;
    }

    /**
     * Method Plea
     *
     * Is there a plea for a custom method response? Check the the HTML form's
     * $_POST request data for '__method_plea'.
     */
    public static function methodPlea() : ?string
    {
        return $_POST['__method_plea'] ?? static::method();
    }
}

WebRequest::__staticConstructor();