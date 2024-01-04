<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Token;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\BranchPruner;

class Stack
{
    private BranchPruner $branchPruner;

    /**
     * The parser stack for formulae.
     *
     * @var mixed[]
     */
    private array $stack = [];

    /**
     * Count of entries in the parser stack.
     */
    private int $count = 0;

    public function __construct(BranchPruner $branchPruner)
    {
        $this->branchPruner = $branchPruner;
    }

    /**
     * Return the number of entries on the stack.
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Push a new entry onto the stack.
     */
    public function push(string $type, mixed $value, ?string $reference = null): void
    {
        $stackItem = $this->getStackItem($type, $value, $reference);
        $this->stack[$this->count++] = $stackItem;

        if ($type === 'Function') {
            $localeFunction = Calculation::localeFunc($value);
            if ($localeFunction != $value) {
                $this->stack[($this->count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }

    public function pushStackItem(array $stackItem): void
    {
        $this->stack[$this->count++] = $stackItem;
    }

    public function getStackItem(string $type, mixed $value, ?string $reference = null): array
    {
        $stackItem = [
            'type' => $type,
            'value' => $value,
            'reference' => $reference,
        ];

        // will store the result under this alias
        $storeKey = $this->branchPruner->currentCondition();
        if (isset($storeKey) || $reference === 'NULL') {
            $stackItem['storeKey'] = $storeKey;
        }

        // will only run computation if the matching store key is true
        $onlyIf = $this->branchPruner->currentOnlyIf();
        if (isset($onlyIf) || $reference === 'NULL') {
            $stackItem['onlyIf'] = $onlyIf;
        }

        // will only run computation if the matching store key is false
        $onlyIfNot = $this->branchPruner->currentOnlyIfNot();
        if (isset($onlyIfNot) || $reference === 'NULL') {
            $stackItem['onlyIfNot'] = $onlyIfNot;
        }

        return $stackItem;
    }

    /**
     * Pop the last entry from the stack.
     */
    public function pop(): ?array
    {
        if ($this->count > 0) {
            return $this->stack[--$this->count];
        }

        return null;
    }

    /**
     * Return an entry from the stack without removing it.
     */
    public function last(int $n = 1): ?array
    {
        if ($this->count - $n < 0) {
            return null;
        }

        return $this->stack[$this->count - $n];
    }

    /**
     * Clear the stack.
     */
    public function clear(): void
    {
        $this->stack = [];
        $this->count = 0;
    }
}
