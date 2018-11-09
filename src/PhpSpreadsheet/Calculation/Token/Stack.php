<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Token;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class Stack
{
    /**
     * The parser stack for formulae.
     *
     * @var mixed[]
     */
    private $stack = [];

    /**
     * Count of entries in the parser stack.
     *
     * @var int
     */
    private $count = 0;

    /**
     * Return the number of entries on the stack.
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Push a new entry onto the stack.
     *
     * @param mixed $type
     * @param mixed $value
     * @param mixed $reference
     */
    public function push($type, $value, $reference = null)
    {
        $this->stack[$this->count++] = [
            'type' => $type,
            'value' => $value,
            'reference' => $reference,
        ];
        if ($type == 'Function') {
            $localeFunction = Calculation::localeFunc($value);
            if ($localeFunction != $value) {
                $this->stack[($this->count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }

    /**
     * Pop the last entry from the stack.
     *
     * @return mixed
     */
    public function pop()
    {
        if ($this->count > 0) {
            return $this->stack[--$this->count];
        }

        return null;
    }

    /**
     * Return an entry from the stack without removing it.
     *
     * @param int $n number indicating how far back in the stack we want to look
     *
     * @return mixed
     */
    public function last($n = 1)
    {
        if ($this->count - $n < 0) {
            return null;
        }

        return $this->stack[$this->count - $n];
    }

    /**
     * Clear the stack.
     */
    public function clear()
    {
        $this->stack = [];
        $this->count = 0;
    }
}
