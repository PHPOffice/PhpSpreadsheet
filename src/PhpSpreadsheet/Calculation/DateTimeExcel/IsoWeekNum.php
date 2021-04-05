<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class IsoWeekNum
{
    /**
     * ISOWEEKNUM.
     *
     * Returns the ISO 8601 week number of the year for a specified date.
     *
     * Excel Function:
     *        ISOWEEKNUM(dateValue)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return int|string Week Number
     */
    public static function funcIsoWeekNum($dateValue)
    {
        if (self::apparentBug($dateValue)) {
            return 52;
        }

        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        Helpers::silly1900($PHPDateObject);

        return (int) $PHPDateObject->format('W');
    }

    private static function apparentBug($dateValue): bool
    {
        if (Date::getExcelCalendar() !== DATE::CALENDAR_MAC_1904) {
            if (is_bool($dateValue)) {
                return true;
            }
            if (is_numeric($dateValue) && !((int) $dateValue)) {
                return true;
            }
        }

        return false;
    }
}
