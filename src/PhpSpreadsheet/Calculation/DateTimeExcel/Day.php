<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Day
{
    /**
     * DAYOFMONTH.
     *
     * Returns the day of the month, for a specified date. The day is given as an integer
     * ranging from 1 to 31.
     *
     * Excel Function:
     *        DAY(dateValue)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return int|string Day of the month
     */
    public static function funcDay($dateValue)
    {
        $weirdResult = self::weirdCondition($dateValue);
        if ($weirdResult >= 0) {
            return $weirdResult;
        }

        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('j');
    }

    private static function weirdCondition($dateValue): int
    {
        // Excel does not treat 0 consistently for DAY vs. (MONTH or YEAR)
        if (Date::getExcelCalendar() === DATE::CALENDAR_WINDOWS_1900 && Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            if (is_bool($dateValue)) {
                return (int) $dateValue;
            }
            if ($dateValue === null) {
                return 0;
            }
            if (is_numeric($dateValue) && $dateValue < 1 && $dateValue >= 0) {
                return 0;
            }
        }

        return -1;
    }
}
