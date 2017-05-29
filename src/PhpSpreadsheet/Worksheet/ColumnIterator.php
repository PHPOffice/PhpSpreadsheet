<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet;

class ColumnIterator implements \Iterator
{
    /**
     * Worksheet to iterate.
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet
     */
    private $subject;

    /**
     * Current iterator position.
     *
     * @var int
     */
    private $position = 0;

    /**
     * Start position.
     *
     * @var int
     */
    private $startColumn = 0;

    /**
     * End position.
     *
     * @var int
     */
    private $endColumn = 0;

    /**
     * Create a new column iterator.
     *
     * @param Worksheet $subject The worksheet to iterate over
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn Optionally, the column address at which to stop iterating
     */
    public function __construct(Worksheet $subject, $startColumn = 'A', $endColumn = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->resetEnd($endColumn);
        $this->resetStart($startColumn);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start column and the current column pointer.
     *
     * @param int $startColumn The column address at which to start iterating
     *
     * @throws Exception
     *
     * @return ColumnIterator
     */
    public function resetStart($startColumn = 'A')
    {
        $startColumnIndex = Cell::columnIndexFromString($startColumn) - 1;
        if ($startColumnIndex > Cell::columnIndexFromString($this->subject->getHighestColumn()) - 1) {
            throw new Exception("Start column ({$startColumn}) is beyond highest column ({$this->subject->getHighestColumn()})");
        }

        $this->startColumn = $startColumnIndex;
        if ($this->endColumn < $this->startColumn) {
            $this->endColumn = $this->startColumn;
        }
        $this->seek($startColumn);

        return $this;
    }

    /**
     * (Re)Set the end column.
     *
     * @param string $endColumn The column address at which to stop iterating
     *
     * @return ColumnIterator
     */
    public function resetEnd($endColumn = null)
    {
        $endColumn = ($endColumn) ? $endColumn : $this->subject->getHighestColumn();
        $this->endColumn = Cell::columnIndexFromString($endColumn) - 1;

        return $this;
    }

    /**
     * Set the column pointer to the selected column.
     *
     * @param string $column The column address to set the current pointer at
     *
     * @throws PhpSpreadsheetException
     *
     * @return ColumnIterator
     */
    public function seek($column = 'A')
    {
        $column = Cell::columnIndexFromString($column) - 1;
        if (($column < $this->startColumn) || ($column > $this->endColumn)) {
            throw new PhpSpreadsheetException("Column $column is out of range ({$this->startColumn} - {$this->endColumn})");
        }
        $this->position = $column;

        return $this;
    }

    /**
     * Rewind the iterator to the starting column.
     */
    public function rewind()
    {
        $this->position = $this->startColumn;
    }

    /**
     * Return the current column in this worksheet.
     *
     * @return Column
     */
    public function current()
    {
        return new Column($this->subject, Cell::stringFromColumnIndex($this->position));
    }

    /**
     * Return the current iterator key.
     *
     * @return string
     */
    public function key()
    {
        return Cell::stringFromColumnIndex($this->position);
    }

    /**
     * Set the iterator to its next value.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Set the iterator to its previous value.
     *
     * @throws PhpSpreadsheetException
     */
    public function prev()
    {
        if ($this->position <= $this->startColumn) {
            throw new PhpSpreadsheetException('Column is already at the beginning of range (' . Cell::stringFromColumnIndex($this->endColumn) . ' - ' . Cell::stringFromColumnIndex($this->endColumn) . ')');
        }
        --$this->position;
    }

    /**
     * Indicate if more columns exist in the worksheet range of columns that we're iterating.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->position <= $this->endColumn;
    }
}
