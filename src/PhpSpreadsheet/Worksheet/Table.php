<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class Table
{
    /**
     * Table Name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Show Header Row.
     *
     * @var bool
     */
    private $showHeaderRow = true;

    /**
     * Show Totals Row.
     *
     * @var bool
     */
    private $showTotalsRow = false;

    /**
     * Table Range.
     *
     * @var string
     */
    private $range = '';

    /**
     * Table Worksheet.
     *
     * @var null|Worksheet
     */
    private $workSheet;

    /**
     * Table Column.
     *
     * @var Table\Column[]
     */
    private $columns = [];

    /**
     * Table Style.
     *
     * @var TableStyle
     */
    private $style;

    /**
     * Create a new Table.
     *
     * @param string $range (e.g. A1:D4)
     *
     * @return $this
     */
    public function __construct(string $range = '', ?Worksheet $worksheet = null)
    {
        $this->setRange($range);
        $this->setWorksheet($worksheet);
        $this->style = new TableStyle();
    }

    /**
     * Get Table name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Table name.
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $name = trim($name);

        if (strlen($name) === 1 && in_array($name, ['C', 'c', 'R', 'r'])) {
            throw new PhpSpreadsheetException('The table name is invalid');
        }
        if (strlen($name) > 255) {
            throw new PhpSpreadsheetException('The table name cannot be longer than 255 characters');
        }
        // Check for A1 or R1C1 cell reference notation
        if (
            preg_match(Coordinate::A1_COORDINATE_REGEX, $name) ||
            preg_match('/^R\[?\-?[0-9]*\]?C\[?\-?[0-9]*\]?$/i', $name)
        ) {
            throw new PhpSpreadsheetException('The table name can\'t be the same as a cell reference');
        }
        if (!preg_match('/^[\p{L}_\\\\]/iu', $name)) {
            throw new PhpSpreadsheetException('The table name must begin a name with a letter, an underscore character (_), or a backslash (\)');
        }
        if (!preg_match('/^[\p{L}_\\\\][\p{L}\p{M}0-9\._]+$/iu', $name)) {
            throw new PhpSpreadsheetException('The table name contains invalid characters');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Get show Header Row.
     *
     * @return bool
     */
    public function getShowHeaderRow()
    {
        return $this->showHeaderRow;
    }

    /**
     * Set show Header Row.
     *
     * @return  $this
     */
    public function setShowHeaderRow(bool $showHeaderRow)
    {
        $this->showHeaderRow = $showHeaderRow;

        return $this;
    }

    /**
     * Get show Totals Row.
     *
     * @return bool
     */
    public function getShowTotalsRow()
    {
        return $this->showTotalsRow;
    }

    /**
     * Set show Totals Row.
     *
     * @return  $this
     */
    public function setShowTotalsRow(bool $showTotalsRow)
    {
        $this->showTotalsRow = $showTotalsRow;

        return $this;
    }

    /**
     * Get Table Range.
     *
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * Set Table Cell Range.
     *
     * @return $this
     */
    public function setRange(string $range): self
    {
        // extract coordinate
        [$worksheet, $range] = Worksheet::extractSheetTitle($range, true);
        if (empty($range)) {
            //    Discard all column rules
            $this->columns = [];
            $this->range = '';

            return $this;
        }

        if (strpos($range, ':') === false) {
            throw new PhpSpreadsheetException('Table must be set on a range of cells.');
        }

        $this->range = $range;
        //    Discard any column ruless that are no longer valid within this range
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
        foreach ($this->columns as $key => $value) {
            $colIndex = Coordinate::columnIndexFromString($key);
            if (($rangeStart[0] > $colIndex) || ($rangeEnd[0] < $colIndex)) {
                unset($this->columns[$key]);
            }
        }

        return $this;
    }

    /**
     * Set Table Cell Range to max row.
     *
     * @return $this
     */
    public function setRangeToMaxRow(): self
    {
        if ($this->workSheet !== null) {
            $thisrange = $this->range;
            $range = preg_replace('/\\d+$/', (string) $this->workSheet->getHighestRow(), $thisrange) ?? '';
            if ($range !== $thisrange) {
                $this->setRange($range);
            }
        }

        return $this;
    }

    /**
     * Get Table's Worksheet.
     *
     * @return null|Worksheet
     */
    public function getWorksheet()
    {
        return $this->workSheet;
    }

    /**
     * Set Table's Worksheet.
     *
     * @return $this
     */
    public function setWorksheet(?Worksheet $worksheet = null)
    {
        if ($this->name !== '' && $worksheet !== null) {
            $spreadsheet = $worksheet->getParent();

            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                foreach ($sheet->getTableCollection() as $table) {
                    if ($table->getName() === $this->name) {
                        throw new PhpSpreadsheetException("Workbook already contains a table named '{$this->name}'");
                    }
                }
            }
        }

        $this->workSheet = $worksheet;

        return $this;
    }

    /**
     * Get all Table Columns.
     *
     * @return Table\Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Validate that the specified column is in the Table range.
     *
     * @param string $column Column name (e.g. A)
     *
     * @return int The column offset within the table range
     */
    public function isColumnInRange($column)
    {
        if (empty($this->range)) {
            throw new PhpSpreadsheetException('No table range is defined.');
        }

        $columnIndex = Coordinate::columnIndexFromString($column);
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
        if (($rangeStart[0] > $columnIndex) || ($rangeEnd[0] < $columnIndex)) {
            throw new PhpSpreadsheetException('Column is outside of current table range.');
        }

        return $columnIndex - $rangeStart[0];
    }

    /**
     * Get a specified Table Column Offset within the defined Table range.
     *
     * @param string $column Column name (e.g. A)
     *
     * @return int The offset of the specified column within the table range
     */
    public function getColumnOffset($column)
    {
        return $this->isColumnInRange($column);
    }

    /**
     * Get a specified Table Column.
     *
     * @param string $column Column name (e.g. A)
     *
     * @return Table\Column
     */
    public function getColumn($column)
    {
        $this->isColumnInRange($column);

        if (!isset($this->columns[$column])) {
            $this->columns[$column] = new Table\Column($column, $this);
        }

        return $this->columns[$column];
    }

    /**
     * Get a specified Table Column by it's offset.
     *
     * @param int $columnOffset Column offset within range (starting from 0)
     *
     * @return Table\Column
     */
    public function getColumnByOffset($columnOffset)
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($this->range);
        $pColumn = Coordinate::stringFromColumnIndex($rangeStart[0] + $columnOffset);

        return $this->getColumn($pColumn);
    }

    /**
     * Set Table.
     *
     * @param string|Table\Column $columnObjectOrString
     *            A simple string containing a Column ID like 'A' is permitted
     *
     * @return $this
     */
    public function setColumn($columnObjectOrString)
    {
        if ((is_string($columnObjectOrString)) && (!empty($columnObjectOrString))) {
            $column = $columnObjectOrString;
        } elseif (is_object($columnObjectOrString) && ($columnObjectOrString instanceof Table\Column)) {
            $column = $columnObjectOrString->getColumnIndex();
        } else {
            throw new PhpSpreadsheetException('Column is not within the table range.');
        }
        $this->isColumnInRange($column);

        if (is_string($columnObjectOrString)) {
            $this->columns[$columnObjectOrString] = new Table\Column($columnObjectOrString, $this);
        } else {
            $columnObjectOrString->setTable($this);
            $this->columns[$column] = $columnObjectOrString;
        }
        ksort($this->columns);

        return $this;
    }

    /**
     * Clear a specified Table Column.
     *
     * @param string $column Column name (e.g. A)
     *
     * @return $this
     */
    public function clearColumn($column)
    {
        $this->isColumnInRange($column);

        if (isset($this->columns[$column])) {
            unset($this->columns[$column]);
        }

        return $this;
    }

    /**
     * Shift an Table Column Rule to a different column.
     *
     * Note: This method bypasses validation of the destination column to ensure it is within this Table range.
     *        Nor does it verify whether any column rule already exists at $toColumn, but will simply override any existing value.
     *        Use with caution.
     *
     * @param string $fromColumn Column name (e.g. A)
     * @param string $toColumn Column name (e.g. B)
     *
     * @return $this
     */
    public function shiftColumn($fromColumn, $toColumn)
    {
        $fromColumn = strtoupper($fromColumn);
        $toColumn = strtoupper($toColumn);

        if (($fromColumn !== null) && (isset($this->columns[$fromColumn])) && ($toColumn !== null)) {
            $this->columns[$fromColumn]->setTable();
            $this->columns[$fromColumn]->setColumnIndex($toColumn);
            $this->columns[$toColumn] = $this->columns[$fromColumn];
            $this->columns[$toColumn]->setTable($this);
            unset($this->columns[$fromColumn]);

            ksort($this->columns);
        }

        return $this;
    }

    /**
     * Get table Style.
     *
     * @return TableStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set table Style.
     *
     * @return  $this
     */
    public function setStyle(TableStyle $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                if ($key === 'workSheet') {
                    //    Detach from worksheet
                    $this->{$key} = null;
                } else {
                    $this->{$key} = clone $value;
                }
            } elseif ((is_array($value)) && ($key === 'columns')) {
                //    The columns array of \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet\Table objects
                $this->{$key} = [];
                foreach ($value as $k => $v) {
                    $this->{$key}[$k] = clone $v;
                    // attach the new cloned Column to this new cloned Table object
                    $this->{$key}[$k]->setTable($this);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * toString method replicates previous behavior by returning the range if object is
     * referenced as a property of its worksheet.
     */
    public function __toString()
    {
        return (string) $this->range;
    }
}
