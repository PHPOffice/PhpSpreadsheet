<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RowRange
{
    private const MAX_COLUMN = 'XFD';

    /**
     * @var ?Worksheet
     */
    protected $worksheet;

    /**
     * @var int
     */
    protected $from;

    /**
     * @var int
     */
    protected $to;

    public function __construct(int $from, ?int $to = null, ?Worksheet $worksheet = null)
    {
        $this->validateFromTo($from, $to ?? $from);
        $this->worksheet = $worksheet;
    }

    public static function fromArray(array $array, ?Worksheet $worksheet = null): self
    {
        [$from, $to] = $array;

        return new self($from, $to, $worksheet);
    }

    private function validateFromTo(int $from, int $to): void
    {
        // Identify actual top and bottom values (in case we've been given bottom and top)
        $this->from = min($from, $to);
        $this->to = max($from, $to);
    }

    public function from(): int
    {
        return $this->from;
    }

    public function to(): int
    {
        return $this->to;
    }

    public function rowCount(): int
    {
        return $this->to - $this->from + 1;
    }

    public function toCellRange(): CellRange
    {
        return new CellRange(
            CellAddress::fromColumnAndRow(Coordinate::columnIndexFromString('A'), $this->from, $this->worksheet),
            CellAddress::fromColumnAndRow(Coordinate::columnIndexFromString(self::MAX_COLUMN), $this->to)
        );
    }

    public function __toString(): string
    {
        if ($this->worksheet !== null) {
            $title = str_replace("'", "''", $this->worksheet->getTitle());

            return "'{$title}'!{$this->from}:{$this->to}";
        }

        return "{$this->from}:{$this->to}";
    }
}
