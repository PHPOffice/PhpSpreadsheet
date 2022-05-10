<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CellAddress
{
    /**
     * @var ?Worksheet
     */
    protected $worksheet;

    /**
     * @var string
     */
    protected $cellAddress;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var int
     */
    protected $columnId;

    /**
     * @var int
     */
    protected $rowId;

    public function __construct(string $cellAddress, ?Worksheet $worksheet = null)
    {
        $this->cellAddress = str_replace('$', '', $cellAddress);
        [$this->columnId, $this->rowId, $this->columnName] = Coordinate::indexesFromString($this->cellAddress);
        $this->worksheet = $worksheet;
    }

    /**
     * @param mixed $columnId
     * @param mixed $rowId
     */
    private static function validateColumnAndRow($columnId, $rowId): void
    {
        if (!is_numeric($columnId) || $columnId <= 0 || !is_numeric($rowId) || $rowId <= 0) {
            throw new Exception('Row and Column Ids must be positive integer values');
        }
    }

    /**
     * @param mixed $columnId
     * @param mixed $rowId
     */
    public static function fromColumnAndRow($columnId, $rowId, ?Worksheet $worksheet = null): self
    {
        self::validateColumnAndRow($columnId, $rowId);

        /** @phpstan-ignore-next-line */
        return new static(Coordinate::stringFromColumnIndex($columnId) . ((string) $rowId), $worksheet);
    }

    public static function fromColumnRowArray(array $array, ?Worksheet $worksheet = null): self
    {
        [$columnId, $rowId] = $array;

        return static::fromColumnAndRow($columnId, $rowId, $worksheet);
    }

    /**
     * @param mixed $cellAddress
     */
    public static function fromCellAddress($cellAddress, ?Worksheet $worksheet = null): self
    {
        /** @phpstan-ignore-next-line */
        return new static($cellAddress, $worksheet);
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

        return static::fromColumnAndRow($this->columnId, $newRowId);
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

        return static::fromColumnAndRow($newColumnId, $this->rowId);
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
    public function __toString()
    {
        return $this->fullCellAddress();
    }
}
