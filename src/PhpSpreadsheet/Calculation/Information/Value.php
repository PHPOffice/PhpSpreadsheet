<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class Value
{
    /**
     * IS_BLANK.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isBlank($value = null)
    {
        if ($value !== null) {
            $value = Functions::flattenSingleValue($value);
        }

        return $value === null;
    }

    /**
     * IS_EVEN.
     *
     * @param mixed $value Value to check
     *
     * @return bool|string
     */
    public static function isEven($value = null)
    {
        $value = Functions::flattenSingleValue($value);

        if ($value === null) {
            return ExcelError::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return ExcelError::VALUE();
        }

        return $value % 2 == 0;
    }

    /**
     * IS_ODD.
     *
     * @param mixed $value Value to check
     *
     * @return bool|string
     */
    public static function isOdd($value = null)
    {
        $value = Functions::flattenSingleValue($value);

        if ($value === null) {
            return ExcelError::NAME();
        } elseif ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return ExcelError::VALUE();
        }

        return abs($value) % 2 == 1;
    }

    /**
     * IS_NUMBER.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isNumber($value = null)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_string($value)) {
            return false;
        }

        return is_numeric($value);
    }

    /**
     * IS_LOGICAL.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isLogical($value = null)
    {
        $value = Functions::flattenSingleValue($value);

        return is_bool($value);
    }

    /**
     * IS_TEXT.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isText($value = null)
    {
        $value = Functions::flattenSingleValue($value);

        return is_string($value) && !self::isError($value);
    }

    /**
     * IS_NONTEXT.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isNonText($value = null)
    {
        return !self::isText($value);
    }

    /**
     * ISFORMULA.
     *
     * @param mixed $cellReference The cell to check
     * @param ?Cell $cell The current cell (containing this formula)
     *
     * @return bool|string
     */
    public static function isFormula($cellReference = '', ?Cell $cell = null)
    {
        if ($cell === null) {
            return ExcelError::REF();
        }
        $cellReference = Functions::expandDefinedName((string) $cellReference, $cell);
        $cellReference = Functions::trimTrailingRange($cellReference);

        preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $cellReference, $matches);

        $cellReference = $matches[6] . $matches[7];
        $worksheetName = str_replace("''", "'", trim($matches[2], "'"));

        $worksheet = (!empty($worksheetName))
            ? $cell->getWorksheet()->getParent()->getSheetByName($worksheetName)
            : $cell->getWorksheet();

        return ($worksheet !== null) ? $worksheet->getCell($cellReference)->isFormula() : ExcelError::REF();
    }

    /**
     * IS_ERR.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isErr($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        return self::isError($value) && (!self::isNa(($value)));
    }

    /**
     * IS_ERROR.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isError($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        if (!is_string($value)) {
            return false;
        }

        return in_array($value, ExcelError::$errorCodes);
    }

    /**
     * IS_NA.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function isNa($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        return $value === ExcelError::NA();
    }

    /**
     * N.
     *
     * Returns a value converted to a number
     *
     * @param null|mixed $value The value you want converted
     *
     * @return number N converts values listed in the following table
     *        If value is or refers to N returns
     *        A number            That number
     *        A date                The serial number of that date
     *        TRUE                1
     *        FALSE                0
     *        An error value        The error value
     *        Anything else        0
     */
    public static function asNumber($value = null)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
            case 'boolean':
                return (int) $value;
            case 'string':
                //    Errors
                if ((strlen($value) > 0) && ($value[0] == '#')) {
                    return $value;
                }

                break;
        }

        return 0;
    }

    /**
     * TYPE.
     *
     * Returns a number that identifies the type of a value
     *
     * @param null|mixed $value The value you want tested
     *
     * @return number N converts values listed in the following table
     *        If value is or refers to N returns
     *        A number            1
     *        Text                2
     *        Logical Value        4
     *        An error value        16
     *        Array or Matrix        64
     */
    public static function type($value = null)
    {
        $value = Functions::flattenArrayIndexed($value);
        if (is_array($value) && (count($value) > 1)) {
            end($value);
            $a = key($value);
            //    Range of cells is an error
            if (Functions::isCellValue($a)) {
                return 16;
            //    Test for Matrix
            } elseif (Functions::isMatrixValue($a)) {
                return 64;
            }
        } elseif (empty($value)) {
            //    Empty Cell
            return 1;
        }
        $value = Functions::flattenSingleValue($value);

        if (($value === null) || (is_float($value)) || (is_int($value))) {
            return 1;
        } elseif (is_bool($value)) {
            return 4;
        } elseif (is_array($value)) {
            return 64;
        } elseif (is_string($value)) {
            //    Errors
            if ((strlen($value) > 0) && ($value[0] == '#')) {
                return 16;
            }

            return 2;
        }

        return 0;
    }
}
