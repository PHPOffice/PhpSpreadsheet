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

    public function beforeCellAddress(): string
    {
        return $this->beforeCellAddress;
    }

    public function refreshRequired(string $beforeCellAddress, int $numberOfColumns, int $numberOfRows): bool
    {
        return $this->beforeCellAddress !== $beforeCellAddress ||
            $this->numberOfColumns !== $numberOfColumns ||
            $this->numberOfRows !== $numberOfRows;
    }

    public function updateCellReference(string $cellReference = 'A1', bool $includeAbsoluteReferences = false): string
    {
        if (Coordinate::coordinateIsRange($cellReference)) {
            throw new Exception('Only single cell references may be passed to this method.');
        }

        // Get coordinate of $cellReference
        [$newColumn, $newRow] = Coordinate::coordinateFromString($cellReference);
        $newColumnIndex = (int) Coordinate::columnIndexFromString(str_replace('$', '', $newColumn));
        $newRowIndex = (int) str_replace('$', '', $newRow);

        $absoluteColumn = $newColumn[0] === '$' ? '$' : '';
        $absoluteRow = $newRow[0] === '$' ? '$' : '';
        // Verify which parts should be updated
        if ($includeAbsoluteReferences === false) {
            $updateColumn = (($absoluteColumn !== '$') && $newColumnIndex >= $this->beforeColumn);
            $updateRow = (($absoluteRow !== '$') && $newRowIndex >= $this->beforeRow);
        } else {
            $updateColumn = ($newColumnIndex >= $this->beforeColumn);
            $updateRow = ($newRowIndex >= $this->beforeRow);
        }

        // Create new column reference
        if ($updateColumn) {
            $newColumn = ($includeAbsoluteReferences === false)
                ? Coordinate::stringFromColumnIndex($newColumnIndex + $this->numberOfColumns)
                : $absoluteColumn . Coordinate::stringFromColumnIndex($newColumnIndex + $this->numberOfColumns);
        }

        // Create new row reference
        if ($updateRow) {
            $newRow = ($includeAbsoluteReferences === false)
                ? $newRowIndex + $this->numberOfRows
                : $absoluteRow . (string) ($newRowIndex + $this->numberOfRows);
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
