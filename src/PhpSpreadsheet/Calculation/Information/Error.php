<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Error
{
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
    ];

    /**
     * ERROR_TYPE.
     *
     * @param mixed $value Value to check
     *
     * @return int|string
     */
    public static function type($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        $i = 1;
        foreach (self::$errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }

        return Functions::NA();
    }
}
