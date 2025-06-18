<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class ColumnIterator implements \Iterator
{
    /**
     * Worksheet to iterate.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Current iterator position.
     *
     * @var int
     */
    private $currentColumnIndex = 1;

    /**
     * Start position.
     *
     * @var int
     */
    private $startColumnIndex = 1;

    /**
     * End position.
     *
     * @var int
     */
    private $endColumnIndex = 1;

    /**
     * Create a new column iterator.
     *
     * @param Worksheet $worksheet The worksheet to iterate over
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn Optionally, the column address at which to stop iterating
     */
    public function __construct(Worksheet $worksheet, $startColumn = 'A', $endColumn = null)
    {
        // Set subject
        $this->worksheet = $worksheet;
        $this->resetEnd($endColumn);
        $this->resetStart($startColumn);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->worksheet);
    }

    /**
     * (Re)Set the start column and the current column pointer.
     *
     * @param string $startColumn The column address at which to start iterating
     *
     * @throws Exception
     *
     * @return ColumnIterator
     */
    public function resetStart($startColumn = 'A')
    {
        $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        if ($startColumnIndex > Coordinate::columnIndexFromString($this->worksheet->getHighestColumn())) {
            throw new Exception("Start column ({$startColumn}) is beyond highest column ({$this->worksheet->getHighestColumn()})");
        }

        $this->startColumnIndex = $startColumnIndex;
        if ($this->endColumnIndex < $this->startColumnIndex) {
            $this->endColumnIndex = $this->startColumnIndex;
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
        $endColumn = $endColumn ? $endColumn : $this->worksheet->getHighestColumn();
        $this->endColumnIndex = Coordinate::columnIndexFromString($endColumn);

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
        $column = Coordinate::columnIndexFromString($column);
        if (($column < $this->startColumnIndex) || ($column > $this->endColumnIndex)) {
            throw new PhpSpreadsheetException("Column $column is out of range ({$this->startColumnIndex} - {$this->endColumnIndex})");
        }
        $this->currentColumnIndex = $column;

        return $this;
    }

    /**
     * Rewind the iterator to the starting column.
     */
    public function rewind()
    {
        $this->currentColumnIndex = $this->startColumnIndex;
    }

    /**
     * Return the current column in this worksheet.
     *
     * @return Column
     */
    public function current()
    {
        return new Column($this->worksheet, Coordinate::stringFromColumnIndex($this->currentColumnIndex));
    }

    /**
     * Return the current iterator key.
     *
     * @return string
     */
    public function key()
    {
        return Coordinate::stringFromColumnIndex($this->currentColumnIndex);
    }

    /**
     * Set the iterator to its next value.
     */
    public function next()
    {
        ++$this->currentColumnIndex;
    }

    /**
     * Set the iterator to its previous value.
     */
    public function prev()
    {
        --$this->currentColumnIndex;
    }

    /**
     * Indicate if more columns exist in the worksheet range of columns that we're iterating.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->currentColumnIndex <= $this->endColumnIndex && $this->currentColumnIndex >= $this->startColumnIndex;
    }
}
