<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Column
{
    /**
     * SUBTOTAL function numbers used by Excel for the totals row functions.
     */
    private const TOTALS_ROW_SUBTOTALS = [
        'average' => 101,
        'countnums' => 102,
        'count' => 103,
        'max' => 104,
        'min' => 105,
        'stddev' => 107,
        'sum' => 109,
        'var' => 110,
    ];

    /**
     * Table Column Index.
     */
    private string $columnIndex;

    /**
     * Show Filter Button.
     */
    private bool $showFilterButton = true;

    /**
     * Total Row Label.
     */
    private ?string $totalsRowLabel = null;

    /**
     * Total Row Function.
     */
    private ?string $totalsRowFunction = null;

    /**
     * Total Row Formula.
     */
    private ?string $totalsRowFormula = null;

    /**
     * Column Formula.
     */
    private ?string $columnFormula = null;

    /**
     * Table.
     */
    private ?Table $table;

    /**
     * Create a new Column.
     *
     * @param string $column Column (e.g. A)
     * @param ?Table $table Table for this column
     */
    public function __construct(string $column, ?Table $table = null)
    {
        $this->columnIndex = $column;
        $this->table = $table;
    }

    /**
     * Get Table column index as string eg: 'A'.
     */
    public function getColumnIndex(): string
    {
        return $this->columnIndex;
    }

    /**
     * Set Table column index as string eg: 'A'.
     *
     * @param string $column Column (e.g. A)
     */
    public function setColumnIndex(string $column): self
    {
        // Uppercase coordinate
        $column = strtoupper($column);
        if ($this->table !== null) {
            $this->table->isColumnInRange($column);
        }

        $this->columnIndex = $column;

        return $this;
    }

    /**
     * Get show Filter Button.
     */
    public function getShowFilterButton(): bool
    {
        return $this->showFilterButton;
    }

    /**
     * Set show Filter Button.
     */
    public function setShowFilterButton(bool $showFilterButton): self
    {
        $this->showFilterButton = $showFilterButton;

        return $this;
    }

    /**
     * Get total Row Label.
     */
    public function getTotalsRowLabel(): ?string
    {
        return $this->totalsRowLabel;
    }

    /**
     * Set total Row Label.
     */
    public function setTotalsRowLabel(string $totalsRowLabel): self
    {
        $this->totalsRowLabel = $totalsRowLabel;
        $this->updateTotalsRowCell();

        return $this;
    }

    /**
     * Get total Row Function.
     */
    public function getTotalsRowFunction(): ?string
    {
        return $this->totalsRowFunction;
    }

    /**
     * Set total Row Function.
     */
    public function setTotalsRowFunction(string $totalsRowFunction): self
    {
        $this->totalsRowFunction = $totalsRowFunction;
        $this->updateTotalsRowCell();

        return $this;
    }

    /**
     * Get total Row Formula.
     */
    public function getTotalsRowFormula(): ?string
    {
        return $this->totalsRowFormula;
    }

    /**
     * Set total Row Formula.
     */
    public function setTotalsRowFormula(string $totalsRowFormula): self
    {
        $this->totalsRowFormula = $totalsRowFormula;
        $this->updateTotalsRowCell();

        return $this;
    }

    /**
     * Set the totals row cell of this column from its totals row formula, function or label.
     * An existing value in that cell is left alone.
     */
    public function updateTotalsRowCell(): void
    {
        $table = $this->table;
        $worksheet = $table?->getWorksheet();
        if ($table === null || $worksheet === null || !$table->getShowTotalsRow()) {
            return;
        }

        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($table->getRange());
        $coordinate = $this->columnIndex . $rangeEnd[1];
        if ($worksheet->cellExists($coordinate) && $worksheet->getCell($coordinate)->getValueString() !== '') {
            return;
        }

        $value = $this->getTotalsRowCellValue($table, $worksheet, $rangeStart);
        if ($value !== null) {
            $worksheet->getCell($coordinate)->setValue($value);
        }
    }

    /**
     * @param array{0: int, 1: int} $rangeStart
     */
    private function getTotalsRowCellValue(Table $table, Worksheet $worksheet, array $rangeStart): ?string
    {
        $function = strtolower($this->totalsRowFunction ?? '');
        if ($this->totalsRowFormula !== null && $this->totalsRowFormula !== '' && ($function === '' || $function === 'custom')) {
            return '=' . ltrim($this->totalsRowFormula, '=');
        }

        if (isset(self::TOTALS_ROW_SUBTOTALS[$function])) {
            $offset = Coordinate::columnIndexFromString($this->columnIndex) - $rangeStart[0];
            $headerCoordinate = $this->columnIndex . $rangeStart[1];
            if (!$table->getShowHeaderRow()) {
                $columnName = 'Column' . ($offset + 1);
            } elseif ($worksheet->cellExists($headerCoordinate)) {
                $columnName = $worksheet->getCell($headerCoordinate)->getValueString();
            } else {
                $columnName = '';
            }
            if ($columnName === '') {
                return null;
            }
            // Special characters in a column name are escaped with an apostrophe
            $columnName = (string) preg_replace("/(['#\\[\\]])/", "'$1", $columnName);

            return '=SUBTOTAL(' . self::TOTALS_ROW_SUBTOTALS[$function] . ',' . $table->getName() . '[' . $columnName . '])';
        }

        if ($function === '' && $this->totalsRowLabel !== null && $this->totalsRowLabel !== '') {
            return $this->totalsRowLabel;
        }

        return null;
    }

    /**
     * Get column Formula.
     */
    public function getColumnFormula(): ?string
    {
        return $this->columnFormula;
    }

    /**
     * Set column Formula.
     */
    public function setColumnFormula(string $columnFormula): self
    {
        $this->columnFormula = $columnFormula;

        return $this;
    }

    /**
     * Get this Column's Table.
     */
    public function getTable(): ?Table
    {
        return $this->table;
    }

    /**
     * Set this Column's Table.
     */
    public function setTable(?Table $table = null): self
    {
        $this->table = $table;

        return $this;
    }

    public static function updateStructuredReferences(?Worksheet $workSheet, ?string $oldTitle, ?string $newTitle): void
    {
        if ($workSheet === null || $oldTitle === null || $oldTitle === '' || $newTitle === null) {
            return;
        }

        // Remember that table headings are case-insensitive
        if (StringHelper::strToLower($oldTitle) !== StringHelper::strToLower($newTitle)) {
            // We need to check all formula cells that might contain Structured References that refer
            //    to this column, and update those formulae to reference the new column text
            $spreadsheet = $workSheet->getParentOrThrow();
            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                self::updateStructuredReferencesInCells($sheet, $oldTitle, $newTitle);
            }
            self::updateStructuredReferencesInNamedFormulae($spreadsheet, $oldTitle, $newTitle);
        }
    }

    private static function updateStructuredReferencesInCells(Worksheet $worksheet, string $oldTitle, string $newTitle): void
    {
        $pattern = '/\[(@?)' . preg_quote($oldTitle, '/') . '\]/mui';

        foreach ($worksheet->getCoordinates(false) as $coordinate) {
            $cell = $worksheet->getCell($coordinate);
            if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                $formula = $cell->getValueString();
                if (preg_match($pattern, $formula) === 1) {
                    $formula = preg_replace($pattern, "[$1{$newTitle}]", $formula);
                    $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);
                }
            }
        }
    }

    private static function updateStructuredReferencesInNamedFormulae(Spreadsheet $spreadsheet, string $oldTitle, string $newTitle): void
    {
        $pattern = '/\[(@?)' . preg_quote($oldTitle, '/') . '\]/mui';

        foreach ($spreadsheet->getNamedFormulae() as $namedFormula) {
            $formula = $namedFormula->getValue();
            if (preg_match($pattern, $formula) === 1) {
                $formula = preg_replace($pattern, "[$1{$newTitle}]", $formula) ?? '';
                $namedFormula->setValue($formula);
            }
        }
    }
}
