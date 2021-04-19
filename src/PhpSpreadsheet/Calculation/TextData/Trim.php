<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Trim
{
    private static $invalidChars;

    /**
     * TRIMNONPRINTABLE.
     *
     * @param mixed $stringValue String Value to check
     *
     * @return null|string
     */
    public static function nonPrintable($stringValue = '')
    {
        $stringValue = Functions::flattenSingleValue($stringValue);

        if (is_bool($stringValue)) {
            return ($stringValue) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        if (self::$invalidChars === null) {
            self::$invalidChars = range(chr(0), chr(31));
        }

        if (is_string($stringValue) || is_numeric($stringValue)) {
            return str_replace(self::$invalidChars, '', trim($stringValue, "\x00..\x1F"));
        }

        return null;
    }

    /**
     * TRIMSPACES.
     *
     * @param mixed $stringValue String Value to check
     *
     * @return null|string
     */
    public static function spaces($stringValue = '')
    {
        $stringValue = Functions::flattenSingleValue($stringValue);
        if (is_bool($stringValue)) {
            return ($stringValue) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        if (is_string($stringValue) || is_numeric($stringValue)) {
            return trim(preg_replace('/ +/', ' ', trim($stringValue, ' ')), ' ');
        }

        return null;
    }
}
