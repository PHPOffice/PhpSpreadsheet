<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
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
     * Collection of cells.
     *
     * @var Cells
     */
    private $parent;

    /**
     * Index to cellXf.
     *
     * @var int
     */
    private $xfIndex = 0;

    /**
     * Attributes of the formula.
     */
    private $formulaAttributes;

    /**
     * Update the cell into the cell collection.
     *
     * @return self
     */
    public function updateInCollection()
    {
        $this->parent->update($this);

        return $this;
    }

    public function detach()
    {
        $this->parent = null;
    }

    public function attach(Cells $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Create a new Cell.
     *
     * @param mixed $pValue
     * @param string $pDataType
     * @param Worksheet $pSheet
     *
     * @throws Exception
     */
    public function __construct($pValue, $pDataType, Worksheet $pSheet)
    {
        // Initialise cell value
        $this->value = $pValue;

        // Set worksheet cache
        $this->parent = $pSheet->getCellCollection();

        // Set datatype?
        if ($pDataType !== null) {
            if ($pDataType == DataType::TYPE_STRING2) {
                $pDataType = DataType::TYPE_STRING;
            }
            $this->dataType = $pDataType;
        } elseif (!self::getValueBinder()->bindValue($this, $pValue)) {
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
        return $this->parent->getCurrentCoordinate();
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
     *
     * @return string
     */
    public function getFormattedValue()
    {
        return (string) NumberFormat::toFormattedString(
            $this->getCalculatedValue(),
            $this->getStyle()
                ->getNumberFormat()->getFormatCode()
        );
    }

    /**
     * Set cell value.
     *
     *    Sets the value for a cell, automatically determining the datatype using the value binder
     *
     * @param mixed $pValue Value
     *
     * @throws Exception
     *
     * @return Cell
     */
    public function setValue($pValue)
    {
        if (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new Exception('Value could not be bound to cell.');
        }

        return $this;
    }

    /**
     * Set the value for a cell, with the explicit data type passed to the method (bypassing any use of the value binder).
     *
     * @param mixed $pValue Value
     * @param string $pDataType Explicit data type, see DataType::TYPE_*
     *
     * @throws Exception
     *
     * @return Cell
     */
    public function setValueExplicit($pValue, $pDataType)
    {
        // set the value according to data type
        switch ($pDataType) {
            case DataType::TYPE_NULL:
                $this->value = $pValue;

                break;
            case DataType::TYPE_STRING2:
                $pDataType = DataType::TYPE_STRING;
                // no break
            case DataType::TYPE_STRING:
                // Synonym for string
            case DataType::TYPE_INLINE:
                // Rich text
                $this->value = DataType::checkString($pValue);

                break;
            case DataType::TYPE_NUMERIC:
                $this->value = 0 + $pValue;

                break;
            case DataType::TYPE_FORMULA:
                $this->value = (string) $pValue;

                break;
            case DataType::TYPE_BOOL:
                $this->value = (bool) $pValue;

                break;
            case DataType::TYPE_ERROR:
                $this->value = DataType::checkErrorCode($pValue);

                break;
            default:
                throw new Exception('Invalid datatype: ' . $pDataType);

                break;
        }

        // set the datatype
        $this->dataType = $pDataType;

        return $this->updateInCollection();
    }

    /**
     * Get calculated cell value.
     *
     * @param bool $resetLog Whether the calculation engine logger should be reset or not
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function getCalculatedValue($resetLog = true)
    {
        if ($this->dataType == DataType::TYPE_FORMULA) {
            try {
                $result = Calculation::getInstance(
                    $this->getWorksheet()->getParent()
                )->calculateCellValue($this, $resetLog);
                //    We don't yet handle array returns
                if (is_array($result)) {
                    while (is_array($result)) {
                        $result = array_pop($result);
                    }
                }
            } catch (Exception $ex) {
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
                    return $this->calculatedValue; // Fallback for calculations referencing external files.
                }

                throw new \PhpOffice\PhpSpreadsheet\Calculation\Exception(
                    $this->getWorksheet()->getTitle() . '!' . $this->getCoordinate() . ' -> ' . $ex->getMessage()
                );
            }

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
     * @param mixed $pValue Value
     *
     * @return Cell
     */
    public function setCalculatedValue($pValue)
    {
        if ($pValue !== null) {
            $this->calculatedValue = (is_numeric($pValue)) ? (float) $pValue : $pValue;
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
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Set cell data type.
     *
     * @param string $pDataType see DataType::TYPE_*
     *
     * @return Cell
     */
    public function setDataType($pDataType)
    {
        if ($pDataType == DataType::TYPE_STRING2) {
            $pDataType = DataType::TYPE_STRING;
        }
        $this->dataType = $pDataType;

        return $this->updateInCollection();
    }

    /**
     * Identify if the cell contains a formula.
     *
     * @return bool
     */
    public function isFormula()
    {
        return $this->dataType == DataType::TYPE_FORMULA;
    }

    /**
     *    Does this cell contain Data validation rules?
     *
     * @throws Exception
     *
     * @return bool
     */
    public function hasDataValidation()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for data validation when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }

    /**
     * Get Data validation rules.
     *
     * @throws Exception
     *
     * @return DataValidation
     */
    public function getDataValidation()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get data validation for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }

    /**
     * Set Data validation rules.
     *
     * @param DataValidation $pDataValidation
     *
     * @throws Exception
     *
     * @return Cell
     */
    public function setDataValidation(DataValidation $pDataValidation = null)
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set data validation for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setDataValidation($this->getCoordinate(), $pDataValidation);

        return $this->updateInCollection();
    }

    /**
     * Does this cell contain valid value?
     *
     * @return bool
     */
    public function hasValidValue()
    {
        $validator = new DataValidator();

        return $validator->isValid($this);
    }

    /**
     * Does this cell contain a Hyperlink?
     *
     * @throws Exception
     *
     * @return bool
     */
    public function hasHyperlink()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }

    /**
     * Get Hyperlink.
     *
     * @throws Exception
     *
     * @return Hyperlink
     */
    public function getHyperlink()
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }

    /**
     * Set Hyperlink.
     *
     * @param Hyperlink $pHyperlink
     *
     * @throws Exception
     *
     * @return Cell
     */
    public function setHyperlink(Hyperlink $pHyperlink = null)
    {
        if (!isset($this->parent)) {
            throw new Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $pHyperlink);

        return $this->updateInCollection();
    }

    /**
     * Get cell collection.
     *
     * @return Cells
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get parent worksheet.
     *
     * @return Worksheet
     */
    public function getWorksheet()
    {
        return $this->parent->getParent();
    }

    /**
     * Is this cell in a merge range.
     *
     * @return bool
     */
    public function isInMergeRange()
    {
        return (bool) $this->getMergeRange();
    }

    /**
     * Is this cell the master (top left cell) in a merge range (that holds the actual data value).
     *
     * @return bool
     */
    public function isMergeRangeValueCell()
    {
        if ($mergeRange = $this->getMergeRange()) {
            $mergeRange = Coordinate::splitRange($mergeRange);
            [$startCell] = $mergeRange[0];
            if ($this->getCoordinate() === $startCell) {
                return true;
            }
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
     *
     * @return Style
     */
    public function getStyle()
    {
        return $this->getWorksheet()->getStyle($this->getCoordinate());
    }

    /**
     * Re-bind parent.
     *
     * @param Worksheet $parent
     *
     * @return Cell
     */
    public function rebindParent(Worksheet $parent)
    {
        $this->parent = $parent->getCellCollection();

        return $this->updateInCollection();
    }

    /**
     *    Is cell in a specific range?
     *
     * @param string $pRange Cell range (e.g. A1:A1)
     *
     * @return bool
     */
    public function isInRange($pRange)
    {
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange);

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
    public static function compareCells(self $a, self $b)
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
     *
     * @return IValueBinder
     */
    public static function getValueBinder()
    {
        if (self::$valueBinder === null) {
            self::$valueBinder = new DefaultValueBinder();
        }

        return self::$valueBinder;
    }

    /**
     * Set value binder to use.
     *
     * @param IValueBinder $binder
     */
    public static function setValueBinder(IValueBinder $binder)
    {
        self::$valueBinder = $binder;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != 'parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }

    /**
     * Get index to cellXf.
     *
     * @return int
     */
    public function getXfIndex()
    {
        return $this->xfIndex;
    }

    /**
     * Set index to cellXf.
     *
     * @param int $pValue
     *
     * @return Cell
     */
    public function setXfIndex($pValue)
    {
        $this->xfIndex = $pValue;

        return $this->updateInCollection();
    }

    /**
     * Set the formula attributes.
     *
     * @param mixed $pAttributes
     *
     * @return Cell
     */
    public function setFormulaAttributes($pAttributes)
    {
        $this->formulaAttributes = $pAttributes;

        return $this;
    }

    /**
     * Get the formula attributes.
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
