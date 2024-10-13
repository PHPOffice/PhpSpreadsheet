<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellStyleAssessor;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Stringable;

class Cell implements Stringable
{
    /**
     * Value binder to use.
     */
    private static ?IValueBinder $valueBinder = null;

    /**
     * Value of the cell.
     */
    private mixed $value;

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
     */
    private string $dataType;

    /**
     * The collection of cells that this cell belongs to (i.e. The Cell Collection for the parent Worksheet).
     */
    private ?Cells $parent;

    /**
     * Index to the cellXf reference for the styling of this cell.
     */
    private int $xfIndex = 0;

    /**
     * Attributes of the formula.
     *
     * @var null|array<string, string>
     */
    private mixed $formulaAttributes = null;

    private IgnoredErrors $ignoredErrors;

    /**
     * Update the cell into the cell collection.
     *
     * @throws SpreadsheetException
     */
    public function updateInCollection(): self
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new SpreadsheetException('Cannot update when cell is not bound to a worksheet');
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
     * @throws SpreadsheetException
     */
    public function __construct(mixed $value, ?string $dataType, Worksheet $worksheet)
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
        } else {
            $valueBinder = $worksheet->getParent()?->getValueBinder() ?? self::getValueBinder();
            if ($valueBinder->bindValue($this, $value) === false) {
                throw new SpreadsheetException('Value could not be bound to cell.');
            }
        }
        $this->ignoredErrors = new IgnoredErrors();
    }

    /**
     * Get cell coordinate column.
     *
     * @throws SpreadsheetException
     */
    public function getColumn(): string
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new SpreadsheetException('Cannot get column when cell is not bound to a worksheet');
        }

        return $parent->getCurrentColumn();
    }

    /**
     * Get cell coordinate row.
     *
     * @throws SpreadsheetException
     */
    public function getRow(): int
    {
        $parent = $this->parent;
        if ($parent === null) {
            throw new SpreadsheetException('Cannot get row when cell is not bound to a worksheet');
        }

        return $parent->getCurrentRow();
    }

    /**
     * Get cell coordinate.
     *
     * @throws SpreadsheetException
     */
    public function getCoordinate(): string
    {
        $parent = $this->parent;
        if ($parent !== null) {
            $coordinate = $parent->getCurrentCoordinate();
        } else {
            $coordinate = null;
        }
        if ($coordinate === null) {
            throw new SpreadsheetException('Coordinate no longer exists');
        }

        return $coordinate;
    }

    /**
     * Get cell value.
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getValueString(): string
    {
        $value = $this->value;

        return ($value === '' || is_scalar($value) || $value instanceof Stringable) ? "$value" : '';
    }

    /**
     * Get cell value with formatting.
     */
    public function getFormattedValue(): string
    {
        $currentCalendar = SharedDate::getExcelCalendar();
        SharedDate::setExcelCalendar($this->getWorksheet()->getParent()?->getExcelCalendar());
        $formattedValue = (string) NumberFormat::toFormattedString(
            $this->getCalculatedValueString(),
            (string) $this->getStyle()->getNumberFormat()->getFormatCode(true)
        );
        SharedDate::setExcelCalendar($currentCalendar);

        return $formattedValue;
    }

    protected static function updateIfCellIsTableHeader(?Worksheet $workSheet, self $cell, mixed $oldValue, mixed $newValue): void
    {
        $oldValue = (is_scalar($oldValue) || $oldValue instanceof Stringable) ? ((string) $oldValue) : null;
        $newValue = (is_scalar($newValue) || $newValue instanceof Stringable) ? ((string) $newValue) : null;
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
     * @throws SpreadsheetException
     */
    public function setValue(mixed $value, ?IValueBinder $binder = null): self
    {
        // Cells?->Worksheet?->Spreadsheet
        $binder ??= $this->parent?->getParent()?->getParent()?->getValueBinder() ?? self::getValueBinder();
        if (!$binder->bindValue($this, $value)) {
            throw new SpreadsheetException('Value could not be bound to cell.');
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
     * @throws SpreadsheetException
     */
    public function setValueExplicit(mixed $value, string $dataType = DataType::TYPE_STRING): self
    {
        $oldValue = $this->value;
        $quotePrefix = false;

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
                if (is_string($value) && strlen($value) > 1 && $value[0] === '=') {
                    $quotePrefix = true;
                }
                // no break
            case DataType::TYPE_INLINE:
                // Rich text
                if ($value !== null && !is_scalar($value) && !($value instanceof Stringable)) {
                    throw new SpreadsheetException('Invalid unstringable value for datatype Inline/String/String2');
                }
                $this->value = DataType::checkString(($value instanceof RichText) ? $value : ((string) $value));

                break;
            case DataType::TYPE_NUMERIC:
                if (is_string($value) && !is_numeric($value)) {
                    throw new SpreadsheetException('Invalid numeric value for datatype Numeric');
                }
                $this->value = 0 + $value;

                break;
            case DataType::TYPE_FORMULA:
                if ($value !== null && !is_scalar($value) && !($value instanceof Stringable)) {
                    throw new SpreadsheetException('Invalid unstringable value for datatype Formula');
                }
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
                throw new SpreadsheetException('Invalid datatype: ' . $dataType);
        }

        // set the datatype
        $this->dataType = $dataType;

        $this->updateInCollection();
        $cellCoordinate = $this->getCoordinate();
        self::updateIfCellIsTableHeader($this->getParent()?->getParent(), $this, $oldValue, $value);
        $worksheet = $this->getWorksheet();
        $spreadsheet = $worksheet->getParent();
        if (isset($spreadsheet)) {
            $originalSelected = $worksheet->getSelectedCells();
            $activeSheetIndex = $spreadsheet->getActiveSheetIndex();
            $style = $this->getStyle();
            $oldQuotePrefix = $style->getQuotePrefix();
            if ($oldQuotePrefix !== $quotePrefix) {
                $style->setQuotePrefix($quotePrefix);
            }
            $worksheet->setSelectedCells($originalSelected);
            if ($activeSheetIndex >= 0) {
                $spreadsheet->setActiveSheetIndex($activeSheetIndex);
            }
        }

        return $this->getParent()?->get($cellCoordinate) ?? $this;
    }

    public const CALCULATE_DATE_TIME_ASIS = 0;
    public const CALCULATE_DATE_TIME_FLOAT = 1;
    public const CALCULATE_TIME_FLOAT = 2;

    private static int $calculateDateTimeType = self::CALCULATE_DATE_TIME_ASIS;

    public static function getCalculateDateTimeType(): int
    {
        return self::$calculateDateTimeType;
    }

    /** @throws CalculationException */
    public static function setCalculateDateTimeType(int $calculateDateTimeType): void
    {
        self::$calculateDateTimeType = match ($calculateDateTimeType) {
            self::CALCULATE_DATE_TIME_ASIS, self::CALCULATE_DATE_TIME_FLOAT, self::CALCULATE_TIME_FLOAT => $calculateDateTimeType,
            default => throw new CalculationException("Invalid value $calculateDateTimeType for calculated date time type"),
        };
    }

    /**
     * Convert date, time, or datetime from int to float if desired.
     */
    private function convertDateTimeInt(mixed $result): mixed
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
     * Get calculated cell value converted to string.
     */
    public function getCalculatedValueString(): string
    {
        $value = $this->getCalculatedValue();
        while (is_array($value)) {
            $value = array_shift($value);
        }

        return ($value === '' || is_scalar($value) || $value instanceof Stringable) ? "$value" : '';
    }

    /**
     * Get calculated cell value.
     *
     * @param bool $resetLog Whether the calculation engine logger should be reset or not
     *
     * @throws CalculationException
     */
    public function getCalculatedValue(bool $resetLog = true): mixed
    {
        $title = 'unknown';
        $oldAttributes = $this->formulaAttributes;
        $oldAttributesT = $oldAttributes['t'] ?? '';
        $coordinate = $this->getCoordinate();
        $oldAttributesRef = $oldAttributes['ref'] ?? $coordinate;
        if (!str_contains($oldAttributesRef, ':')) {
            $oldAttributesRef .= ":$oldAttributesRef";
        }
        $originalValue = $this->value;
        $originalDataType = $this->dataType;
        $this->formulaAttributes = [];
        $spill = false;

        if ($this->dataType === DataType::TYPE_FORMULA) {
            try {
                $currentCalendar = SharedDate::getExcelCalendar();
                SharedDate::setExcelCalendar($this->getWorksheet()->getParent()?->getExcelCalendar());
                $thisworksheet = $this->getWorksheet();
                $index = $thisworksheet->getParentOrThrow()->getActiveSheetIndex();
                $selected = $thisworksheet->getSelectedCells();
                $title = $thisworksheet->getTitle();
                $calculation = Calculation::getInstance($thisworksheet->getParent());
                $result = $calculation->calculateCellValue($this, $resetLog);
                $result = $this->convertDateTimeInt($result);
                $thisworksheet->setSelectedCells($selected);
                $thisworksheet->getParentOrThrow()->setActiveSheetIndex($index);
                if (is_array($result) && $calculation->getInstanceArrayReturnType() !== Calculation::RETURN_ARRAY_AS_ARRAY) {
                    while (is_array($result)) {
                        $result = array_shift($result);
                    }
                }
                // if return_as_array for formula like '=sheet!cell'
                if (is_array($result) && count($result) === 1) {
                    $resultKey = array_keys($result)[0];
                    $resultValue = $result[$resultKey];
                    if (is_int($resultKey) && is_array($resultValue) && count($resultValue) === 1) {
                        $resultKey2 = array_keys($resultValue)[0];
                        $resultValue2 = $resultValue[$resultKey2];
                        if (is_string($resultKey2) && !is_array($resultValue2) && preg_match('/[a-zA-Z]{1,3}/', $resultKey2) === 1) {
                            $result = $resultValue2;
                        }
                    }
                }
                $newColumn = $this->getColumn();
                if (is_array($result)) {
                    $this->formulaAttributes['t'] = 'array';
                    $this->formulaAttributes['ref'] = $maxCoordinate = $coordinate;
                    $newRow = $row = $this->getRow();
                    $column = $this->getColumn();
                    foreach ($result as $resultRow) {
                        if (is_array($resultRow)) {
                            $newColumn = $column;
                            foreach ($resultRow as $resultValue) {
                                if ($row !== $newRow || $column !== $newColumn) {
                                    $maxCoordinate = $newColumn . $newRow;
                                    if ($thisworksheet->getCell($newColumn . $newRow)->getValue() !== null) {
                                        if (!Coordinate::coordinateIsInsideRange($oldAttributesRef, $newColumn . $newRow)) {
                                            $spill = true;

                                            break;
                                        }
                                    }
                                }
                                ++$newColumn;
                            }
                            ++$newRow;
                        } else {
                            if ($row !== $newRow || $column !== $newColumn) {
                                $maxCoordinate = $newColumn . $newRow;
                                if ($thisworksheet->getCell($newColumn . $newRow)->getValue() !== null) {
                                    if (!Coordinate::coordinateIsInsideRange($oldAttributesRef, $newColumn . $newRow)) {
                                        $spill = true;
                                    }
                                }
                            }
                            ++$newColumn;
                        }
                        if ($spill) {
                            break;
                        }
                    }
                    if (!$spill) {
                        $this->formulaAttributes['ref'] .= ":$maxCoordinate";
                    }
                    $thisworksheet->getCell($column . $row);
                }
                if (is_array($result)) {
                    if ($oldAttributes !== null && $calculation->getInstanceArrayReturnType() === Calculation::RETURN_ARRAY_AS_ARRAY) {
                        if (($oldAttributesT) === 'array') {
                            $thisworksheet = $this->getWorksheet();
                            $coordinate = $this->getCoordinate();
                            $ref = $oldAttributesRef;
                            if (preg_match('/^([A-Z]{1,3})([0-9]{1,7})(:([A-Z]{1,3})([0-9]{1,7}))?$/', $ref, $matches) === 1) {
                                if (isset($matches[3])) {
                                    $minCol = $matches[1];
                                    $minRow = (int) $matches[2];
                                    // https://github.com/phpstan/phpstan/issues/11602
                                    $maxCol = $matches[4]; // @phpstan-ignore-line
                                    ++$maxCol;
                                    $maxRow = (int) $matches[5]; // @phpstan-ignore-line
                                    for ($row = $minRow; $row <= $maxRow; ++$row) {
                                        for ($col = $minCol; $col !== $maxCol; ++$col) {
                                            if ("$col$row" !== $coordinate) {
                                                $thisworksheet->getCell("$col$row")->setValue(null);
                                            }
                                        }
                                    }
                                }
                            }
                            $thisworksheet->getCell($coordinate);
                        }
                    }
                }
                if ($spill) {
                    $result = ExcelError::SPILL();
                }
                if (is_array($result)) {
                    $newRow = $row = $this->getRow();
                    $newColumn = $column = $this->getColumn();
                    foreach ($result as $resultRow) {
                        if (is_array($resultRow)) {
                            $newColumn = $column;
                            foreach ($resultRow as $resultValue) {
                                if ($row !== $newRow || $column !== $newColumn) {
                                    $thisworksheet->getCell($newColumn . $newRow)->setValue($resultValue);
                                }
                                ++$newColumn;
                            }
                            ++$newRow;
                        } else {
                            if ($row !== $newRow || $column !== $newColumn) {
                                $thisworksheet->getCell($newColumn . $newRow)->setValue($resultRow);
                            }
                            ++$newColumn;
                        }
                    }
                    $thisworksheet->getCell($column . $row);
                    $this->value = $originalValue;
                    $this->dataType = $originalDataType;
                }
            } catch (SpreadsheetException $ex) {
                SharedDate::setExcelCalendar($currentCalendar);
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
                    return $this->calculatedValue; // Fallback for calculations referencing external files.
                } elseif (preg_match('/[Uu]ndefined (name|offset: 2|array key 2)/', $ex->getMessage()) === 1) {
                    return ExcelError::NAME();
                }

                throw new CalculationException(
                    $title . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage(),
                    $ex->getCode(),
                    $ex
                );
            }
            SharedDate::setExcelCalendar($currentCalendar);

            if ($result === Functions::NOT_YET_IMPLEMENTED) {
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
    public function setCalculatedValue(mixed $originalValue, bool $tryNumeric = true): self
    {
        if ($originalValue !== null) {
            $this->calculatedValue = ($tryNumeric && is_numeric($originalValue)) ? (0 + $originalValue) : $originalValue;
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
     */
    public function getOldCalculatedValue(): mixed
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
    public function setDataType(string $dataType): self
    {
        $this->setValueExplicit($this->value, $dataType);

        return $this;
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
     *
     * @throws SpreadsheetException
     */
    public function hasDataValidation(): bool
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot check for data validation when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }

    /**
     * Get Data validation rules.
     *
     * @throws SpreadsheetException
     */
    public function getDataValidation(): DataValidation
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot get data validation for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }

    /**
     * Set Data validation rules.
     *
     * @throws SpreadsheetException
     */
    public function setDataValidation(?DataValidation $dataValidation = null): self
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot set data validation for cell that is not bound to a worksheet');
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
     *
     * @throws SpreadsheetException
     */
    public function hasHyperlink(): bool
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot check for hyperlink when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }

    /**
     * Get Hyperlink.
     *
     * @throws SpreadsheetException
     */
    public function getHyperlink(): Hyperlink
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot get hyperlink for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }

    /**
     * Set Hyperlink.
     *
     * @throws SpreadsheetException
     */
    public function setHyperlink(?Hyperlink $hyperlink = null): self
    {
        if (!isset($this->parent)) {
            throw new SpreadsheetException('Cannot set hyperlink for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $hyperlink);

        return $this->updateInCollection();
    }

    /**
     * Get cell collection.
     */
    public function getParent(): ?Cells
    {
        return $this->parent;
    }

    /**
     * Get parent worksheet.
     *
     * @throws SpreadsheetException
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
            throw new SpreadsheetException('Worksheet no longer exists');
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
        return ($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn)
            && ($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow);
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
     * @param $attributes null|array<string, string>
     *
     * @return $this
     */
    public function setFormulaAttributes(?array $attributes): self
    {
        $this->formulaAttributes = $attributes;

        return $this;
    }

    /**
     * Get the formula attributes.
     *
     * @return null|array<string, string>
     */
    public function getFormulaAttributes(): mixed
    {
        return $this->formulaAttributes;
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        $retVal = $this->value;

        return ($retVal === null || is_scalar($retVal) || $retVal instanceof Stringable) ? ((string) $retVal) : '';
    }

    public function getIgnoredErrors(): IgnoredErrors
    {
        return $this->ignoredErrors;
    }

    public function isLocked(): bool
    {
        $protected = $this->parent?->getParent()?->getProtection()?->getSheet();
        if ($protected !== true) {
            return false;
        }
        $locked = $this->getStyle()->getProtection()->getLocked();

        return $locked !== Protection::PROTECTION_UNPROTECTED;
    }

    public function isHiddenOnFormulaBar(): bool
    {
        if ($this->getDataType() !== DataType::TYPE_FORMULA) {
            return false;
        }
        $protected = $this->parent?->getParent()?->getProtection()?->getSheet();
        if ($protected !== true) {
            return false;
        }
        $hidden = $this->getStyle()->getProtection()->getHidden();

        return $hidden !== Protection::PROTECTION_UNPROTECTED;
    }
}
