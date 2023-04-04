<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellStyleAssessor;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Cell
{
    /**
     * Value binder to use.
     *
     * @var IValueBinder
     */
    private static $valueBinder;

    /**
     * Value of the cell.
     *
     * @var mixed
     */
    private $value;

    /**
     *    Calculated value of the cell (used for caching)
     *    This returns the value last calculated by MS Excel or whichever spreadsheet program was used to
     *        create the original spreadsheet file.
     *    Note that this value is not guaranteed to reflect the actual calculated value because it is
     *        possible that auto-calculation was disabled in the original spreadsheet, and underlying data
     *        values used by the formula have changed since it was last calculated.
     *
     * @var mixed
     */
    private $calculatedValue;

    /**
     * Type of the cell data.
     *
     * @var string
     */
    private $dataType;

    /**
     * The collection of cells that this cell belongs to (i.e. The Cell Collection for the parent Worksheet).
     *
     * @var ?Cells
     */
    private $parent;

    /**
     * Index to the cellXf reference for the styling of this cell.
     *
     * @var int
     */
    private $xfIndex = 0;

    /**
     * Attributes of the formula.
     *
     * @var mixed
     */
    private $formulaAttributes;

    /**
     * Update the cell into the cell collection.
     *
     * @return $this
     */
    public function updateInCollection(): self
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new Exception('Cannot update when cell is not bound to a worksheet');
        }
        $parent->update($this);

        return $this;
    }

    public function detach(): void
    {
        $this->parent = null;
    }

    public function attach(Cells $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Create a new Cell.
     *
     * @param mixed $value
     */
    public function __construct($value, ?string $dataType, Worksheet $worksheet)
    {
        // Initialise cell value
        $this->value = $value;

        // Set worksheet cache
        $this->parent = $worksheet->getCellCollection();

        // Set datatype?
        if ($dataType !== null) {
            if ($dataType == DataType::TYPE_STRING2) {
                $dataType = DataType::TYPE_STRING;
            }
            $this->dataType = $dataType;
        } elseif (self::getValueBinder()->bindValue($this, $value) === false) {
            throw new Exception('Value could not be bound to cell.');
        }
    }

    /**
     * Get cell coordinate column.
     *
     * @return string
     */
    public function getColumn()
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new Exception('Cannot get column when cell is not bound to a worksheet');
        }

        return $parent->getCurrentColumn();
    }

    /**
     * Get cell coordinate row.
     *
     * @return int
     */
    public function getRow()
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new Exception('Cannot get row when cell is not bound to a worksheet');
        }

        return $parent->getCurrentRow();
    }

    /**
     * Get cell coordinate.
     *
     * @return string
     */
    public function getCoordinate()
    {
        $parent = $this->parent;
        if ($parent !== null) {
            $coordinate = $parent->getCurrentCoordinate();
        } else {
            $coordinate = null;
        }
        if ($coordinate === null) {
            throw new Exception('Coordinate no longer exists');
        }

        return $coordinate;
    }

    /**
     * Get cell value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get cell value with formatting.
     */
    public function getFormattedValue(): string
    {
        return (string) NumberFormat::toFormattedString(
            $this->getCalculatedValue(),
            (string) $this->getStyle()->getNumberFormat()->getFormatCode()
        );
    }

    /**
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    protected static function updateIfCellIsTableHeader(?Worksheet $workSheet, self $cell, $oldValue, $newValue): void
    {
        if (StringHelper::strToLower($oldValue ?? '') === StringHelper::strToLower($newValue ?? '') || $workSheet === null) {
            return;
        }

        foreach ($workSheet->getTableCollection() as $table) {
            /** @var Table $table */
            if ($cell->isInRange($table->getRange())) {
                $rangeRowsColumns = Coordinate::getRangeBoundaries($table->getRange());
                if ($cell->getRow() === (int) $rangeRowsColumns[0][1]) {
                    Table\Column::updateStructuredReferences($workSheet, $oldValue, $newValue);
                }

                return;
            }
        }
    }

    /**
     * Set cell value.
     *
     *    Sets the value for a cell, automatically determining the datatype using the value binder
     *
     * @param mixed $value Value
     * @param null|IValueBinder $binder Value Binder to override the currently set Value Binder
     *
     * @throws Exception
     *
     * @return $this
     */
    public function setValue($value, ?IValueBinder $binder = null): self
    {
        $binder ??= self::getValueBinder();
        if (!$binder->bindValue($this, $value)) {
            throw new Exception('Value could not be bound to cell.');
        }

        return $this;
    }

    /**
     * Set the value for a cell, with the explicit data type passed to the method (bypassing any use of the value binder).
     *
     * @param mixed $value Value
     * @param string $dataType Explicit data type, see DataType::TYPE_*
     *        Note that PhpSpreadsheet does not validate that the value and datatype are consistent, in using this
     *             method, then it is your responsibility as an end-user developer to validate that the value and
     *             the datatype match.
     *       If you do mismatch value and datatype, then the value you enter may be changed to match the datatype
     *          that you specify.
     *
     * @return Cell
     */
    public function setValueExplicit($value, string $dataType = DataType::TYPE_STRING)
    {
        $oldValue = $this->value;

        // set the value according to data type
        switch ($dataType) {
            case DataType::TYPE_NULL:
                $this->value = null;

                break;
            case DataType::TYPE_STRING2:
                $dataType = DataType::TYPE_STRING;
                // no break
            case DataType::TYPE_STRING:
                // Synonym for string
            case DataType::TYPE_INLINE:
                // Rich text
                $this->value = DataType::checkString($value);

                break;
            case DataType::TYPE_NUMERIC:
                if (is_string($value) && !is_numeric($value)) {
                    throw new Exception('Invalid numeric value for datatype Numeric');
                }
                $this->value = 0 + $value;

                break;
            case DataType::TYPE_FORMULA:
                $this->value = (string) $value;

                break;
            case DataType::TYPE_BOOL:
                $this->value = (bool) $value;

                break;
            case DataType::TYPE_ISO_DATE:
                $this->value = SharedDate::convertIsoDate($value);
                $dataType = DataType::TYPE_NUMERIC;

                break;
            case DataType::TYPE_ERROR:
                $this->value = DataType::checkErrorCode($value);

                break;
            default:
                throw new Exception('Invalid datatype: ' . $dataType);
        }

        // set the datatype
        $this->dataType = $dataType;

        $this->updateInCollection();
        $cellCoordinate = $this->getCoordinate();
        self::updateIfCellIsTableHeader($this->getParent()->getParent(), $this, $oldValue, $value); // @phpstan-ignore-line

        return $this->getParent()->get($cellCoordinate); // @phpstan-ignore-line
    }

    public const CALCULATE_DATE_TIME_ASIS = 0;
    public const CALCULATE_DATE_TIME_FLOAT = 1;
    public const CALCULATE_TIME_FLOAT = 2;

    /** @var int */
    private static $calculateDateTimeType = self::CALCULATE_DATE_TIME_ASIS;

    public static function getCalculateDateTimeType(): int
    {
        return self::$calculateDateTimeType;
    }

    public static function setCalculateDateTimeType(int $calculateDateTimeType): void
    {
        switch ($calculateDateTimeType) {
            case self::CALCULATE_DATE_TIME_ASIS:
            case self::CALCULATE_DATE_TIME_FLOAT:
            case self::CALCULATE_TIME_FLOAT:
                self::$calculateDateTimeType = $calculateDateTimeType;

                break;
            default:
                throw new \PhpOffice\PhpSpreadsheet\Calculation\Exception("Invalid value $calculateDateTimeType for calculated date time type");
        }
    }

    /**
     * Convert date, time, or datetime from int to float if desired.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    private function convertDateTimeInt($result)
    {
        if (is_int($result)) {
            if (self::$calculateDateTimeType === self::CALCULATE_TIME_FLOAT) {
                if (SharedDate::isDateTime($this, $result, false)) {
                    $result = (float) $result;
                }
            } elseif (self::$calculateDateTimeType === self::CALCULATE_DATE_TIME_FLOAT) {
                if (SharedDate::isDateTime($this, $result, true)) {
                    $result = (float) $result;
                }
            }
        }

        return $result;
    }

    /**
     * Get calculated cell value.
     *
     * @param bool $resetLog Whether the calculation engine logger should be reset or not
     *
     * @return mixed
     */
    public function getCalculatedValue(bool $resetLog = true)
    {
        if ($this->dataType === DataType::TYPE_FORMULA) {
            try {
                $index = $this->getWorksheet()->getParentOrThrow()->getActiveSheetIndex();
                $selected = $this->getWorksheet()->getSelectedCells();
                $result = Calculation::getInstance(
                    $this->getWorksheet()->getParent()
                )->calculateCellValue($this, $resetLog);
                $result = $this->convertDateTimeInt($result);
                $this->getWorksheet()->setSelectedCells($selected);
                $this->getWorksheet()->getParentOrThrow()->setActiveSheetIndex($index);
                //    We don't yet handle array returns
                if (is_array($result)) {
                    while (is_array($result)) {
                        $result = array_shift($result);
                    }
                }
            } catch (Exception $ex) {
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
                    return $this->calculatedValue; // Fallback for calculations referencing external files.
                } elseif (preg_match('/[Uu]ndefined (name|offset: 2|array key 2)/', $ex->getMessage()) === 1) {
                    return ExcelError::NAME();
                }

                throw new \PhpOffice\PhpSpreadsheet\Calculation\Exception(
                    $this->getWorksheet()->getTitle() . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage(),
                    $ex->getCode(),
                    $ex
                );
            }

            if ($result === '#Not Yet Implemented') {
                return $this->calculatedValue; // Fallback if calculation engine does not support the formula.
            }

            return $result;
        } elseif ($this->value instanceof RichText) {
            return $this->value->getPlainText();
        }

        return $this->convertDateTimeInt($this->value);
    }

    /**
     * Set old calculated value (cached).
     *
     * @param mixed $originalValue Value
     */
    public function setCalculatedValue($originalValue): self
    {
        if ($originalValue !== null) {
            $this->calculatedValue = (is_numeric($originalValue)) ? (float) $originalValue : $originalValue;
        }

        return $this->updateInCollection();
    }

    /**
     *    Get old calculated value (cached)
     *    This returns the value last calculated by MS Excel or whichever spreadsheet program was used to
     *        create the original spreadsheet file.
     *    Note that this value is not guaranteed to reflect the actual calculated value because it is
     *        possible that auto-calculation was disabled in the original spreadsheet, and underlying data
     *        values used by the formula have changed since it was last calculated.
     *
     * @return mixed
     */
    public function getOldCalculatedValue()
    {
        return $this->calculatedValue;
    }

    /**
     * Get cell data type.
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * Set cell data type.
     *
     * @param string $dataType see DataType::TYPE_*
     */
    public function setDataType($dataType): self
    {
        if ($dataType == DataType::TYPE_STRING2) {
            $dataType = DataType::TYPE_STRING;
        }
        $this->dataType = $dataType;

        return $this->updateInCollection();
    }

    /**
     * Identify if the cell contains a formula.
     */
    public function isFormula(): bool
    {
        return $this->dataType === DataType::TYPE_FORMULA && $this->getStyle()->getQuotePrefix() === false;
    }

    /**
     *    Does this cell contain Data validation rules?
     */
    public function hasDataValidation(): bool
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for data validation when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }

    /**
     * Get Data validation rules.
     */
    public function getDataValidation(): DataValidation
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get data validation for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }

    /**
     * Set Data validation rules.
     */
    public function setDataValidation(?DataValidation $dataValidation = null): self
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set data validation for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setDataValidation($this->getCoordinate(), $dataValidation);

        return $this->updateInCollection();
    }

    /**
     * Does this cell contain valid value?
     */
    public function hasValidValue(): bool
    {
        $validator = new DataValidator();

        return $validator->isValid($this);
    }

    /**
     * Does this cell contain a Hyperlink?
     */
    public function hasHyperlink(): bool
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }

    /**
     * Get Hyperlink.
     */
    public function getHyperlink(): Hyperlink
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }

    /**
     * Set Hyperlink.
     */
    public function setHyperlink(?Hyperlink $hyperlink = null): self
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $hyperlink);

        return $this->updateInCollection();
    }

    /**
     * Get cell collection.
     *
     * @return ?Cells
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parent worksheet.
     */
    public function getWorksheet(): Worksheet
    {
        $parent = $this->parent;
        if ($parent !== null) {
            $worksheet = $parent->getParent();
        } else {
            $worksheet = null;
        }

        if ($worksheet === null) {
            throw new Exception('Worksheet no longer exists');
        }

        return $worksheet;
    }

    public function getWorksheetOrNull(): ?Worksheet
    {
        $parent = $this->parent;
        if ($parent !== null) {
            $worksheet = $parent->getParent();
        } else {
            $worksheet = null;
        }

        return $worksheet;
    }

    /**
     * Is this cell in a merge range.
     */
    public function isInMergeRange(): bool
    {
        return (bool) $this->getMergeRange();
    }

    /**
     * Is this cell the master (top left cell) in a merge range (that holds the actual data value).
     */
    public function isMergeRangeValueCell(): bool
    {
        if ($mergeRange = $this->getMergeRange()) {
            $mergeRange = Coordinate::splitRange($mergeRange);
            [$startCell] = $mergeRange[0];

            return $this->getCoordinate() === $startCell;
        }

        return false;
    }

    /**
     * If this cell is in a merge range, then return the range.
     *
     * @return false|string
     */
    public function getMergeRange()
    {
        foreach ($this->getWorksheet()->getMergeCells() as $mergeRange) {
            if ($this->isInRange($mergeRange)) {
                return $mergeRange;
            }
        }

        return false;
    }

    /**
     * Get cell style.
     */
    public function getStyle(): Style
    {
        return $this->getWorksheet()->getStyle($this->getCoordinate());
    }

    /**
     * Get cell style.
     */
    public function getAppliedStyle(): Style
    {
        if ($this->getWorksheet()->conditionalStylesExists($this->getCoordinate()) === false) {
            return $this->getStyle();
        }
        $range = $this->getWorksheet()->getConditionalRange($this->getCoordinate());
        if ($range === null) {
            return $this->getStyle();
        }

        $matcher = new CellStyleAssessor($this, $range);

        return $matcher->matchConditions($this->getWorksheet()->getConditionalStyles($this->getCoordinate()));
    }

    /**
     * Re-bind parent.
     */
    public function rebindParent(Worksheet $parent): self
    {
        $this->parent = $parent->getCellCollection();

        return $this->updateInCollection();
    }

    /**
     *    Is cell in a specific range?
     *
     * @param string $range Cell range (e.g. A1:A1)
     */
    public function isInRange(string $range): bool
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($range);

        // Translate properties
        $myColumn = Coordinate::columnIndexFromString($this->getColumn());
        $myRow = $this->getRow();

        // Verify if cell is in range
        return ($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn) &&
                ($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow);
    }

    /**
     * Compare 2 cells.
     *
     * @param Cell $a Cell a
     * @param Cell $b Cell b
     *
     * @return int Result of comparison (always -1 or 1, never zero!)
     */
    public static function compareCells(self $a, self $b): int
    {
        if ($a->getRow() < $b->getRow()) {
            return -1;
        } elseif ($a->getRow() > $b->getRow()) {
            return 1;
        } elseif (Coordinate::columnIndexFromString($a->getColumn()) < Coordinate::columnIndexFromString($b->getColumn())) {
            return -1;
        }

        return 1;
    }

    /**
     * Get value binder to use.
     */
    public static function getValueBinder(): IValueBinder
    {
        if (self::$valueBinder === null) {
            self::$valueBinder = new DefaultValueBinder();
        }

        return self::$valueBinder;
    }

    /**
     * Set value binder to use.
     */
    public static function setValueBinder(IValueBinder $binder): void
    {
        self::$valueBinder = $binder;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $propertyName => $propertyValue) {
            if ((is_object($propertyValue)) && ($propertyName !== 'parent')) {
                $this->$propertyName = clone $propertyValue;
            } else {
                $this->$propertyName = $propertyValue;
            }
        }
    }

    /**
     * Get index to cellXf.
     */
    public function getXfIndex(): int
    {
        return $this->xfIndex;
    }

    /**
     * Set index to cellXf.
     */
    public function setXfIndex(int $indexValue): self
    {
        $this->xfIndex = $indexValue;

        return $this->updateInCollection();
    }

    /**
     * Set the formula attributes.
     *
     * @param mixed $attributes
     *
     * @return $this
     */
    public function setFormulaAttributes($attributes): self
    {
        $this->formulaAttributes = $attributes;

        return $this;
    }

    /**
     * Get the formula attributes.
     *
     * @return mixed
     */
    public function getFormulaAttributes()
    {
        return $this->formulaAttributes;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
