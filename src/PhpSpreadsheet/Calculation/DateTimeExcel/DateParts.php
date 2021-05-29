<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class DateParts
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
    public static function day($dateValue)
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
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('j');
    }

    /**
     * MONTHOFYEAR.
     *
     * Returns the month of a date represented by a serial number.
     * The month is given as an integer, ranging from 1 (January) to 12 (December).
     *
     * Excel Function:
     *        MONTH(dateValue)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return int|string Month of the year
     */
    public static function month($dateValue)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($dateValue < 1 && SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_WINDOWS_1900) {
            return 1;
        }

        // Execute function
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('n');
    }

    /**
     * YEAR.
     *
     * Returns the year corresponding to a date.
     * The year is returned as an integer in the range 1900-9999.
     *
     * Excel Function:
     *        YEAR(dateValue)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return int|string Year
     */
    public static function year($dateValue)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($dateValue < 1 && SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_WINDOWS_1900) {
            return 1900;
        }
        // Execute function
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('Y');
    }

    /**
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     */
    private static function weirdCondition($dateValue): int
    {
        // Excel does not treat 0 consistently for DAY vs. (MONTH or YEAR)
        if (SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_WINDOWS_1900 && Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
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
