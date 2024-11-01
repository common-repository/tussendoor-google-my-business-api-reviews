<?php namespace Tussendoor\GmbReviews\Helpers;

use Tussendoor\GmbReviews\Plugin;

class Cookie
{

    public static $prefix;
    
    /**
     * On init of the plugin we call this method
     * Allways set the state cookie and populate the plugin cookies prefix
     */
    public static function init()
    {
        self::$prefix = Plugin::config('plugin.tag') . '_';
        self::setStateCookie();
    }
    
    /**
     * Setting a cookie
     *
     * @param  string $identifier Cookie identifier
     * @param  string $value      The value to be stored, cookies only accept strings. Arrays should be serialized
     * @param  int    $seconds    The expiration rate in seconds (1 hour = 3600)
     * @param  string $path       Cookie path to be saved
     * @return bool
     */
    public static function set(string $identifier, string $value, int $seconds, string $path = '/') : bool
    {
        $cookieName = self::$prefix . self::sanitizeIdentifier($identifier);
        $expireTime = time() + $seconds;
        
        return setcookie($cookieName, $value, $expireTime, $path);
    }
    
    /**
     * Get specific cookie
     *
     * @param  string $identifier
     * @return null|string
     */
    public static function get(string $identifier, $default = '') : ?string
    {
        $identifier = self::$prefix . self::sanitizeIdentifier($identifier);

        if (! isset($_COOKIE[$identifier])) return $default;

        return $_COOKIE[$identifier];
    }
    
    /**
     * Delete specific cookie
     *
     * @param  mixed $identifier
     * @return void
     */
    public static function delete(string $identifier) : void
    {   
        $identifier = self::$prefix . self::sanitizeIdentifier($identifier);

        unset($_COOKIE[$identifier]);
    }

    /**
     * Returns true if the parameter is defined.
     * @param  string $key The key
     * @return bool   true if the parameter exists, false otherwise
     */
    public static function has(string $identifier)
    {
        $identifier = self::$prefix . self::sanitizeIdentifier($identifier);
        return array_key_exists($identifier, $_COOKIE);
    }

    /**
     * When loading the plugin we set a cookie with a random number that is valid for 30min
     * We send this to Google in our request for access
     * If a different value returns then something malicious is going on and we will not save any credentials
     *
     * @return bool
     */
    public static function setStateCookie() : bool
    {   
        $cookieName = self::$prefix . 'state';
        $state      = bin2hex(random_bytes(128/8));
        $expireTime = time() + 3600;
        
        if (! isset($_COOKIE[$cookieName])) {
            return setcookie($cookieName, $state, $expireTime, '/');
        }

        return false;
    }
    
    /**
     * Sanitize identifier as string and remove all slashes
     *
     * @param  string $identifier
     * @return string
     */
    protected static function sanitizeIdentifier(string $identifier) : string
    {
        return str_replace('/', '',  htmlspecialchars($identifier, ENT_NOQUOTES));
    }

    /**
     * Returns the alphabetic characters of the parameter value.
     * @param  string $identifier     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public static function getString(string $identifier)
    {
        return sanitize_text_field(self::get($identifier));
    }
}