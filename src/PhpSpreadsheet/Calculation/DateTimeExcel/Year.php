<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class Year
{
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
    public static function funcYear($dateValue)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($dateValue < 1 && Date::getExcelCalendar() === DATE::CALENDAR_WINDOWS_1900) {
            return 1900;
        }
        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('Y');
    }
}
