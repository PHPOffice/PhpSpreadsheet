<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

abstract class CellIterator implements \Iterator
{
    /**
     * Worksheet to iterate.
     *
     * @var Worksheet
     */
    protected $worksheet;

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
        unset($this->worksheet);
    }

    /**
     * Get loop only existing cells.
     *
     * @return bool
     */
    public function getIterateOnlyExistingCells()
    {
        return $this->onlyExistingCells;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary.
     *
     * @throws PhpSpreadsheetException
     */
    abstract protected function adjustForExistingOnlyRange();

    /**
     * Set the iterator to loop only existing cells.
     *
     * @param bool $value
     *
     * @throws PhpSpreadsheetException
     */
    public function setIterateOnlyExistingCells($value)
    {
        $this->onlyExistingCells = (bool) $value;

        $this->adjustForExistingOnlyRange();
    }
}
