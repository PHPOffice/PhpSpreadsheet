<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use Iterator as NativeIterator;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

/**
 * @implements NativeIterator<int, Row>
 */
class RowIterator implements NativeIterator
{
    /**
     * Worksheet to iterate.
     *
     * @var Worksheet
     */
    private $subject;

    /**
     * Current iterator position.
     *
     * @var int
     */
    private $position = 1;

    /**
     * Start position.
     *
     * @var int
     */
    private $startRow = 1;

    /**
     * End position.
     *
     * @var int
     */
    private $endRow = 1;

    /**
     * Create a new row iterator.
     *
     * @param Worksheet $subject The worksheet to iterate over
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow Optionally, the row number at which to stop iterating
     */
    public function __construct(Worksheet $subject, $startRow = 1, $endRow = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    public function __destruct()
    {
        $this->subject = null; // @phpstan-ignore-line
    }

    /**
     * (Re)Set the start row and the current row pointer.
     *
     * @param int $startRow The row number at which to start iterating
     *
     * @return $this
     */
    public function resetStart(int $startRow = 1)
    {
        if ($startRow > $this->subject->getHighestRow()) {
            throw new PhpSpreadsheetException(
                "Start row ({$startRow}) is beyond highest row ({$this->subject->getHighestRow()})"
            );
        }

        $this->startRow = $startRow;
        if ($this->endRow < $this->startRow) {
            $this->endRow = $this->startRow;
        }
        $this->seek($startRow);

        return $this;
    }

    /**
     * (Re)Set the end row.
     *
     * @param int $endRow The row number at which to stop iterating
     *
     * @return $this
     */
    public function resetEnd($endRow = null)
    {
        $this->endRow = $endRow ?: $this->subject->getHighestRow();

        return $this;
    }

    /**
     * Set the row pointer to the selected row.
     *
     * @param int $row The row number to set the current pointer at
     *
     * @return $this
     */
    public function seek(int $row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new PhpSpreadsheetException("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        }
        $this->position = $row;

        return $this;
    }

    /**
     * Rewind the iterator to the starting row.
     */
    public function rewind(): void
    {
        $this->position = $this->startRow;
    }

    /**
     * Return the current row in this worksheet.
     */
    public function current(): Row
    {
        return new Row($this->subject, $this->position);
    }

    /**
     * Return the current iterator key.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Set the iterator to its next value.
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Set the iterator to its previous value.
     */
    public function prev(): void
    {
        --$this->position;
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating.
     */
    public function valid(): bool
    {
        return $this->position <= $this->endRow && $this->position >= $this->startRow;
    }
}
