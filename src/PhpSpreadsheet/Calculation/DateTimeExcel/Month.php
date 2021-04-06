<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Month
{
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
    public static function funcMonth($dateValue)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        if ($dateValue < 1 && Date::getExcelCalendar() === DATE::CALENDAR_WINDOWS_1900) {
            return 1;
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('n');
    }
}
