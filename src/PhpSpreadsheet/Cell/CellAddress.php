<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

class CellAddress implements Stringable
{
    protected string $cellAddress;

    protected string $columnName = '';

    protected int $columnId;

    protected int $rowId;

    public function __construct(string $cellAddress, protected ?Worksheet $worksheet = null)
    {
        $this->cellAddress = str_replace('$', '', $cellAddress);
        [$this->columnId, $this->rowId, $this->columnName] = Coordinate::indexesFromString($this->cellAddress);
    }

    public function __destruct()
    {
        unset($this->worksheet);
    }

    /**
     * @phpstan-assert int|numeric-string $columnId
     * @phpstan-assert int|numeric-string $rowId
     */
    private static function validateColumnAndRow(int|string $columnId, int|string $rowId): void
    {
        if (!is_numeric($columnId) || $columnId <= 0 || !is_numeric($rowId) || $rowId <= 0) {
            throw new Exception('Row and Column Ids must be positive integer values');
        }
    }

    public static function fromColumnAndRow(int|string $columnId, int|string $rowId, ?Worksheet $worksheet = null): self
    {
        self::validateColumnAndRow($columnId, $rowId);

        return new self(Coordinate::stringFromColumnIndex($columnId) . $rowId, $worksheet);
    }

    public static function fromColumnRowArray(array $array, ?Worksheet $worksheet = null): self
    {
        [$columnId, $rowId] = $array;

        return self::fromColumnAndRow($columnId, $rowId, $worksheet);
    }

    public static function fromCellAddress(string $cellAddress, ?Worksheet $worksheet = null): self
    {
        return new self($cellAddress, $worksheet);
    }

    /**
     * The returned address string will contain the worksheet name as well, if available,
     *     (ie. if a Worksheet was provided to the constructor).
     *     e.g. "'Mark''s Worksheet'!C5".
     */
    public function fullCellAddress(): string
    {
        if ($this->worksheet !== null) {
            $title = str_replace("'", "''", $this->worksheet->getTitle());

            return "'{$title}'!{$this->cellAddress}";
        }

        return $this->cellAddress;
    }

    public function worksheet(): ?Worksheet
    {
        return $this->worksheet;
    }

    /**
     * The returned address string will contain just the column/row address,
     *     (even if a Worksheet was provided to the constructor).
     *     e.g. "C5".
     */
    public function cellAddress(): string
    {
        return $this->cellAddress;
    }

    public function rowId(): int
    {
        return $this->rowId;
    }

    public function columnId(): int
    {
        return $this->columnId;
    }

    public function columnName(): string
    {
        return $this->columnName;
    }

    public function nextRow(int $offset = 1): self
    {
        $newRowId = $this->rowId + $offset;
        if ($newRowId < 1) {
            $newRowId = 1;
        }

        return self::fromColumnAndRow($this->columnId, $newRowId);
    }

    public function previousRow(int $offset = 1): self
    {
        return $this->nextRow(0 - $offset);
    }

    public function nextColumn(int $offset = 1): self
    {
        $newColumnId = $this->columnId + $offset;
        if ($newColumnId < 1) {
            $newColumnId = 1;
        }

        return self::fromColumnAndRow($newColumnId, $this->rowId);
    }

    public function previousColumn(int $offset = 1): self
    {
        return $this->nextColumn(0 - $offset);
    }

    /**
     * The returned address string will contain the worksheet name as well, if available,
     *     (ie. if a Worksheet was provided to the constructor).
     *     e.g. "'Mark''s Worksheet'!C5".
     */
    public function __toString(): string
    {
        return $this->fullCellAddress();
    }
}
