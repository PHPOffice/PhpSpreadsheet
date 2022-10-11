<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Collection\Cells;

/**
 * @template TKey
 *
 * @implements Iterator<TKey, Cell>
 */
abstract class CellIterator implements Iterator
{
    public const TREAT_NULL_VALUE_AS_EMPTY_CELL = 1;

    public const TREAT_EMPTY_STRING_AS_EMPTY_CELL = 2;

    /**
     * Worksheet to iterate.
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     * Cell Collection to iterate.
     *
     * @var Cells
     */
    protected $cellCollection;

    /**
     * Iterate only existing cells.
     *
     * @var bool
     */
    protected $onlyExistingCells = false;

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // @phpstan-ignore-next-line
        $this->worksheet = $this->cellCollection = null;
    }

    /**
     * Get loop only existing cells.
     */
    public function getIterateOnlyExistingCells(): bool
    {
        return $this->onlyExistingCells;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary.
     */
    abstract protected function adjustForExistingOnlyRange();

    /**
     * Set the iterator to loop only existing cells.
     */
    public function setIterateOnlyExistingCells(bool $value): void
    {
        $this->onlyExistingCells = (bool) $value;

        $this->adjustForExistingOnlyRange();
    }
}
