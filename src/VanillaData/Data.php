<?php

namespace Rentalhost\VanillaData;

use ArrayAccess;
use ArrayIterator;
use Countable;

/**
 * Class Data
 * @package Rentalhost\VanillaData
 */
class Data implements Countable, ArrayAccess
{
    /**
     * Stores array data.
     * @var array
     */
    private $data;

    /**
     * Construct the data controller.
     *
     * @param array $data Data array.
     *
     * @throws Exception\InvalidDataTypeException If type of data is invalid.
     */
    public function __construct($data = null)
    {
        $this->reconfigureArray($data);
    }

    /**
     * Clear internals data.
     */
    public function clear()
    {
        $this->data = [ ];
    }

    /**
     * Returns internals data.
     *
     * @param  mixed $key          Key name.
     * @param  mixed $defaultValue Default value.
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        // Returns if keys is not scalar, or is boolean, or is not defined.
        if (!is_scalar($key) || is_bool($key) || !$this->has($key)) {
            return $defaultValue;
        }

        // Returns internals data.
        return $this->data[$key];
    }

    /**
     * Returns internals data protected to HTML.
     * It'll protect only if is a string, else, will return original value.
     *
     * @param  string $key          Key name.
     * @param  mixed  $defaultValue Default value.
     *
     * @return mixed
     */
    public function getHTML($key, $defaultValue = null)
    {
        $data = $this->get($key, $defaultValue);

        // If it is a string, returns protected.
        if (is_string($data)) {
            return htmlspecialchars($data);
        }

        return $data;
    }

    /**
     * Returns a self instance over key value.
     *
     * @param  string      $key          Key name.
     * @param  array|false $defaultValue Alternative value, if key is not an array.
     *
     * @return self|false
     */
    public function getSelf($key, $defaultValue = null)
    {
        $data = $this->get($key);

        // If it is a Data instance, returns itself.
        if ($data instanceof self) {
            return $data;
        }

        // If it is not an array, will returns default value.
        // If default value is false, so will returns false.
        if (!is_array($data)) {
            if ($defaultValue === false) {
                return false;
            }

            return new self($defaultValue);
        }

        // Else, returns a self instance filled.
        return new self($data);
    }

    /**
     * Returns internals array data.
     * @return array
     */
    public function getArray()
    {
        return $this->data;
    }

    /**
     * Returns an array iterator.
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Set a key value.
     *
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Extend all arguments passed one over the other.
     *
     * @param Data|array ...$args Arguments.
     *
     * @return self
     */
    public static function extend()
    {
        $data = new self;

        foreach (func_get_args() as $arg) {
            $data->setArray($arg);
        }

        return $data;
    }

    /**
     * Set an array or self to internal array.
     *
     * @param array|self|mixed $data      Array or self instance.
     * @param boolean          $overwrite When true, will replace existent keys.
     *
     * @throws Exception\InvalidDataTypeException If type of data is invalid.
     */
    public function setArray($data, $overwrite = true)
    {
        if (!$data) {
            return;
        }

        // Validate data.
        if (!is_array($data)
            && !$data instanceof self
        ) {
            throw new Exception\InvalidDataTypeException('invalid data type');
        }

        // If is instance of self, copy array.
        if ($data instanceof self) {
            $data = $data->data;
        }

        // Avoid overwrite.
        if ($overwrite === false) {
            $data = array_diff_key($data, array_flip(array_keys($this->data)));
        }

        $this->data = array_replace($this->data, $data);
    }

    /**
     * Reconfigure internal array to a new one, clearing all data.
     *
     * @param array|self $data Array or self instance.
     *
     * @throws Exception\InvalidDataTypeException If type of data is invalid.
     */
    public function reconfigureArray($data = null)
    {
        if ($data === null) {
            // Reconfigure over an empty array.
            $this->data = [ ];
        }
        else if (is_array($data)) {
            // Reconfigure over an existent array.
            $this->data = $data;
        }
        elseif ($data instanceof self) {
            // Reconfigure over an existent Data.
            $this->data = $data->data;
        }
        else {
            // Throws invalid data type exception.
            throw new Exception\InvalidDataTypeException('invalid data type');
        }
    }

    /**
     * Check if keys exists.
     *
     * @param  string $key Key name.
     *
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove key.
     *
     * @param string $key Key name.
     */
    public function remove($key)
    {
        unset( $this->data[$key] );
    }

    /**
     * Returns internals data.
     *
     * @param  string $key Key name.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set a key value.
     *
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Check if keys was setted.
     *
     * @param  string $key Key name.
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Remove key.
     *
     * @param string $key Key name.
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * Returns internals data.
     *
     * @param  string $key Key name.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a key value.
     *
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Check if keys was setted.
     *
     * @param  string $key Key name.
     *
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Remove key.
     *
     * @param string $key Key name.
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * Count array keys.
     * @return integer
     */
    public function count()
    {
        return count($this->data);
    }
}
