<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

class CellRange implements AddressRange, Stringable
{
    /**
     * @var CellAddress
     */
    protected $from;

    /**
     * @var CellAddress
     */
    protected $to;

    public function __construct(CellAddress $from, CellAddress $to)
    {
        $this->validateFromTo($from, $to);
    }

    private function validateFromTo(CellAddress $from, CellAddress $to): void
    {
        // Identify actual top-left and bottom-right values (in case we've been given top-right and bottom-left)
        $firstColumn = min($from->columnId(), $to->columnId());
        $firstRow = min($from->rowId(), $to->rowId());
        $lastColumn = max($from->columnId(), $to->columnId());
        $lastRow = max($from->rowId(), $to->rowId());

        $fromWorksheet = $from->worksheet();
        $toWorksheet = $to->worksheet();
        $this->validateWorksheets($fromWorksheet, $toWorksheet);

        $this->from = $this->cellAddressWrapper($firstColumn, $firstRow, $fromWorksheet);
        $this->to = $this->cellAddressWrapper($lastColumn, $lastRow, $toWorksheet);
    }

    private function validateWorksheets(?Worksheet $fromWorksheet, ?Worksheet $toWorksheet): void
    {
        if ($fromWorksheet !== null && $toWorksheet !== null) {
            // We could simply compare worksheets rather than worksheet titles; but at some point we may introduce
            //    support for 3d ranges; and at that point we drop this check and let the validation fall through
            //    to the check for same workbook; but unless we check on titles, this test will also detect if the
            //    worksheets are in different spreadsheets, and the next check will never execute or throw its
            //    own exception.
            if ($fromWorksheet->getTitle() !== $toWorksheet->getTitle()) {
                throw new Exception('3d Cell Ranges are not supported');
            } elseif ($fromWorksheet->getParent() !== $toWorksheet->getParent()) {
                throw new Exception('Worksheets must be in the same spreadsheet');
            }
        }
    }

    private function cellAddressWrapper(int $column, int $row, ?Worksheet $worksheet = null): CellAddress
    {
        $cellAddress = Coordinate::stringFromColumnIndex($column) . (string) $row;

        return new class ($cellAddress, $worksheet) extends CellAddress {
            public function nextRow(int $offset = 1): CellAddress
            {
                /** @var CellAddress $result */
                $result = parent::nextRow($offset);
                $this->rowId = $result->rowId;
                $this->cellAddress = $result->cellAddress;

                return $this;
            }

            public function previousRow(int $offset = 1): CellAddress
            {
                /** @var CellAddress $result */
                $result = parent::previousRow($offset);
                $this->rowId = $result->rowId;
                $this->cellAddress = $result->cellAddress;

                return $this;
            }

            public function nextColumn(int $offset = 1): CellAddress
            {
                /** @var CellAddress $result */
                $result = parent::nextColumn($offset);
                $this->columnId = $result->columnId;
                $this->columnName = $result->columnName;
                $this->cellAddress = $result->cellAddress;

                return $this;
            }

            public function previousColumn(int $offset = 1): CellAddress
            {
                /** @var CellAddress $result */
                $result = parent::previousColumn($offset);
                $this->columnId = $result->columnId;
                $this->columnName = $result->columnName;
                $this->cellAddress = $result->cellAddress;

                return $this;
            }
        };
    }

    public function from(): CellAddress
    {
        // Re-order from/to in case the cell addresses have been modified
        $this->validateFromTo($this->from, $this->to);

        return $this->from;
    }

    public function to(): CellAddress
    {
        // Re-order from/to in case the cell addresses have been modified
        $this->validateFromTo($this->from, $this->to);

        return $this->to;
    }

    public function __toString(): string
    {
        // Re-order from/to in case the cell addresses have been modified
        $this->validateFromTo($this->from, $this->to);

        if ($this->from->cellAddress() === $this->to->cellAddress()) {
            return "{$this->from->fullCellAddress()}";
        }

        $fromAddress = $this->from->fullCellAddress();
        $toAddress = $this->to->cellAddress();

        return "{$fromAddress}:{$toAddress}";
    }
}
