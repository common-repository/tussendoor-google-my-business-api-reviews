<?php

namespace Tussendoor\GmbReviews\Helpers;

use Countable;
use ArrayIterator;
use IteratorAggregate;

class ParameterBag implements IteratorAggregate, Countable
{
    /**
     * Parameter storage.
     */
    protected $parameters;
    /**
     * @param array $parameters An array of parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
    /**
     * Returns the parameters.
     * @return array An array of parameters
     */
    public function all()
    {
        return $this->parameters;
    }
    /**
     * Returns the parameter keys.
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }
    /**
     * Replaces the current parameters by a new set.
     * @param array $parameters An array of parameters
     */
    public function replace(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
    /**
     * Adds parameters.
     * @param array $parameters An array of parameters
     */
    public function add(array $parameters = [])
    {
        $this->parameters = array_replace($this->parameters, $parameters);
    }
    /**
     * Returns a parameter by name.
     * @param  string $key     The key
     * @param  mixed  $default The default value if the parameter key does not exist
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }
    /**
     * Sets a parameter by name.
     * @param string $key   The key
     * @param mixed  $value The value
     */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }
    /**
     * Returns true if the parameter is defined.
     * @param  string $key The key
     * @return bool   true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }
    /**
     * Removes a parameter.
     * @param string $key The key
     */
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }
    /**
     * Returns the alphabetic characters of the parameter value.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getString($key, $default = '')
    {
        return sanitize_text_field($this->get($key, $default));
    }
    /**
     * Returns the alphabetic characters of the parameter value.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getTextarea($key, $default = '')
    {
        return sanitize_textarea_field($this->get($key, $default));
    }
    /**
     * Strips out all characters that are not allowable in an email.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string Filtered email address.
     */
    public function getEmail($key, $default = '')
    {
        return sanitize_email($this->get($key, $default));
    }
    /**
     * Sanitizes content for allowed HTML tags for post content.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string Filtered post data
     */
    public function getPost($key, $default = '')
    {
        return wp_kses_post($this->get($key, $default));
    }
    /**
     * Performs esc_url() for database or redirect usage.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The cleaned URL after esc_url() is run with the 'db' context.
     */
    public function getUrl($key, $default = '')
    {
        return sanitize_url($this->get($key, $default));
    }
    /**
     * Returns the alphabetic characters of the parameter value.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getAlpha($key, $default = '')
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
    }
    /**
     * Returns the alphabetic characters of the parameter value. With spaces
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getAlphaSpace($key, $default = '')
    {
        return preg_replace('/[^[:alpha:] ]/', '', $this->get($key, $default));
    }
    /**
     * Returns the alphabetic characters and digits of the parameter value.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getAlnum($key, $default = '')
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
    }
    /**
     * Returns the digits of the parameter value.
     * @param  string $key     The parameter key
     * @param  string $default The default value if the parameter key does not exist
     * @return string The filtered value
     */
    public function getDigits($key, $default = '')
    {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(['-', '+'], '', $this->filter($key, $default, FILTER_SANITIZE_NUMBER_INT));
    }
    /**
     * Returns the parameter value converted to integer.
     * @param  string $key     The parameter key
     * @param  int    $default The default value if the parameter key does not exist
     * @return int    The filtered value
     */
    public function getInt($key, $default = 0)
    {
        return (int) $this->get($key, $default);
    }
    /**
     * Returns the parameter value converted to boolean.
     * @param  string $key     The parameter key
     * @param  mixed  $default The default value if the parameter key does not exist
     * @return bool   The filtered value
     */
    public function getBoolean($key, $default = false)
    {
        return $this->filter($key, $default, FILTER_VALIDATE_BOOLEAN);
    }
    /**
     * Returns a boolean if the value is considered empty.
     * @param  string $key
     * @return bool
     */
    public function isEmpty($key)
    {
        $value = $this->has($key) ? $this->get($key) : null;

        return empty($value);
    }
    /**
     * Returns a boolean if the value is considered not empty.
     * @param  string $key
     * @return bool
     */
    public function isNotEmpty($key)
    {
        return $this->isEmpty($key) === false;
    }
    /**
     * Filter key.
     * @param string $key     Key
     * @param mixed  $default Default = null
     * @param int    $filter  FILTER_* constant
     * @param mixed  $options Filter options
     * @see http://php.net/manual/en/function.filter-var.php
     * @return mixed
     */
    public function filter($key, $default = null, $filter = FILTER_DEFAULT, $options = [])
    {
        $value = $this->get($key, $default);
        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }
        // Add a convenience check for arrays.
        if (\is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }

        return filter_var($value, $filter, $options);
    }
    /**
     * Returns an iterator for parameters.
     * @return ArrayIterator An ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }
    /**
     * Returns the number of parameters.
     * @return int The number of parameters
     */
    public function count()
    {
        return \count($this->parameters);
    }
}
