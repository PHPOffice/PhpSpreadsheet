<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

class FormulaArguments implements Stringable
{
    /**
     * @var mixed[]
     */
    protected array $args;

    public function __construct(mixed ...$args)
    {
        $this->args = $args;
    }

    public function populateWorksheet(Worksheet $worksheet): string
    {
        $cells = [];
        $cellAddress = new CellAddress('A2');
        foreach ($this->args as $value) {
            if (is_array($value)) {
                // We need to set a matrix in the worksheet
                $worksheet->fromArray($value, null, (string) $cellAddress, true);
                $from = (string) $cellAddress;
                $columns = is_array($value[0]) ? count($value[0]) : count($value);
                $rows = is_array($value[0]) ? count($value) : 1;
                $to = $cellAddress->nextColumn($columns)->nextRow($rows);
                $cells[] = "{$from}:{$to}";
                $columnIncrement = $columns;
            } else {
                $worksheet->setCellValue($cellAddress, $value);
                $cells[] = (string) $cellAddress;
                $columnIncrement = 1;
            }
            $cellAddress = $cellAddress->nextColumn($columnIncrement);
        }

        return implode(',', $cells);
    }

    private function matrixRows(array $value): string
    {
        $columns = [];
        foreach ($value as $column) {
            $columns[] = $this->stringify($column);
        }

        return implode(',', $columns);
    }

    private function makeMatrix(array $value): string
    {
        $matrix = [];
        foreach ($value as $row) {
            if (is_array($row)) {
                $matrix[] = $this->matrixRows($row);
            } else {
                $matrix[] = $this->stringify($row);
            }
        }

        return implode(';', $matrix);
    }

    private function stringify(mixed $value): string
    {
        if (is_array($value)) {
            return '{' . $this->makeMatrix($value) . '}';
        } elseif (null === $value) {
            return '';
        } elseif (is_string($value)) {
            return '"' . str_replace('"', '""', $value) . '"';
        } elseif (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_scalar($value) || $value instanceof Stringable) {
            return (string) $value;
        }

        throw new Exception('Cannot convert object to string');
    }

    public function __toString(): string
    {
        $args = array_map(
            [self::class, 'stringify'],
            $this->args
        );

        return implode(',', $args);
    }
}
