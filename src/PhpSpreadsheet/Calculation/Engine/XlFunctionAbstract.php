<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

use ArrayAccess;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use ReturnTypeWillChange;

/**
 * @property  string $name
 * @property  string $category
 * @property  callable $functionCall
 * @property  string $argumentCount
 *
 * @implements ArrayAccess<string, mixed>
 */
class XlFunctionAbstract implements ArrayAccess
{
    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * @param mixed $offset
     *
     * @return null|mixed
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return (property_exists($this, $offset)) ? $this->$offset : null;
    }

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new Exception('Action not permitted');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new Exception('Action not permitted');
    }

    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * @return null|mixed
     */
    public function __get(string $name)
    {
        return (property_exists($this, $name)) ? $this->{$name} : null;
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        throw new Exception('Action not permitted');
    }

    public function __unset(string $name): void
    {
        throw new Exception('Action not permitted');
    }
}
