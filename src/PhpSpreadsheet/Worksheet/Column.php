<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

class Column
{
    /**
     * \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet.
     *
     * @var Worksheet
     */
    private $worksheet;

    /**
     * Column index.
     *
     * @var string
     */
    private $columnIndex;

    /**
     * Create a new column.
     *
     * @param string $columnIndex
     */
    public function __construct(Worksheet $worksheet, $columnIndex = 'A')
    {
        // Set parent and column index
        $this->worksheet = $worksheet;
        $this->columnIndex = $columnIndex;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        // @phpstan-ignore-next-line
        $this->worksheet = null;
    }

    /**
     * Get column index as string eg: 'A'.
     */
    public function getColumnIndex(): string
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
        return new ColumnCellIterator($this->worksheet, $this->columnIndex, $startRow, $endRow);
    }

    /**
     * Returns a boolean true if the column contains no cells. By default, this means that no cell records exist in the
     *         collection for this column. false will be returned otherwise.
     *     This rule can be modified by passing a $definitionOfEmptyFlags value:
     *          1 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL If the only cells in the collection are null value
     *                  cells, then the column will be considered empty.
     *          2 - CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL If the only cells in the collection are empty
     *                  string value cells, then the column will be considered empty.
     *          3 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL | CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     *                  If the only cells in the collection are null value or empty string value cells, then the column
     *                  will be considered empty.
     *
     * @param int $definitionOfEmptyFlags
     *              Possible Flag Values are:
     *                  CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
     *                  CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     */
    public function isEmpty(int $definitionOfEmptyFlags = 0): bool
    {
        $nullValueCellIsEmpty = (bool) ($definitionOfEmptyFlags & CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);
        $emptyStringCellIsEmpty = (bool) ($definitionOfEmptyFlags & CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL);

        $cellIterator = $this->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            /** @scrutinizer ignore-call */
            $value = $cell->getValue();
            if ($value === null && $nullValueCellIsEmpty === true) {
                continue;
            }
            if ($value === '' && $emptyStringCellIsEmpty === true) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * Returns bound worksheet.
     */
    public function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }
}
