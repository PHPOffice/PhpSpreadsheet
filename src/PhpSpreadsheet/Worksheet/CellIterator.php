<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator as NativeIterator;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Collection\Cells;

/**
 * @template TKey
 *
 * @implements NativeIterator<TKey, Cell>
 */
abstract class CellIterator implements NativeIterator
{
    public const TREAT_NULL_VALUE_AS_EMPTY_CELL = 1;

    public const TREAT_EMPTY_STRING_AS_EMPTY_CELL = 2;

    public const IF_NOT_EXISTS_RETURN_NULL = false;

    public const IF_NOT_EXISTS_CREATE_NEW = true;

    /**
     * Worksheet to iterate.
     */
    protected Worksheet $worksheet;

    /**
     * Cell Collection to iterate.
     */
    protected Cells $cellCollection;

    /**
     * Iterate only existing cells.
     */
    protected bool $onlyExistingCells = false;

    /**
     * If iterating all cells, and a cell doesn't exist, identifies whether a new cell should be created,
     *    or if the iterator should return a null value.
     */
    protected bool $ifNotExists = self::IF_NOT_EXISTS_CREATE_NEW;

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->worksheet, $this->cellCollection);
    }

    public function getIfNotExists(): bool
    {
        return $this->ifNotExists;
    }

    public function setIfNotExists(bool $ifNotExists = self::IF_NOT_EXISTS_CREATE_NEW): void
    {
        $this->ifNotExists = $ifNotExists;
    }

    /**
     * Get loop only existing cells.
     */
    public function getIterateOnlyExistingCells(): bool
    {
        return $this->onlyExistingCells;
    }

    /**
     * Validate start/end values for 'IterateOnlyExistingCells' mode, and adjust if necessary.
     */
    abstract protected function adjustForExistingOnlyRange(): void;

    /**
     * Set the iterator to loop only existing cells.
     */
    public function setIterateOnlyExistingCells(bool $value): void
    {
        $this->onlyExistingCells = (bool) $value;

        $this->adjustForExistingOnlyRange();
    }
}
