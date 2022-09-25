<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellStyleAssessor;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

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
     * @var Cells
     */
    private $parent;

    /**
     * Index to the cellXf reference for the styling of this cell.
     *
     * @var int
     */
    private $xfIndex = 0;

    /**
     * @var ?string
     */
    private $arrayFormulaRange;

    /**
     * Attributes of the formula.
     *
     * @var null|array
     */
    private $formulaAttributes;

    /**
     * Update the cell into the cell collection.
     *
     * @return $this
     */
    public function updateInCollection(): self
    {
        $this->parent->update($this);

        return $this;
    }

    public function detach(): void
    {
        // @phpstan-ignore-next-line
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
        return $this->parent->getCurrentColumn();
    }

    /**
     * Get cell coordinate row.
     *
     * @return int
     */
    public function getRow()
    {
        return $this->parent->getCurrentRow();
    }

    /**
     * Get cell coordinate.
     *
     * @return string
     */
    public function getCoordinate()
    {
        try {
            $coordinate = $this->parent->getCurrentCoordinate();
        } catch (Throwable $e) {
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
        $currentCalendar = SharedDate::getExcelCalendar();
        SharedDate::setExcelCalendar($this->getWorksheet()->getParent()->getExcelCalendar());

        $formattedValue = (string) NumberFormat::toFormattedString(
            $this->getCalculatedValue(),
            $this->getStyle()
                ->getNumberFormat()->getFormatCode()
        );

        SharedDate::setExcelCalendar($currentCalendar);

        return $formattedValue;
    }

    /**
     * Set cell value.
     *
     *    Sets the value for a cell, automatically determining the datatype using the value binder
     *
     * @param mixed $value Value
     */
    public function setValue($value, bool $isArrayFormula = false, ?string $arrayFormulaRange = null): self
    {
        if (!self::getValueBinder()->bindValue($this, $value, $isArrayFormula, $arrayFormulaRange)) {
            throw new Exception('Value could not be bound to cell.');
        }

        return $this;
    }

    protected function formulaAttributes(bool $isArrayFormula, ?string $arrayFormulaRange): array
    {
        if ($isArrayFormula === true) {
            return [
                't' => 'array',
                'ref' => $arrayFormulaRange === null ? $this->getCoordinate() : $arrayFormulaRange,
            ];
        }

        return [];
    }

    private function arrayFormulaRangeCheck(?string $arrayFormulaRange = null): bool
    {
        if ($arrayFormulaRange !== null) {
            if ($this->isInRange($arrayFormulaRange) && $this->isTopLeftRangeCell($arrayFormulaRange) === false) {
                if (IOFactory::isLoading() === false) {
                    throw new Exception(sprintf(
                        'Cell %s is within the spillage range of a formula, and cannot be changed',
                        $this->getCoordinate()
                    ));
                }
            }

            return $this->isTopLeftRangeCell($arrayFormulaRange);
        }

        return false;
    }

    private function clearSpillageRange(string $arrayFormulaRange): void
    {
        $thisCell = $this->getCoordinate();
        $worksheet = $this->getWorksheet();

        foreach (Coordinate::extractAllCellReferencesInRange($arrayFormulaRange) as $cellAddress) {
            if ($worksheet->cellExists($cellAddress)) {
                $cell = $worksheet->getCell($cellAddress);
                $cell->value = null;
                $cell->dataType = DataType::TYPE_NULL;
                $cell->arrayFormulaRange = null;
                $cell->updateInCollection();
            }
        }

        $worksheet->getCell($thisCell);
    }

    private function setSpillageRange(string $arrayFormulaRange): void
    {
        $thisCell = $this->getCoordinate();
        $worksheet = $this->getWorksheet();

        foreach (Coordinate::extractAllCellReferencesInRange($arrayFormulaRange) as $cellAddress) {
            $cell = $worksheet->getCell($cellAddress);
            $cell->arrayFormulaRange = $arrayFormulaRange;
            $cell->updateInCollection();
        }

        $worksheet->getCell($thisCell);
    }

    /**
     * Set the value for a cell,
     *     with the explicit data type passed to the method (bypassing any use of the value binders).
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
    public function setValueExplicit($value, $dataType, bool $isArrayFormula = false, ?string $arrayFormulaRange = null)
    {
        if ($this->arrayFormulaRangeCheck($this->arrayFormulaRange)) {
            $this->clearSpillageRange((string) $this->arrayFormulaRange);
        }

        if ($this->arrayFormulaRangeCheck($arrayFormulaRange)) {
            $this->setSpillageRange((string) $arrayFormulaRange);
        }

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
                if (is_string($value) !== true || strpos($value, '=') !== 0) {
                    $dataType = DataType::TYPE_STRING;
                    if (in_array($value, Calculation::$excelConstants, true)) {
                        $value = array_search($value, Calculation::$excelConstants, true);
                    }
                    $value = (string) $value;
                    $this->formulaAttributes = [];
                } else {
                    $this->formulaAttributes = $this->formulaAttributes($isArrayFormula, $arrayFormulaRange);
                }
                $this->value = $value;

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

        return $this->updateInCollection();
    }

    private function processArrayResult(
        Worksheet $worksheet,
        string $coordinate,
        array $result,
        string $value,
        ?array $formulaAttributes
    ): array {
        // We'll need to do a check here for the Singular Operator (@) at some point
        //       and not populate the spillage cells if it's there
        if ($this->isArrayFormula()) {
            // Here is where we should set all cellRange values from the result (but within the range limit)
            // Ensure that our array result dimensions match the specified array formula range dimensions,
            //    expanding or shrinking it as necessary.
            $result = Functions::resizeMatrix(
                $result,
                ...Coordinate::rangeDimension($this->formulaAttributes['ref'] ?? $coordinate)
            );
            // But if we do write it, we get problems with #SPILL! Errors if the spreadsheet is saved
            // TODO How are we going to identify and handle a #SPILL! or a #CALC! error?
            IOFactory::setLoading(true);
            $worksheet->fromArray(
                $result,
                null,
                $coordinate,
                true
            );
            IOFactory::setLoading(false);
            // Using fromArray() would reset the value for this cell with the calculation result
            //      as well as updating the spillage cells,
            //  so we need to restore this cell to its formula value, attributes, and datatype
            $worksheet->getCell($coordinate);
            $this->value = $value;
            $this->dataType = DataType::TYPE_FORMULA;
            $this->formulaAttributes = $formulaAttributes;
            $this->updateInCollection();
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
    public function getCalculatedValue(bool $asArray = false, bool $resetLog = true)
    {
        if ($this->dataType === DataType::TYPE_FORMULA) {
            $currentCalendar = SharedDate::getExcelCalendar();
            SharedDate::setExcelCalendar($this->getWorksheet()->getParent()->getExcelCalendar());

            try {
                $coordinate = $this->getCoordinate();
                $worksheet = $this->getWorksheet();
                $value = $this->value;
                $formulaAttributes = $this->formulaAttributes;
                $index = $this->getWorksheet()->getParent()->getActiveSheetIndex();
                $selected = $this->getWorksheet()->getSelectedCells();

                $result = Calculation::getInstance(
                    $this->getWorksheet()->getParent()
                )->calculateCellValue($this, $asArray, $resetLog);

                $worksheet->getCell($coordinate);

                if (is_array($result)) {
                    $result = $this->processArrayResult($worksheet, $coordinate, $result, $value, $formulaAttributes);

                    // Now we just extract the top-left value from the array to get the result for this specific cell
                    if ($asArray === false) {
                        while (is_array($result)) {
                            $result = array_shift($result);
                        }
                    }
                }

                $this->getWorksheet()->setSelectedCells($selected);
                $this->getWorksheet()->getParent()->setActiveSheetIndex($index);
            } catch (Exception $ex) {
                SharedDate::setExcelCalendar($currentCalendar);
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
                    return $this->calculatedValue; // Fallback for calculations referencing external files.
                } elseif (preg_match('/[Uu]ndefined (name|offset: 2|array key 2)/', $ex->getMessage()) === 1) {
                    return ExcelError::NAME();
                }

                throw new \PhpOffice\PhpSpreadsheet\Calculation\Exception(
                    $this->getWorksheet()->getTitle() . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage()
                );
            }

            SharedDate::setExcelCalendar($currentCalendar);
            if ($result === '#Not Yet Implemented') {
                return $this->calculatedValue; // Fallback if calculation engine does not support the formula.
            }

            return $result;
        } elseif ($this->value instanceof RichText) {
            return $this->value->getPlainText();
        }

        return $this->value;
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
     * Identify if the cell contains an array formula.
     */
    public function isArrayFormula(): bool
    {
        if ($this->dataType === DataType::TYPE_FORMULA) {
            $formulaAttributes = $this->getFormulaAttributes();

            return isset($formulaAttributes['t']) && $formulaAttributes['t'] === 'array';
        }

        return false;
    }

    public function arrayFormulaRange(): ?string
    {
        if ($this->isFormula() && $this->isArrayFormula()) {
            $formulaAttributes = $this->getFormulaAttributes();

            return $formulaAttributes['ref'] ?? null;
        }

        return $this->arrayFormulaRange;
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
     */
    public function getParent(): Cells
    {
        return $this->parent;
    }

    /**
     * Get parent worksheet.
     */
    public function getWorksheet(): Worksheet
    {
        try {
            $worksheet = $this->parent->getParent();
        } catch (Throwable $e) {
            $worksheet = null;
        }
        if ($worksheet === null) {
            throw new Exception('Worksheet no longer exists');
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
     * Is this cell in an array formula spillage range.
     */
    public function isInSpillageRange(): bool
    {
        return $this->arrayFormulaRange !== null;
    }

    private function isTopLeftRangeCell(string $cellRange): bool
    {
        $mergeRange = Coordinate::splitRange($cellRange);
        [$startCell] = $mergeRange[0];

        return $this->getCoordinate() === $startCell;
    }

    /**
     * Is this cell the master (top left cell) in a merge range (that holds the actual data value).
     */
    public function isMergeRangeValueCell(): bool
    {
        $mergeRange = $this->getMergeRange();
        if ($mergeRange === false) {
            return false;
        }

        return $this->isTopLeftRangeCell($mergeRange);
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
     * @param mixed[] $attributes
     *
     * @return $this
     */
    public function setFormulaAttributes(array $attributes): self
    {
        $this->formulaAttributes = $attributes;

        return $this;
    }

    /**
     * Get the formula attributes.
     */
    public function getFormulaAttributes(): ?array
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
