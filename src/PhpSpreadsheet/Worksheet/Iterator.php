<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @implements \Iterator<int, Worksheet>
 */
class Iterator implements \Iterator
{
    /**
     * Spreadsheet to iterate.
     */
    private Spreadsheet $subject;

    /**
     * Current iterator position.
     */
    private int $position = 0;

    /**
     * Create a new worksheet iterator.
     */
    public function __construct(Spreadsheet $subject)
    {
        // Set subject
        $this->subject = $subject;
    }

    /**
     * Rewind iterator.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Current Worksheet.
     */
    public function current(): Worksheet
    {
        return $this->subject->getSheet($this->position);
    }

    /**
     * Current key.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Next value.
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Are there more Worksheet instances available?
     */
    public function valid(): bool
    {
        return $this->position < $this->subject->getSheetCount() && $this->position >= 0;
    }
}
