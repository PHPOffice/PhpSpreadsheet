<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Row
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Row index.
     *
     * @var int
     */
    private $rowIndex = 0;

    /**
     * Create a new row.
     *
     * @param int $rowIndex
     */
    public function __construct(?Worksheet $worksheet = null, $rowIndex = 1)
    {
        // Set parent and row index
        $this->worksheet = $worksheet;
        $this->rowIndex = $rowIndex;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->worksheet = null; // @phpstan-ignore-line
    }

    /**
     * Get row index.
     */
    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * Get cell iterator.
     *
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn Optionally, the column address at which to stop iterating
     *
     * @return RowCellIterator
     */
    public function getCellIterator($startColumn = 'A', $endColumn = null)
    {
        return new RowCellIterator($this->worksheet, $this->rowIndex, $startColumn, $endColumn);
    }

    /**
     * Returns bound worksheet.
     */
    public function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }
}
