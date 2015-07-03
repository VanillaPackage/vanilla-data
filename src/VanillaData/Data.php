<?php

namespace Rentalhost\VanillaData;

use Countable;
use ArrayAccess;
use ArrayIterator;

class Data implements Countable, ArrayAccess
{
    /**
     * Stores array data.
     * @var array
     */
    private $data;

    /**
     * Stores the iterator.
     * @var scalar[]
     */
    private $iterator;

    /**
     * Construct the data controller.
     * @param array $data Data array.
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
        $this->data = [];
    }

    /**
     * Returns internals data.
     * @param  string $key          Key name.
     * @param  mixed  $defaultValue Default value.
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
     * @param  string  $key           Key name.
     * @param  mixed   $defaultValue  Default value.
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
     * Will throws an exception, except if you avoid it by second parameter.
     * @param  string  $key            Key name.
     * @param  boolean $avoidException Avoid throws exception if type of value is invalid (aka force).
     * @throws Exception\InvalidKeyValueTypeException If type of value of key is invalid.
     * @return self
     */
    public function getSelf($key, $avoidException = false)
    {
        $data = $this->get($key);

        // If it is not an array, decides if will avoid exception or throw it.
        if (!is_array($data)) {
            if ($avoidException === false) {
                throw new Exception\InvalidKeyValueTypeException("invalid key-value type exception");
            }

            // Else, returns an empty self.
            return new self;
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
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Set an array or self to internal array.
     * @param array|self $data      Array or self instance.
     * @param boolean    $overwrite When true, will replace existent keys.
     * @throws Exception\InvalidDataTypeException If type of data is invalid.
     */
    public function setArray($data, $overwrite = true)
    {
        // Validate data.
        if (!is_array($data)
        && !$data instanceof self) {
            throw new Exception\InvalidDataTypeException("invalid data type");
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
     * @param array|self $data Array or self instance.
     * @throws Exception\InvalidDataTypeException If type of data is invalid.
     */
    public function reconfigureArray($data = null)
    {
        if ($data === null) {
            // Reconfigure over an empty array.
            $this->data = [];
        } else if (is_array($data)) {
            // Reconfigure over an existent array.
            $this->data = $data;
        } elseif ($data instanceof self) {
            // Reconfigure over an existent Data.
            $this->data = $data->data;
        } else {
            // Throws invalid data type exception.
            throw new Exception\InvalidDataTypeException("invalid data type");
        }
    }

    /**
     * Check if keys exists.
     * @param  string $key Key name.
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Remove key.
     * @param string $key Key name.
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Returns internals data.
     * @param  string $key Key name.
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set a key value.
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Check if keys was setted.
     * @param  string $key Key name.
     * @return boolean
     */
    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Remove key.
     * @param string $key Key name.
     */
    public function __unset($key)
    {
        $this->remove($key);
    }

    /**
     * Returns internals data.
     * @param  string $key Key name.
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a key value.
     * @param string $key   Key name.
     * @param mixed  $value Key value.
     */
    public function offsetSet($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Check if keys was setted.
     * @param  string $key Key name.
     * @return boolean
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Remove key.
     * @param string $key Key name.
     */
    public function offsetUnset($key)
    {
        return $this->remove($key);
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
