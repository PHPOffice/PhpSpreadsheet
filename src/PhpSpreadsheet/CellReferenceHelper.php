<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CellReferenceHelper
{
    /**
     * @var string
     */
    protected $beforeCellAddress;

    /**
     * @var int
     */
    protected $beforeColumn;

    /**
     * @var int
     */
    protected $beforeRow;

    /**
     * @var int
     */
    protected $numberOfColumns;

    /**
     * @var int
     */
    protected $numberOfRows;

    public function __construct(string $beforeCellAddress = 'A1', int $numberOfColumns = 0, int $numberOfRows = 0)
    {
        $this->beforeCellAddress = str_replace('$', '', $beforeCellAddress);
        $this->numberOfColumns = $numberOfColumns;
        $this->numberOfRows = $numberOfRows;

        // Get coordinate of $beforeCellAddress
        [$beforeColumn, $beforeRow] = Coordinate::coordinateFromString($beforeCellAddress);
        $this->beforeColumn = (int) Coordinate::columnIndexFromString($beforeColumn);
        $this->beforeRow = (int) $beforeRow;
    }

    public function refreshRequired(string $beforeCellAddress, int $numberOfColumns, int $numberOfRows): bool
    {
        return $this->beforeCellAddress !== $beforeCellAddress ||
            $this->numberOfColumns !== $numberOfColumns ||
            $this->numberOfRows !== $numberOfRows;
    }

    public function updateCellReference(string $cellReference = 'A1'): string
    {
        if (Coordinate::coordinateIsRange($cellReference)) {
            throw new Exception('Only single cell references may be passed to this method.');
        }

        // Get coordinate of $cellReference
        [$newColumn, $newRow] = Coordinate::coordinateFromString($cellReference);
        $newColumnIndex = (int) Coordinate::columnIndexFromString(str_replace('$', '', $newColumn));
        $newRowIndex = (int) str_replace('$', '', $newRow);

        // Verify which parts should be updated
        $updateColumn = (($newColumn[0] !== '$') && $newColumnIndex >= $this->beforeColumn);
        $updateRow = (($newRow[0] !== '$') && $newRow >= $this->beforeRow);

        // Create new column reference
        if ($updateColumn) {
            $newColumn = Coordinate::stringFromColumnIndex($newColumnIndex + $this->numberOfColumns);
        }

        // Create new row reference
        if ($updateRow) {
            $newRow = $newRowIndex + $this->numberOfRows;
        }

        // Return new reference
        return "{$newColumn}{$newRow}";
    }

    public function cellAddressInDeleteRange(string $cellAddress): bool
    {
        [$cellColumn, $cellRow] = Coordinate::coordinateFromString($cellAddress);
        $cellColumnIndex = Coordinate::columnIndexFromString($cellColumn);
        //    Is cell within the range of rows/columns if we're deleting
        if (
            $this->numberOfRows < 0 &&
            ($cellRow >= ($this->beforeRow + $this->numberOfRows)) &&
            ($cellRow < $this->beforeRow)
        ) {
            return true;
        } elseif (
            $this->numberOfColumns < 0 &&
            ($cellColumnIndex >= ($this->beforeColumn + $this->numberOfColumns)) &&
            ($cellColumnIndex < $this->beforeColumn)
        ) {
            return true;
        }

        return false;
    }
}
