<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;

final class StructuredReference implements Operand
{
    public const NAME = 'Structured Reference';

    private const OPEN_BRACE = '[';
    private const CLOSE_BRACE = ']';

    private const ITEM_SPECIFIER_ALL = '#All';
    private const ITEM_SPECIFIER_HEADERS = '#Headers';
    private const ITEM_SPECIFIER_DATA = '#Data';
    private const ITEM_SPECIFIER_TOTALS = '#Totals';
    private const ITEM_SPECIFIER_THIS_ROW = '#This Row';

    private const ITEM_SPECIFIER_ROWS_SET = [
        self::ITEM_SPECIFIER_ALL,
        self::ITEM_SPECIFIER_HEADERS,
        self::ITEM_SPECIFIER_DATA,
        self::ITEM_SPECIFIER_TOTALS,
    ];

    private const TABLE_REFERENCE = '/([\p{L}_\\\\][\p{L}\p{N}\._]+)?(\[(?:[^\]\[]+|(?R))*+\])/miu';

    private string $value;

    private string $tableName;

    private Table $table;

    private string $reference;

    private ?int $headersRow;

    private int $firstDataRow;

    private int $lastDataRow;

    private ?int $totalsRow;

    private array $columns;

    public function __construct(string $structuredReference)
    {
        $this->value = $structuredReference;
    }

    public static function fromParser(string $formula, int $index, array $matches): self
    {
        $val = $matches[0];

        $srCount = substr_count($val, self::OPEN_BRACE)
            - substr_count($val, self::CLOSE_BRACE);
        while ($srCount > 0) {
            $srIndex = strlen($val);
            $srStringRemainder = substr($formula, $index + $srIndex);
            $closingPos = strpos($srStringRemainder, self::CLOSE_BRACE);
            if ($closingPos === false) {
                throw new Exception("Formula Error: No closing ']' to match opening '['");
            }
            $srStringRemainder = substr($srStringRemainder, 0, $closingPos + 1);
            --$srCount;
            if (strpos($srStringRemainder, self::OPEN_BRACE) !== false) {
                ++$srCount;
            }
            $val .= $srStringRemainder;
        }

        return new self($val);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function parse(Cell $cell): string
    {
        $this->getTableStructure($cell);
        $cellRange = ($this->isRowReference()) ? $this->getRowReference($cell) : $this->getColumnReference();

        return $cellRange;
    }

    private function isRowReference(): bool
    {
        return strpos($this->value, '[@') !== false
            || strpos($this->value, '[' . self::ITEM_SPECIFIER_THIS_ROW . ']') !== false;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getTableStructure(Cell $cell): void
    {
        preg_match(self::TABLE_REFERENCE, $this->value, $matches);

        $this->tableName = $matches[1];
        $this->table = ($this->tableName === '')
            ? $this->getTableForCell($cell)
            : $this->getTableByName($cell);
        $this->reference = $matches[2];
        $tableRange = Coordinate::getRangeBoundaries($this->table->getRange());

        $this->headersRow = ($this->table->getShowHeaderRow()) ? (int) $tableRange[0][1] : null;
        $this->firstDataRow = ($this->table->getShowHeaderRow()) ? (int) $tableRange[0][1] + 1 : $tableRange[0][1];
        $this->totalsRow = ($this->table->getShowTotalsRow()) ? (int) $tableRange[1][1] : null;
        $this->lastDataRow = ($this->table->getShowTotalsRow()) ? (int) $tableRange[1][1] - 1 : $tableRange[1][1];

        $this->columns = $this->getColumns($cell, $tableRange);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getTableForCell(Cell $cell): Table
    {
        $tables = $cell->getWorksheet()->getTableCollection();
        foreach ($tables as $table) {
            /** @var Table $table */
            $range = $table->getRange();
            if ($cell->isInRange($range) === true) {
                $this->tableName = $table->getName();

                return $table;
            }
        }

        throw new Exception('Table for Structured Reference cannot be identified');
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getTableByName(Cell $cell): Table
    {
        $table = $cell->getWorksheet()->getTableByName($this->tableName);

        if ($table === null) {
            throw new Exception("Table {$this->tableName} for Structured Reference cannot be located");
        }

        return $table;
    }

    private function getColumns(Cell $cell, array $tableRange): array
    {
        $worksheet = $cell->getWorksheet();
        $cellReference = $cell->getCoordinate();

        $columns = [];
        $lastColumn = ++$tableRange[1][0];
        for ($column = $tableRange[0][0]; $column !== $lastColumn; ++$column) {
            $columns[$column] = $worksheet
                ->getCell($column . ($this->headersRow ?? ($this->firstDataRow - 1)))
                ->getCalculatedValue();
        }

        $worksheet->getCell($cellReference);

        return $columns;
    }

    private function getRowReference(Cell $cell): string
    {
        $reference = str_replace("\u{a0}", ' ', $this->reference);
        /** @var string $reference */
        $reference = str_replace('[' . self::ITEM_SPECIFIER_THIS_ROW . '],', '', $reference);

        foreach ($this->columns as $columnId => $columnName) {
            $columnName = str_replace("\u{a0}", ' ', $columnName);
            $reference = $this->adjustRowReference($columnName, $reference, $cell, $columnId);
        }

        /** @var string $reference */
        return $this->validateParsedReference(trim($reference, '[]@, '));
    }

    private function adjustRowReference(string $columnName, string $reference, Cell $cell, string $columnId): string
    {
        if ($columnName !== '') {
            $cellReference = $columnId . $cell->getRow();
            $pattern1 = '/\[' . preg_quote($columnName) . '\]/miu';
            $pattern2 = '/@' . preg_quote($columnName) . '/miu';
            if (preg_match($pattern1, $reference) === 1) {
                $reference = preg_replace($pattern1, $cellReference, $reference);
            } elseif (preg_match($pattern2, $reference) === 1) {
                $reference = preg_replace($pattern2, $cellReference, $reference);
            }
            /** @var string $reference */
        }

        return $reference;
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getColumnReference(): string
    {
        $reference = str_replace("\u{a0}", ' ', $this->reference);
        $startRow = ($this->totalsRow === null) ? $this->lastDataRow : $this->totalsRow;
        $endRow = ($this->headersRow === null) ? $this->firstDataRow : $this->headersRow;

        [$startRow, $endRow] = $this->getRowsForColumnReference($reference, $startRow, $endRow);
        $reference = $this->getColumnsForColumnReference($reference, $startRow, $endRow);

        $reference = trim($reference, '[]@, ');
        if (substr_count($reference, ':') > 1) {
            $cells = explode(':', $reference);
            $firstCell = array_shift($cells);
            $lastCell = array_pop($cells);
            $reference = "{$firstCell}:{$lastCell}";
        }

        return $this->validateParsedReference($reference);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function validateParsedReference(string $reference): string
    {
        if (preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . ':' . Calculation::CALCULATION_REGEXP_CELLREF . '$/miu', $reference) !== 1) {
            if (preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/miu', $reference) !== 1) {
                throw new Exception(
                    "Invalid Structured Reference {$this->reference} {$reference}",
                    Exception::CALCULATION_ENGINE_PUSH_TO_STACK
                );
            }
        }

        return $reference;
    }

    private function fullData(int $startRow, int $endRow): string
    {
        $columns = array_keys($this->columns);
        $firstColumn = array_shift($columns);
        $lastColumn = (empty($columns)) ? $firstColumn : array_pop($columns);

        return "{$firstColumn}{$startRow}:{$lastColumn}{$endRow}";
    }

    private function getMinimumRow(string $reference): int
    {
        switch ($reference) {
            case self::ITEM_SPECIFIER_ALL:
            case self::ITEM_SPECIFIER_HEADERS:
                return $this->headersRow ?? $this->firstDataRow;
            case self::ITEM_SPECIFIER_DATA:
                return $this->firstDataRow;
            case self::ITEM_SPECIFIER_TOTALS:
                return $this->totalsRow ?? $this->lastDataRow;
        }

        return $this->headersRow ?? $this->firstDataRow;
    }

    private function getMaximumRow(string $reference): int
    {
        switch ($reference) {
            case self::ITEM_SPECIFIER_HEADERS:
                return $this->headersRow ?? $this->firstDataRow;
            case self::ITEM_SPECIFIER_DATA:
                return $this->lastDataRow;
            case self::ITEM_SPECIFIER_ALL:
            case self::ITEM_SPECIFIER_TOTALS:
                return $this->totalsRow ?? $this->lastDataRow;
        }

        return $this->totalsRow ?? $this->lastDataRow;
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return array<int, int>
     */
    private function getRowsForColumnReference(string &$reference, int $startRow, int $endRow): array
    {
        $rowsSelected = false;
        foreach (self::ITEM_SPECIFIER_ROWS_SET as $rowReference) {
            $pattern = '/\[' . $rowReference . '\]/mui';
            /** @var string $reference */
            if (preg_match($pattern, $reference) === 1) {
                if (($rowReference === self::ITEM_SPECIFIER_HEADERS) && ($this->table->getShowHeaderRow() === false)) {
                    throw new Exception(
                        'Table Headers are Hidden, and should not be Referenced',
                        Exception::CALCULATION_ENGINE_PUSH_TO_STACK
                    );
                }
                $rowsSelected = true;
                $startRow = min($startRow, $this->getMinimumRow($rowReference));
                $endRow = max($endRow, $this->getMaximumRow($rowReference));
                $reference = preg_replace($pattern, '', $reference);
            }
        }
        if ($rowsSelected === false) {
            // If there isn't any Special Item Identifier specified, then the selection defaults to data rows only.
            $startRow = $this->firstDataRow;
            $endRow = $this->lastDataRow;
        }

        return [$startRow, $endRow];
    }

    private function getColumnsForColumnReference(string $reference, int $startRow, int $endRow): string
    {
        $columnsSelected = false;
        foreach ($this->columns as $columnId => $columnName) {
            $columnName = str_replace("\u{a0}", ' ', $columnName);
            $cellFrom = "{$columnId}{$startRow}";
            $cellTo = "{$columnId}{$endRow}";
            $cellReference = ($cellFrom === $cellTo) ? $cellFrom : "{$cellFrom}:{$cellTo}";
            $pattern = '/\[' . preg_quote($columnName) . '\]/mui';
            if (preg_match($pattern, $reference) === 1) {
                $columnsSelected = true;
                $reference = preg_replace($pattern, $cellReference, $reference);
            }
            /** @var string $reference */
        }
        if ($columnsSelected === false) {
            return $this->fullData($startRow, $endRow);
        }

        return $reference;
    }
}
