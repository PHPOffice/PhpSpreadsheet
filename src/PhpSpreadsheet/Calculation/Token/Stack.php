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
     * @param null|string $storeKey will store the result under this alias
     * @param null|string $onlyIf will only run computation if the matching
     *      store key is true
     * @param null|string $onlyIfNot will only run computation if the matching
     *      store key is false
     */
    public function push(
        $type,
        $value,
        $reference = null,
        $storeKey = null,
        $onlyIf = null,
        $onlyIfNot = null
    ) {
        $stackItem = $this->getStackItem($type, $value, $reference, $storeKey, $onlyIf, $onlyIfNot);

        $this->stack[$this->count++] = $stackItem;

        if ($type == 'Function') {
            $localeFunction = Calculation::localeFunc($value);
            if ($localeFunction != $value) {
                $this->stack[($this->count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }

    public function getStackItem(
        $type,
        $value,
        $reference = null,
        $storeKey = null,
        $onlyIf = null,
        $onlyIfNot = null
    ) {
        $stackItem = [
            'type' => $type,
            'value' => $value,
            'reference' => $reference,
        ];

        if (isset($storeKey)) {
            $stackItem['storeKey'] = $storeKey;
        }

        if (isset($onlyIf)) {
            $stackItem['onlyIf'] = $onlyIf;
        }

        if (isset($onlyIfNot)) {
            $stackItem['onlyIfNot'] = $onlyIfNot;
        }

        return $stackItem;
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

    public function __toString()
    {
        $str = 'Stack: ';
        foreach ($this->stack as $index => $item) {
            if ($index > $this->count - 1) {
                break;
            }
            $value = $item['value'] ?? 'no value';
            while (is_array($value)) {
                $value = array_pop($value);
            }
            $str .= $value . ' |> ';
        }

        return $str;
    }
}
