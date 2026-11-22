<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet\PivotTable;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Fluent builder for creating a new pivot table from a range of source data.
 *
 * The builder reads the header row of the source range to discover field names,
 * lets you place those fields on the row / column / value axes, and produces a
 * PivotTable model. When the spreadsheet is saved as Xlsx, the pivot parts are
 * generated with refreshOnLoad set, so the value cells are computed by the
 * spreadsheet application when the file is opened.
 *
 * Example:
 *
 * ```php
 * $builder = new PivotTableBuilder($dataSheet, 'A1:C100');
 * $builder->addRowField('Region')
 *     ->addColumnField('Product')
 *     ->addDataField('Amount', PivotField::SUBTOTAL_SUM);
 * $pivotTable = $builder->build($pivotSheet, 'A3');
 * ```
 */
class PivotTableBuilder
{
    private Worksheet $sourceWorksheet;

    private string $sourceRange;

    /**
     * Field names in source-column order, taken from the header row.
     *
     * @var string[]
     */
    private array $fieldNames;

    /**
     * Requested placements, in the order they were added.
     *
     * This is a list rather than a map keyed by field name: the same source
     * column may be added more than once as a data field, with a different
     * aggregation each time (sum and average of "Amount", say), and every one
     * of those has to survive to the <dataFields> list.
     *
     * @var list<array{field: string, axis: string, subtotal: ?string, caption: ?string}>
     */
    private array $placements = [];

    /**
     * Requested field groupings, keyed by field name.
     *
     * @var array<string, PivotFieldGroup>
     */
    private array $groups = [];

    /**
     * @param string $sourceRange the source data range including the header row, e.g. "A1:C100"
     */
    public function __construct(Worksheet $sourceWorksheet, string $sourceRange)
    {
        $this->sourceWorksheet = $sourceWorksheet;
        $this->sourceRange = str_replace('$', '', $sourceRange);
        $this->fieldNames = $this->readHeaderNames();
    }

    /**
     * Place a field on the row axis.
     */
    public function addRowField(string $fieldName): self
    {
        return $this->place($fieldName, PivotField::AXIS_ROW, null, null);
    }

    /**
     * Place a field on the column axis.
     */
    public function addColumnField(string $fieldName): self
    {
        return $this->place($fieldName, PivotField::AXIS_COLUMN, null, null);
    }

    /**
     * Place a field on the page (report filter) axis.
     */
    public function addPageField(string $fieldName): self
    {
        return $this->place($fieldName, PivotField::AXIS_PAGE, null, null);
    }

    /**
     * Group a numeric field into fixed-width buckets (e.g. 0-100, 100-200).
     *
     * @param float $interval bucket width
     * @param ?float $startNum lower bound of the first bucket (auto when null)
     * @param ?float $endNum upper bound of the last bucket (auto when null)
     */
    public function groupFieldByNumericRange(string $fieldName, float $interval, ?float $startNum = null, ?float $endNum = null): self
    {
        $this->assertFieldExists($fieldName);
        $this->groups[$fieldName] = PivotFieldGroup::numeric($interval, $startNum, $endNum);

        return $this;
    }

    /**
     * Group a date/time field by one or more calendar units.
     *
     * @param string|string[] $groupBy one or more PivotFieldGroup::GROUP_BY_* constants
     * @param ?string $startDate ISO-8601 start (auto when null)
     * @param ?string $endDate ISO-8601 end (auto when null)
     */
    public function groupFieldByDate(string $fieldName, array|string $groupBy, ?string $startDate = null, ?string $endDate = null): self
    {
        $this->assertFieldExists($fieldName);
        $this->groups[$fieldName] = PivotFieldGroup::date($groupBy, $startDate, $endDate);

        return $this;
    }

    /**
     * Add a value (data) field with the given aggregation function.
     *
     * @param string $subtotal one of the PivotField::SUBTOTAL_* constants
     * @param ?string $caption optional display caption (defaults to e.g. "Sum of Amount")
     */
    public function addDataField(string $fieldName, string $subtotal = PivotField::SUBTOTAL_SUM, ?string $caption = null): self
    {
        return $this->place($fieldName, PivotField::AXIS_VALUES, $subtotal, $caption);
    }

    /**
     * Build the PivotTable model and register it on the target worksheet.
     *
     * @param string $targetCell top-left cell of the pivot table, e.g. "A3"
     */
    public function build(Worksheet $targetWorksheet, string $targetCell, string $name = 'PivotTable1'): PivotTable
    {
        $this->assertHasDataField();

        $cacheDefinition = new PivotCacheDefinition(1);
        $cacheDefinition->setSourceWorksheet($this->sourceWorksheet->getTitle());
        $cacheDefinition->setSourceRange($this->sourceRange);
        $cacheDefinition->setCacheFields($this->fieldNames);
        foreach ($this->fieldNames as $fieldName) {
            $cacheDefinition->setSharedItems($fieldName, $this->distinctValues($fieldName));
            if (isset($this->groups[$fieldName])) {
                $cacheDefinition->setFieldGroup($fieldName, $this->groups[$fieldName]);
            }
        }

        $pivotTable = new PivotTable($name);
        $pivotTable->setGenerated(true);
        $pivotTable->setCacheDefinition($cacheDefinition);
        $pivotTable->setLocation($this->targetLocation($targetCell));

        // Exactly one pivot field per source column, in source order: that is
        // what <pivotFields> and <cacheFields> require.
        foreach ($this->fieldNames as $index => $fieldName) {
            $field = new PivotField($index, $fieldName);
            foreach ($this->placementsFor($fieldName) as $placement) {
                if ($placement['axis'] === PivotField::AXIS_VALUES) {
                    // The first aggregation sets the field's own subtotal and
                    // caption; any further ones only add <dataFields> rows.
                    if (!$field->isDataField()) {
                        $field->setDataField(true);
                        $field->setSubtotal($placement['subtotal']);
                        $field->setDataFieldCaption(
                            $placement['caption'] ?? $this->defaultCaption($placement['subtotal'], $fieldName)
                        );
                    }
                } else {
                    $field->setAxis($placement['axis']);
                }
            }
            $pivotTable->addField($field);
        }

        // One <dataFields> entry per requested aggregation, in request order.
        $fieldIndices = array_flip($this->fieldNames);
        foreach ($this->placements as $placement) {
            if ($placement['axis'] !== PivotField::AXIS_VALUES) {
                continue;
            }
            $fieldName = $placement['field'];
            $pivotTable->addDataFieldDefinition(
                new PivotDataField(
                    (int) $fieldIndices[$fieldName],
                    $fieldName,
                    $placement['subtotal'],
                    $placement['caption'] ?? $this->defaultCaption($placement['subtotal'], $fieldName)
                )
            );
        }

        $targetWorksheet->addPivotTable($pivotTable);

        return $pivotTable;
    }

    private function place(string $fieldName, string $axis, ?string $subtotal, ?string $caption): self
    {
        $this->assertFieldExists($fieldName);

        // A field sits on at most one of the row / column / page axes, so a
        // new axis placement replaces any earlier one for that field. Data
        // fields accumulate instead: the same column may be aggregated more
        // than once.
        if ($axis !== PivotField::AXIS_VALUES) {
            $this->placements = array_values(
                array_filter(
                    $this->placements,
                    static fn (array $placement): bool => $placement['field'] !== $fieldName
                        || $placement['axis'] === PivotField::AXIS_VALUES
                )
            );
        }

        $this->placements[] = [
            'field' => $fieldName,
            'axis' => $axis,
            'subtotal' => $subtotal,
            'caption' => $caption,
        ];

        return $this;
    }

    /**
     * The placements for a field, in order.
     *
     * @return list<array{field: string, axis: string, subtotal: ?string, caption: ?string}>
     */
    private function placementsFor(string $fieldName): array
    {
        return array_values(
            array_filter(
                $this->placements,
                static fn (array $placement): bool => $placement['field'] === $fieldName
            )
        );
    }

    private function assertFieldExists(string $fieldName): void
    {
        if (!in_array($fieldName, $this->fieldNames, true)) {
            throw new PhpSpreadsheetException("Pivot source field '{$fieldName}' does not exist in range {$this->sourceRange}");
        }
    }

    private function assertHasDataField(): void
    {
        foreach ($this->placements as $placement) {
            if ($placement['axis'] === PivotField::AXIS_VALUES) {
                return;
            }
        }

        throw new PhpSpreadsheetException('A pivot table requires at least one data (value) field');
    }

    /**
     * @return string[]
     */
    private function readHeaderNames(): array
    {
        [$start, $end] = Coordinate::rangeBoundaries($this->sourceRange);
        $startColumn = (int) $start[0];
        $endColumn = (int) $end[0];
        $headerRow = (int) $start[1];
        $names = [];
        for ($col = $startColumn; $col <= $endColumn; ++$col) {
            $cell = Coordinate::stringFromColumnIndex($col) . $headerRow;
            $names[] = $this->sourceWorksheet->getCell($cell)->getValueString();
        }

        return $names;
    }

    /**
     * Distinct string values in a field's data rows, in first-seen order.
     *
     * @return string[]
     */
    private function distinctValues(string $fieldName): array
    {
        $fieldIndex = array_search($fieldName, $this->fieldNames, true);
        $result = [];
        if ($fieldIndex !== false) {
            [$start, $end] = Coordinate::rangeBoundaries($this->sourceRange);
            $column = Coordinate::stringFromColumnIndex((int) $start[0] + (int) $fieldIndex);
            $firstDataRow = (int) $start[1] + 1;
            $lastRow = (int) $end[1];

            $values = [];
            for ($row = $firstDataRow; $row <= $lastRow; ++$row) {
                $value = $this->sourceWorksheet->getCell($column . $row)->getValueString();
                if ($value !== '') {
                    $values[$value] = true;
                }
            }

            /** @var string[] $result */
            $result = array_keys($values);
        }

        return $result;
    }

    /**
     * Resolve the pivot table's rendered range.
     *
     * The exact extent is recomputed when the application refreshes the pivot
     * table, but the declared range still has to be large enough to hold the
     * layout described by the rest of the definition. A single-cell ref is not
     * expanded on load: Excel treats a range that cannot contain the header,
     * body and grand total as inconsistent and drops the pivot table.
     */
    private function targetLocation(string $targetCell): string
    {
        [$column, $row] = Coordinate::coordinateFromString(str_replace('$', '', $targetCell));
        $firstColumn = Coordinate::columnIndexFromString($column);
        $firstRow = (int) $row;

        $dataFieldCount = max($this->countPlacements(PivotField::AXIS_VALUES), 1);

        // Columns: the row-label column, then the column-field values. Each of
        // those values repeats once per data field, and the grand total does
        // too. With no column field the data fields are laid out across
        // instead, one column each.
        $columnValueCount = 1;
        $hasColumnFields = false;
        foreach ($this->placements as $placement) {
            if ($placement['axis'] === PivotField::AXIS_COLUMN) {
                $hasColumnFields = true;
                $columnValueCount *= $this->renderedItemCount($placement['field']);
            }
        }
        $width = 1;
        $width += $hasColumnFields
            ? ($columnValueCount + 1) * $dataFieldCount
            : $dataFieldCount;

        // Rows: the header row (plus a second one when column fields add their
        // own header), one per rendered value of each row field, and the grand
        // total row.
        $height = $hasColumnFields ? 2 : 1;
        foreach ($this->placements as $placement) {
            if ($placement['axis'] === PivotField::AXIS_ROW) {
                $height += $this->renderedItemCount($placement['field']);
            }
        }
        ++$height; // grand total row

        // The range must stay inside the sheet: a ref that runs off the last
        // row or column is rejected outright.
        $lastColumnIndex = min($firstColumn + $width - 1, AddressRange::MAX_COLUMN_INT);
        $lastRow = min($firstRow + $height - 1, AddressRange::MAX_ROW);

        $firstCell = Coordinate::stringFromColumnIndex($firstColumn) . $firstRow;
        $lastCell = Coordinate::stringFromColumnIndex($lastColumnIndex) . $lastRow;

        return $firstCell . ':' . $lastCell;
    }

    /**
     * Number of rows/columns a field actually renders.
     *
     * A grouped field displays one entry per group bucket (plus the two
     * out-of-range bounds Excel always adds), which bears no relation to how
     * many distinct values the source column holds. An ungrouped field
     * displays one entry per distinct source value.
     */
    private function renderedItemCount(string $fieldName): int
    {
        $group = $this->groups[$fieldName] ?? null;
        if ($group !== null) {
            return max($group->renderedItemCount(), 1);
        }

        return max(count($this->distinctValues($fieldName)), 1);
    }

    /**
     * Count the fields placed on a given axis.
     */
    private function countPlacements(string $axis): int
    {
        $count = 0;
        foreach ($this->placements as $placement) {
            if ($placement['axis'] === $axis) {
                ++$count;
            }
        }

        return $count;
    }

    private function defaultCaption(?string $subtotal, string $fieldName): string
    {
        $labels = [
            PivotField::SUBTOTAL_SUM => 'Sum',
            PivotField::SUBTOTAL_COUNT => 'Count',
            PivotField::SUBTOTAL_AVERAGE => 'Average',
            PivotField::SUBTOTAL_MAX => 'Max',
            PivotField::SUBTOTAL_MIN => 'Min',
            PivotField::SUBTOTAL_PRODUCT => 'Product',
            PivotField::SUBTOTAL_COUNT_NUMS => 'Count',
            PivotField::SUBTOTAL_STD_DEV => 'StdDev',
            PivotField::SUBTOTAL_STD_DEV_P => 'StdDevp',
            PivotField::SUBTOTAL_VAR => 'Var',
            PivotField::SUBTOTAL_VAR_P => 'Varp',
        ];
        $label = $labels[$subtotal] ?? 'Sum';

        return "{$label} of {$fieldName}";
    }
}
