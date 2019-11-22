<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Column
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet.
     *
     * @var Worksheet
     */
    private $parent;

    /**
     * Column index.
     *
     * @var string
     */
    private $columnIndex;

    /**
     * Create a new column.
     *
     * @param Worksheet $parent
     * @param string $columnIndex
     */
    public function __construct(Worksheet $parent = null, $columnIndex = 'A')
    {
        // Set parent and column index
        $this->parent = $parent;
        $this->columnIndex = $columnIndex;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->parent);
    }

    /**
     * Get column index as string eg: 'A'.
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     * Get cell iterator.
     *
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow Optionally, the row number at which to stop iterating
     *
     * @return ColumnCellIterator
     */
    public function getCellIterator($startRow = 1, $endRow = null)
    {
        return new ColumnCellIterator($this->parent, $this->columnIndex, $startRow, $endRow);
    }
}
