<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CellReferenceHelper
{
    protected string $beforeCellAddress;

    protected int $beforeColumn;

    protected bool $beforeColumnAbsolute = false;

    protected string $beforeColumnString;

    protected int $beforeRow;

    protected bool $beforeRowAbsolute = false;

    protected int $numberOfColumns;

    protected int $numberOfRows;

    public function __construct(string $beforeCellAddress = 'A1', int $numberOfColumns = 0, int $numberOfRows = 0)
    {
        $this->beforeColumnAbsolute = $beforeCellAddress[0] === '$';
        $this->beforeRowAbsolute = strpos($beforeCellAddress, '$', 1) !== false;
        $this->beforeCellAddress = str_replace('$', '', $beforeCellAddress);
        $this->numberOfColumns = $numberOfColumns;
        $this->numberOfRows = $numberOfRows;

        // Get coordinate of $beforeCellAddress
        [$beforeColumn, $beforeRow] = Coordinate::coordinateFromString($beforeCellAddress);
        $this->beforeColumnString = $beforeColumn;
        $this->beforeColumn = (int) Coordinate::columnIndexFromString($beforeColumn);
        $this->beforeRow = (int) $beforeRow;
    }

    public function beforeCellAddress(): string
    {
        return $this->beforeCellAddress;
    }

    public function refreshRequired(string $beforeCellAddress, int $numberOfColumns, int $numberOfRows): bool
    {
        return $this->beforeCellAddress !== $beforeCellAddress
            || $this->numberOfColumns !== $numberOfColumns
            || $this->numberOfRows !== $numberOfRows;
    }

    public function updateCellReference(string $cellReference = 'A1', bool $includeAbsoluteReferences = false, bool $onlyAbsoluteReferences = false, ?bool $topLeft = null): string
    {
        if (Coordinate::coordinateIsRange($cellReference)) {
            throw new Exception('Only single cell references may be passed to this method.');
        }

        // Get coordinate of $cellReference
        [$newColumn, $newRow] = Coordinate::coordinateFromString($cellReference);
        $newColumnIndex = Coordinate::columnIndexFromString(str_replace('$', '', $newColumn));
        $newRowIndex = (int) str_replace('$', '', $newRow);

        $absoluteColumn = $newColumn[0] === '$' ? '$' : '';
        $absoluteRow = $newRow[0] === '$' ? '$' : '';
        // Verify which parts should be updated
        if ($onlyAbsoluteReferences === true) {
            $updateColumn = (($absoluteColumn === '$') && $newColumnIndex >= $this->beforeColumn);
            $updateRow = (($absoluteRow === '$') && $newRowIndex >= $this->beforeRow);
        } elseif ($includeAbsoluteReferences === false) {
            $updateColumn = (($absoluteColumn !== '$') && $newColumnIndex >= $this->beforeColumn);
            $updateRow = (($absoluteRow !== '$') && $newRowIndex >= $this->beforeRow);
        } else {
            $newColumnIndex = $this->computeNewColumnIndex($newColumnIndex, $topLeft);
            $newColumn = $absoluteColumn . Coordinate::stringFromColumnIndex($newColumnIndex);
            $updateColumn = false;

            $newRowIndex = $this->computeNewRowIndex($newRowIndex, $topLeft);
            $newRow = $absoluteRow . $newRowIndex;
            $updateRow = false;
        }

        // Create new column reference
        if ($updateColumn) {
            $newColumn = $this->updateColumnReference($newColumnIndex, $absoluteColumn);
        }

        // Create new row reference
        if ($updateRow) {
            $newRow = $this->updateRowReference($newRowIndex, $absoluteRow);
        }

        // Return new reference
        return "{$newColumn}{$newRow}";
    }

    public function computeNewColumnIndex(int $newColumnIndex, ?bool $topLeft): int
    {
        // A special case is removing the left/top or bottom/right edge of a range
        // $topLeft is null if we aren't adjusting a range at all.
        if (
            $topLeft !== null
            && $this->numberOfColumns < 0
            && $newColumnIndex >= $this->beforeColumn + $this->numberOfColumns
            && $newColumnIndex <= $this->beforeColumn - 1
        ) {
            if ($topLeft) {
                $newColumnIndex = $this->beforeColumn + $this->numberOfColumns;
            } else {
                $newColumnIndex = $this->beforeColumn + $this->numberOfColumns - 1;
            }
        } elseif ($newColumnIndex >= $this->beforeColumn) {
            // Create new column reference
            $newColumnIndex += $this->numberOfColumns;
        }

        return $newColumnIndex;
    }

    public function computeNewRowIndex(int $newRowIndex, ?bool $topLeft): int
    {
        // A special case is removing the left/top or bottom/right edge of a range
        // $topLeft is null if we aren't adjusting a range at all.
        if (
            $topLeft !== null
            && $this->numberOfRows < 0
            && $newRowIndex >= $this->beforeRow + $this->numberOfRows
            && $newRowIndex <= $this->beforeRow - 1
        ) {
            if ($topLeft) {
                $newRowIndex = $this->beforeRow + $this->numberOfRows;
            } else {
                $newRowIndex = $this->beforeRow + $this->numberOfRows - 1;
            }
        } elseif ($newRowIndex >= $this->beforeRow) {
            $newRowIndex = $newRowIndex + $this->numberOfRows;
        }

        return $newRowIndex;
    }

    public function cellAddressInDeleteRange(string $cellAddress): bool
    {
        [$cellColumn, $cellRow] = Coordinate::coordinateFromString($cellAddress);
        $cellColumnIndex = Coordinate::columnIndexFromString($cellColumn);
        //    Is cell within the range of rows/columns if we're deleting
        if (
            $this->numberOfRows < 0
            && ($cellRow >= ($this->beforeRow + $this->numberOfRows))
            && ($cellRow < $this->beforeRow)
        ) {
            return true;
        } elseif (
            $this->numberOfColumns < 0
            && ($cellColumnIndex >= ($this->beforeColumn + $this->numberOfColumns))
            && ($cellColumnIndex < $this->beforeColumn)
        ) {
            return true;
        }

        return false;
    }

    protected function updateColumnReference(int $newColumnIndex, string $absoluteColumn): string
    {
        $newColumn = Coordinate::stringFromColumnIndex(min($newColumnIndex + $this->numberOfColumns, AddressRange::MAX_COLUMN_INT));

        return "{$absoluteColumn}{$newColumn}";
    }

    protected function updateRowReference(int $newRowIndex, string $absoluteRow): string
    {
        $newRow = $newRowIndex + $this->numberOfRows;
        $newRow = ($newRow > AddressRange::MAX_ROW) ? AddressRange::MAX_ROW : $newRow;

        return "{$absoluteRow}{$newRow}";
    }
}
