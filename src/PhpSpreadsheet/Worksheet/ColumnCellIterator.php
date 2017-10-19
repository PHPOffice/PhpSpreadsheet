<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet;

class ColumnCellIterator extends CellIterator implements \Iterator
{
    /**
     * Column index.
     *
     * @var string
     */
    protected $columnIndex;

    /**
     * Start position.
     *
     * @var int
     */
    protected $startRow = 1;

    /**
     * End position.
     *
     * @var int
     */
    protected $endRow = 1;

    /**
     * Create a new row iterator.
     *
     * @param Worksheet $subject The worksheet to iterate over
     * @param string $columnIndex The column that we want to iterate
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow Optionally, the row number at which to stop iterating
     */
    public function __construct(Worksheet $subject = null, $columnIndex = 'A', $startRow = 1, $endRow = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->columnIndex = Cell::columnIndexFromString($columnIndex) - 1;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start row and the current row pointer.
     *
     * @param int $startRow The row number at which to start iterating
     *
     * @throws PhpSpreadsheetException
     *
     * @return ColumnCellIterator
     */
    public function resetStart($startRow = 1)
    {
        $this->startRow = $startRow;
        $this->adjustForExistingOnlyRange();
        $this->seek($startRow);

        return $this;
    }

    /**
     * (Re)Set the end row.
     *
     * @param int $endRow The row number at which to stop iterating
     *
     * @throws PhpSpreadsheetException
     *
     * @return ColumnCellIterator
     */
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->subject->getHighestRow();
        $this->adjustForExistingOnlyRange();

        return $this;
    }

    /**
     * Set the row pointer to the selected row.
     *
     * @param int $row The row number to set the current pointer at
     *
     * @throws PhpSpreadsheetException
     *
     * @return ColumnCellIterator
     */
    public function seek($row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new PhpSpreadsheetException("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        } elseif ($this->onlyExistingCells && !($this->subject->cellExistsByColumnAndRow($this->columnIndex, $row))) {
            throw new PhpSpreadsheetException('In "IterateOnlyExistingCells" mode and Cell does not exist');
        }
        $this->position = $row;

        return $this;
    }

    /**
     * Rewind the iterator to the starting row.
     */
    public function rewind()
    {
        $this->position = $this->startRow;
    }

    /**
     * Return the current cell in this worksheet column.
     *
     * @return null|Cell
     */
    public function current()
    {
        return $this->subject->getCellByColumnAndRow($this->columnIndex, $this->position);
    }

    /**
     * Return the current iterator key.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Set the iterator to its next value.
     */
    public function next()
    {
        do {
            ++$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position <= $this->endRow));
    }

    /**
     * Set the iterator to its previous value.
     */
    public function prev()
    {
        if ($this->position <= $this->startRow) {
            throw new PhpSpreadsheetException("Row is already at the beginning of range ({$this->startRow} - {$this->endRow})");
        }

        do {
            --$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position >= $this->startRow));
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position <= $this->endRow;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary.
     *
     * @throws PhpSpreadsheetException
     */
    protected function adjustForExistingOnlyRange()
    {
        if ($this->onlyExistingCells) {
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->startRow)) &&
                ($this->startRow <= $this->endRow)) {
                ++$this->startRow;
            }
            if ($this->startRow > $this->endRow) {
                throw new PhpSpreadsheetException('No cells exist within the specified range');
            }
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->endRow)) &&
                ($this->endRow >= $this->startRow)) {
                --$this->endRow;
            }
            if ($this->endRow < $this->startRow) {
                throw new PhpSpreadsheetException('No cells exist within the specified range');
            }
        }
    }
}
