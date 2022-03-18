<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Information;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;

class ExcelError
{
    use ArrayEnabled;

    /**
     * List of error codes.
     *
     * @var array
     */
    public static $errorCodes = [
        'null' => '#NULL!',
        'divisionbyzero' => '#DIV/0!',
        'value' => '#VALUE!',
        'reference' => '#REF!',
        'name' => '#NAME?',
        'num' => '#NUM!',
        'na' => '#N/A',
        'gettingdata' => '#GETTING_DATA',
        'spill' => '#SPILL!',
    ];

    /**
     * ERROR_TYPE.
     *
     * @param mixed $value Value to check
     *
     * @return array|int|string
     */
    public static function type($value = '')
    {
        if (is_array($value)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $value);
        }

        $i = 1;
        foreach (self::$errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }

        if ($value === self::CALC()) {
            return 14;
        }

        return self::NA();
    }

    /**
     * NULL.
     *
     * Returns the error value #NULL!
     *
     * @return string #NULL!
     */
    public static function null()
    {
        return self::$errorCodes['null'];
    }

    /**
     * NaN.
     *
     * Returns the error value #NUM!
     *
     * @return string #NUM!
     */
    public static function NAN()
    {
        return self::$errorCodes['num'];
    }

    /**
     * REF.
     *
     * Returns the error value #REF!
     *
     * @return string #REF!
     */
    public static function REF()
    {
        return self::$errorCodes['reference'];
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
     * @return string #N/A!
     */
    public static function NA()
    {
        return self::$errorCodes['na'];
    }

    /**
     * VALUE.
     *
     * Returns the error value #VALUE!
     *
     * @return string #VALUE!
     */
    public static function VALUE()
    {
        return self::$errorCodes['value'];
    }

    /**
     * NAME.
     *
     * Returns the error value #NAME?
     *
     * @return string #NAME?
     */
    public static function NAME()
    {
        return self::$errorCodes['name'];
    }

    /**
     * DIV0.
     *
     * @return string #DIV/0!
     */
    public static function DIV0()
    {
        return self::$errorCodes['divisionbyzero'];
    }

    /**
     * CALC.
     *
     * @return string #Not Yet Implemented
     */
    public static function CALC()
    {
        return '#CALC!';
    }
}
