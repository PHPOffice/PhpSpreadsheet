<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class CaseConvert
{
    /**
     * LOWERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param mixed (string) $mixedCaseValue
     */
    public static function lower($mixedCaseValue): string
    {
        $mixedCaseValue = Functions::flattenSingleValue($mixedCaseValue);

        if (is_bool($mixedCaseValue)) {
            $mixedCaseValue = ($mixedCaseValue === true) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToLower($mixedCaseValue);
    }

    /**
     * UPPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param mixed (string) $mixedCaseValue
     */
    public static function upper($mixedCaseValue): string
    {
        $mixedCaseValue = Functions::flattenSingleValue($mixedCaseValue);

        if (is_bool($mixedCaseValue)) {
            $mixedCaseValue = ($mixedCaseValue === true) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToUpper($mixedCaseValue);
    }

    /**
     * PROPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param mixed (string) $mixedCaseValue
     */
    public static function proper($mixedCaseValue): string
    {
        $mixedCaseValue = Functions::flattenSingleValue($mixedCaseValue);

        if (is_bool($mixedCaseValue)) {
            $mixedCaseValue = ($mixedCaseValue === true) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToTitle($mixedCaseValue);
    }
}
