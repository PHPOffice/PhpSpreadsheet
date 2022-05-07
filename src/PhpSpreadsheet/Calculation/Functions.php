<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Functions
{
    const PRECISION = 8.88E-016;

    /**
     * 2 / PI.
     */
    const M_2DIVPI = 0.63661977236758134307553505349006;

    const COMPATIBILITY_EXCEL = 'Excel';
    const COMPATIBILITY_GNUMERIC = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE = 'OpenOfficeCalc';

    /** Use of RETURNDATE_PHP_NUMERIC is discouraged - not 32-bit Y2038-safe, no timezone. */
    const RETURNDATE_PHP_NUMERIC = 'P';
    /** Use of RETURNDATE_UNIX_TIMESTAMP is discouraged - not 32-bit Y2038-safe, no timezone. */
    const RETURNDATE_UNIX_TIMESTAMP = 'P';
    const RETURNDATE_PHP_OBJECT = 'O';
    const RETURNDATE_PHP_DATETIME_OBJECT = 'O';
    const RETURNDATE_EXCEL = 'E';

    /**
     * Compatibility mode to use for error checking and responses.
     *
     * @var string
     */
    protected static $compatibilityMode = self::COMPATIBILITY_EXCEL;

    /**
     * Data Type to use when returning date values.
     *
     * @var string
     */
    protected static $returnDateType = self::RETURNDATE_EXCEL;

    /**
     * Set the Compatibility Mode.
     *
     * @param string $compatibilityMode Compatibility Mode
     *                                  Permitted values are:
     *                                      Functions::COMPATIBILITY_EXCEL        'Excel'
     *                                      Functions::COMPATIBILITY_GNUMERIC     'Gnumeric'
     *                                      Functions::COMPATIBILITY_OPENOFFICE   'OpenOfficeCalc'
     *
     * @return bool (Success or Failure)
     */
    public static function setCompatibilityMode($compatibilityMode)
    {
        if (
            ($compatibilityMode == self::COMPATIBILITY_EXCEL) ||
            ($compatibilityMode == self::COMPATIBILITY_GNUMERIC) ||
            ($compatibilityMode == self::COMPATIBILITY_OPENOFFICE)
        ) {
            self::$compatibilityMode = $compatibilityMode;

            return true;
        }

        return false;
    }

    /**
     * Return the current Compatibility Mode.
     *
     * @return string Compatibility Mode
     *                Possible Return values are:
     *                    Functions::COMPATIBILITY_EXCEL        'Excel'
     *                    Functions::COMPATIBILITY_GNUMERIC     'Gnumeric'
     *                    Functions::COMPATIBILITY_OPENOFFICE   'OpenOfficeCalc'
     */
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }

    /**
     * Set the Return Date Format used by functions that return a date/time (Excel, PHP Serialized Numeric or PHP DateTime Object).
     *
     * @param string $returnDateType Return Date Format
     *                               Permitted values are:
     *                                   Functions::RETURNDATE_UNIX_TIMESTAMP       'P'
     *                                   Functions::RETURNDATE_PHP_DATETIME_OBJECT  'O'
     *                                   Functions::RETURNDATE_EXCEL                'E'
     *
     * @return bool Success or failure
     */
    public static function setReturnDateType($returnDateType)
    {
        if (
            ($returnDateType == self::RETURNDATE_UNIX_TIMESTAMP) ||
            ($returnDateType == self::RETURNDATE_PHP_DATETIME_OBJECT) ||
            ($returnDateType == self::RETURNDATE_EXCEL)
        ) {
            self::$returnDateType = $returnDateType;

            return true;
        }

        return false;
    }

    /**
     * Return the current Return Date Format for functions that return a date/time (Excel, PHP Serialized Numeric or PHP Object).
     *
     * @return string Return Date Format
     *                Possible Return values are:
     *                    Functions::RETURNDATE_UNIX_TIMESTAMP         'P'
     *                    Functions::RETURNDATE_PHP_DATETIME_OBJECT    'O'
     *                    Functions::RETURNDATE_EXCEL            '     'E'
     */
    public static function getReturnDateType()
    {
        return self::$returnDateType;
    }

    /**
     * DUMMY.
     *
     * @return string #Not Yet Implemented
     */
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }

    public static function isMatrixValue($idx)
    {
        return (substr_count($idx, '.') <= 1) || (preg_match('/\.[A-Z]/', $idx) > 0);
    }

    public static function isValue($idx)
    {
        return substr_count($idx, '.') === 0;
    }

    public static function isCellValue($idx)
    {
        return substr_count($idx, '.') > 1;
    }

    public static function ifCondition($condition)
    {
        $condition = self::flattenSingleValue($condition);

        if ($condition === '') {
            return '=""';
        }
        if (!is_string($condition) || !in_array($condition[0], ['>', '<', '='])) {
            $condition = self::operandSpecialHandling($condition);
            if (is_bool($condition)) {
                return '=' . ($condition ? 'TRUE' : 'FALSE');
            } elseif (!is_numeric($condition)) {
                if ($condition !== '""') { // Not an empty string
                    // Escape any quotes in the string value
                    $condition = preg_replace('/"/ui', '""', $condition);
                }
                $condition = Calculation::wrapResult(strtoupper($condition));
            }

            return str_replace('""""', '""', '=' . $condition);
        }
        preg_match('/(=|<[>=]?|>=?)(.*)/', $condition, $matches);
        [, $operator, $operand] = $matches;

        $operand = self::operandSpecialHandling($operand);
        if (is_numeric(trim($operand, '"'))) {
            $operand = trim($operand, '"');
        } elseif (!is_numeric($operand) && $operand !== 'FALSE' && $operand !== 'TRUE') {
            $operand = str_replace('"', '""', $operand);
            $operand = Calculation::wrapResult(strtoupper($operand));
        }

        return str_replace('""""', '""', $operator . $operand);
    }

    private static function operandSpecialHandling($operand)
    {
        if (is_numeric($operand) || is_bool($operand)) {
            return $operand;
        } elseif (strtoupper($operand) === Calculation::getTRUE() || strtoupper($operand) === Calculation::getFALSE()) {
            return strtoupper($operand);
        }

        // Check for percentage
        if (preg_match('/^\-?\d*\.?\d*\s?\%$/', $operand)) {
            return ((float) rtrim($operand, '%')) / 100;
        }

        // Check for dates
        if (($dateValueOperand = Date::stringToExcel($operand)) !== false) {
            return $dateValueOperand;
        }

        return $operand;
    }

    /**
     * NULL.
     *
     * Returns the error value #NULL!
     *
     * @Deprecated 1.23.0
     *
     * @return string #NULL!
     *
     *@see Information\ExcelError::null()
     * Use the null() method in the Information\Error class instead
     */
    public static function null()
    {
        return Information\ExcelError::null();
    }

    /**
     * NaN.
     *
     * Returns the error value #NUM!
     *
     * @Deprecated 1.23.0
     *
     * @return string #NUM!
     *
     * @see Information\ExcelError::NAN()
     * Use the NAN() method in the Information\Error class instead
     */
    public static function NAN()
    {
        return Information\ExcelError::NAN();
    }

    /**
     * REF.
     *
     * Returns the error value #REF!
     *
     * @Deprecated 1.23.0
     *
     * @return string #REF!
     *
     * @see Information\ExcelError::REF()
     * Use the REF() method in the Information\Error class instead
     */
    public static function REF()
    {
        return Information\ExcelError::REF();
    }

    /**
     * NA.
     *
     * Excel Function:
     *        =NA()
     *
     * Returns the error value #N/A
     *        #N/A is the error value that means "no value is available."
     *
     * @Deprecated 1.23.0
     *
     * @return string #N/A!
     *
     * @see Information\ExcelError::NA()
     * Use the NA() method in the Information\Error class instead
     */
    public static function NA()
    {
        return Information\ExcelError::NA();
    }

    /**
     * VALUE.
     *
     * Returns the error value #VALUE!
     *
     * @Deprecated 1.23.0
     *
     * @return string #VALUE!
     *
     * @see Information\ExcelError::VALUE()
     * Use the VALUE() method in the Information\Error class instead
     */
    public static function VALUE()
    {
        return Information\ExcelError::VALUE();
    }

    /**
     * NAME.
     *
     * Returns the error value #NAME?
     *
     * @Deprecated 1.23.0
     *
     * @return string #NAME?
     *
     * @see Information\ExcelError::NAME()
     * Use the NAME() method in the Information\Error class instead
     */
    public static function NAME()
    {
        return Information\ExcelError::NAME();
    }

    /**
     * DIV0.
     *
     * @Deprecated 1.23.0
     *
     * @return string #Not Yet Implemented
     *
     *@see Information\ExcelError::DIV0()
     * Use the DIV0() method in the Information\Error class instead
     */
    public static function DIV0()
    {
        return Information\ExcelError::DIV0();
    }

    /**
     * ERROR_TYPE.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @return array|int|string
     *
     * @see Information\ExcelError::type()
     * Use the type() method in the Information\Error class instead
     */
    public static function errorType($value = '')
    {
        return Information\ExcelError::type($value);
    }

    /**
     * IS_BLANK.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isBlank()
     * Use the isBlank() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isBlank($value = null)
    {
        return Information\Value::isBlank($value);
    }

    /**
     * IS_ERR.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isErr()
     * Use the isErr() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isErr($value = '')
    {
        return Information\ErrorValue::isErr($value);
    }

    /**
     * IS_ERROR.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isError()
     * Use the isError() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isError($value = '')
    {
        return Information\ErrorValue::isError($value);
    }

    /**
     * IS_NA.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isNa()
     * Use the isNa() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isNa($value = '')
    {
        return Information\ErrorValue::isNa($value);
    }

    /**
     * IS_EVEN.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isEven()
     * Use the isEven() method in the Information\Value class instead
     *
     * @return array|bool|string
     */
    public static function isEven($value = null)
    {
        return Information\Value::isEven($value);
    }

    /**
     * IS_ODD.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isOdd()
     * Use the isOdd() method in the Information\Value class instead
     *
     * @return array|bool|string
     */
    public static function isOdd($value = null)
    {
        return Information\Value::isOdd($value);
    }

    /**
     * IS_NUMBER.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isNumber()
     * Use the isNumber() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isNumber($value = null)
    {
        return Information\Value::isNumber($value);
    }

    /**
     * IS_LOGICAL.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isLogical()
     * Use the isLogical() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isLogical($value = null)
    {
        return Information\Value::isLogical($value);
    }

    /**
     * IS_TEXT.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isText()
     * Use the isText() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isText($value = null)
    {
        return Information\Value::isText($value);
    }

    /**
     * IS_NONTEXT.
     *
     * @param mixed $value Value to check
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isNonText()
     * Use the isNonText() method in the Information\Value class instead
     *
     * @return array|bool
     */
    public static function isNonText($value = null)
    {
        return Information\Value::isNonText($value);
    }

    /**
     * N.
     *
     * Returns a value converted to a number
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::asNumber()
     * Use the asNumber() method in the Information\Value class instead
     *
     * @param null|mixed $value The value you want converted
     *
     * @return number|string N converts values listed in the following table
     *        If value is or refers to N returns
     *        A number            That number
     *        A date                The serial number of that date
     *        TRUE                1
     *        FALSE                0
     *        An error value        The error value
     *        Anything else        0
     */
    public static function n($value = null)
    {
        return Information\Value::asNumber($value);
    }

    /**
     * TYPE.
     *
     * Returns a number that identifies the type of a value
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::type()
     * Use the type() method in the Information\Value class instead
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
    public static function TYPE($value = null)
    {
        return Information\Value::type($value);
    }

    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array.
     *
     * @param array|mixed $array Array to be flattened
     *
     * @return array Flattened array
     */
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }

        return $arrayValues;
    }

    /**
     * @param mixed $value
     *
     * @return null|mixed
     */
    public static function scalar($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        do {
            $value = array_pop($value);
        } while (is_array($value));

        return $value;
    }

    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array, but retain an element of indexing.
     *
     * @param array|mixed $array Array to be flattened
     *
     * @return array Flattened array
     */
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = [];
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1 . '.' . $k2 . '.' . $k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1 . '.' . $k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }

        return $arrayValues;
    }

    /**
     * Convert an array to a single scalar value by extracting the first element.
     *
     * @param mixed $value Array or scalar value
     *
     * @return mixed
     */
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        return $value;
    }

    /**
     * ISFORMULA.
     *
     * @Deprecated 1.23.0
     *
     * @see Information\Value::isFormula()
     * Use the isFormula() method in the Information\Value class instead
     *
     * @param mixed $cellReference The cell to check
     * @param ?Cell $cell The current cell (containing this formula)
     *
     * @return array|bool|string
     */
    public static function isFormula($cellReference = '', ?Cell $cell = null)
    {
        return Information\Value::isFormula($cellReference, $cell);
    }

    public static function expandDefinedName(string $coordinate, Cell $cell): string
    {
        $worksheet = $cell->getWorksheet();
        $spreadsheet = $worksheet->getParent();
        // Uppercase coordinate
        $pCoordinatex = strtoupper($coordinate);
        // Eliminate leading equal sign
        $pCoordinatex = (string) preg_replace('/^=/', '', $pCoordinatex);
        $defined = $spreadsheet->getDefinedName($pCoordinatex, $worksheet);
        if ($defined !== null) {
            $worksheet2 = $defined->getWorkSheet();
            if (!$defined->isFormula() && $worksheet2 !== null) {
                $coordinate = "'" . $worksheet2->getTitle() . "'!" .
                    (string) preg_replace('/^=/', '', $defined->getValue());
            }
        }

        return $coordinate;
    }

    public static function trimTrailingRange(string $coordinate): string
    {
        return (string) preg_replace('/:[\\w\$]+$/', '', $coordinate);
    }

    public static function trimSheetFromCellReference(string $coordinate): string
    {
        if (strpos($coordinate, '!') !== false) {
            $coordinate = substr($coordinate, strrpos($coordinate, '!') + 1);
        }

        return $coordinate;
    }
}
