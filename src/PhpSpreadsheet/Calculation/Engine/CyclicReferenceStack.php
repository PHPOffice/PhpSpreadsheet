<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

class CyclicReferenceStack
{
    /**
     * The call stack for calculated cells.
     *
     * @var mixed[]
     */
    private array $stack = [];

    /**
     * Return the number of entries on the stack.
     */
    public function count(): int
    {
        return count($this->stack);
    }

    /**
     * Push a new entry onto the stack.
     */
    public function push(mixed $value): void
    {
        $this->stack[$value] = $value;
    }

    /**
     * Pop the last entry from the stack.
     */
    public function pop(): mixed
    {
        return array_pop($this->stack);
    }

    /**
     * Test to see if a specified entry exists on the stack.
     *
     * @param mixed $value The value to test
     */
    public function onStack(mixed $value): bool
    {
        return isset($this->stack[$value]);
    }

    /**
     * Clear the stack.
     */
    public function clear(): void
    {
        $this->stack = [];
    }

    /**
     * Return an array of all entries on the stack.
     *
     * @return mixed[]
     */
    public function showStack(): array
    {
        return $this->stack;
    }
}
