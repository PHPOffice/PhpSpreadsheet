<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

/**
 * @implements AddressRange<string>
 */
class ColumnRange implements AddressRange, Stringable
{
    protected ?Worksheet $worksheet;

    protected int $from;

    protected int $to;

    public function __construct(string $from, ?string $to = null, ?Worksheet $worksheet = null)
    {
        $this->validateFromTo(
            Coordinate::columnIndexFromString($from),
            Coordinate::columnIndexFromString($to ?? $from)
        );
        $this->worksheet = $worksheet;
    }

    public function __destruct()
    {
        $this->worksheet = null;
    }

    public static function fromColumnIndexes(int $from, int $to, ?Worksheet $worksheet = null): self
    {
        return new self(Coordinate::stringFromColumnIndex($from), Coordinate::stringFromColumnIndex($to), $worksheet);
    }

    /**
     * @param array<int|string> $array
     */
    public static function fromArray(array $array, ?Worksheet $worksheet = null): self
    {
        array_walk(
            $array,
            function (&$column): void {
                $column = is_numeric($column) ? Coordinate::stringFromColumnIndex((int) $column) : $column;
            }
        );
        /** @var string $from */
        /** @var string $to */
        [$from, $to] = $array;

        return new self($from, $to, $worksheet);
    }

    private function validateFromTo(int $from, int $to): void
    {
        // Identify actual top and bottom values (in case we've been given bottom and top)
        $this->from = min($from, $to);
        $this->to = max($from, $to);
    }

    public function columnCount(): int
    {
        return $this->to - $this->from + 1;
    }

    public function shiftDown(int $offset = 1): self
    {
        $newFrom = $this->from + $offset;
        $newFrom = ($newFrom < 1) ? 1 : $newFrom;

        $newTo = $this->to + $offset;
        $newTo = ($newTo < 1) ? 1 : $newTo;

        return self::fromColumnIndexes($newFrom, $newTo, $this->worksheet);
    }

    public function shiftUp(int $offset = 1): self
    {
        return $this->shiftDown(0 - $offset);
    }

    public function from(): string
    {
        return Coordinate::stringFromColumnIndex($this->from);
    }

    public function to(): string
    {
        return Coordinate::stringFromColumnIndex($this->to);
    }

    public function fromIndex(): int
    {
        return $this->from;
    }

    public function toIndex(): int
    {
        return $this->to;
    }

    public function toCellRange(): CellRange
    {
        return new CellRange(
            CellAddress::fromColumnAndRow($this->from, 1, $this->worksheet),
            CellAddress::fromColumnAndRow($this->to, AddressRange::MAX_ROW)
        );
    }

    public function __toString(): string
    {
        $from = $this->from();
        $to = $this->to();

        if ($this->worksheet !== null) {
            $title = str_replace("'", "''", $this->worksheet->getTitle());

            return "'{$title}'!{$from}:{$to}";
        }

        return "{$from}:{$to}";
    }
}
