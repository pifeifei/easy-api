<?php

namespace Pff\EasyApi\Request;


use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use IteratorAggregate;
use function array_key_exists;
use function count;
use function is_array;
use const FILTER_DEFAULT;

class Parameters implements IteratorAggregate, Countable
{
    /**
     * Parameter storage.
     */
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @return array An int, float, boolean, string or array of parameters
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array $parameters
     * @return $this
     */
    public function replace(array $parameters = [])
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Adds parameters.
     *
     * @param array $parameters
     * @return $this
     */
    public function add(array $parameters = [])
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }

    /**
     * Returns a parameter by name.
     *
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->parameters, $key, $default);
    }

    /**
     * Sets a parameter by name.
     *
     * @param mixed $value The value
     */
    public function set($key, $value)
    {
        Arr::set($this->parameters, $key, $value);
        return $this;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has($key)
    {
        return Arr::has($this->parameters, $key);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param string|array $keys
     * @return $this
     */
    public function remove($keys)
    {
        Arr::forget($this->parameters, $keys);
        return $this;
    }

//    /**
//     * Returns the alphabetic characters of the parameter value.
//     *
//     * @return string The filtered value
//     */
//    public function getAlpha($key, $default = '')
//    {
//        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default));
//    }
//
//    /**
//     * Returns the alphabetic characters and digits of the parameter value.
//     *
//     * @return string The filtered value
//     */
//    public function getAlnum($key, $default = '')
//    {
//        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default));
//    }
//
//    /**
//     * Returns the digits of the parameter value.
//     *
//     * @return string The filtered value
//     */
//    public function getDigits($key, $default = '')
//    {
//        // we need to remove - and + because they're allowed in the filter
//        return str_replace(['-', '+'], '', $this->filter($key, $default, \FILTER_SANITIZE_NUMBER_INT));
//    }
//
//    /**
//     * Returns the parameter value converted to integer.
//     *
//     * @return int The filtered value
//     */
//    public function getInt($key, $default = 0)
//    {
//        return (int) $this->get($key, $default);
//    }
//
//    /**
//     * Returns the parameter value converted to boolean.
//     *
//     * @return bool The filtered value
//     */
//    public function getBoolean($key, $default = false)
//    {
//        return $this->filter($key, $default, \FILTER_VALIDATE_BOOLEAN);
//    }
//
//    /**
//     * Filter key.
//     *
//     * @param mixed $default Default = null
//     * @param int   $filter  FILTER_* constant
//     * @param mixed $options Filter options
//     *
//     * @see https://php.net/filter-var
//     *
//     * @return mixed
//     */
//    public function filter($key, $default = null, $filter = FILTER_DEFAULT, $options = [])
//    {
//        $value = $this->get($key, $default);
//
//        // Always turn $options into an array - this allows filter_var option shortcuts.
//        if (!is_array($options) && $options) {
//            $options = ['flags' => $options];
//        }
//
//        // Add a convenience check for arrays.
//        if (is_array($value) && !isset($options['flags'])) {
//            $options['flags'] = \FILTER_REQUIRE_ARRAY;
//        }
//
//        if ((\FILTER_CALLBACK & $filter) && !((isset($options['options']) ? $options['options'] : null) instanceof \Closure)) {
//            trigger_deprecation('symfony/http-foundation', '5.2', 'Not passing a Closure together with FILTER_CALLBACK to "%s()" is deprecated. Wrap your filter in a closure instead.', __METHOD__);
//        }
//
//        return filter_var($value, $filter, $options);
//    }

    /**
     * Returns an iterator for parameters.
     *
     * @return ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count()
    {
        return count($this->parameters);
    }

}
