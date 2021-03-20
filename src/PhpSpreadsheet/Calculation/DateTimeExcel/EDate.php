<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EDate
{
    /**
     * EDATE.
     *
     * Returns the serial number that represents the date that is the indicated number of months
     * before or after a specified date (the start_date).
     * Use EDATE to calculate maturity dates or due dates that fall on the same day of the month
     * as the date of issue.
     *
     * Excel Function:
     *        EDATE(dateValue,adjustmentMonths)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param int $adjustmentMonths The number of months before or after start_date.
     *                                        A positive value for months yields a future date;
     *                                        a negative value yields a past date.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function funcEDate($dateValue, $adjustmentMonths)
    {
        try {
            $dateValue = Helpers::getDateValue($dateValue, false);
            $adjustmentMonths = Helpers::validateNumericNull($adjustmentMonths);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        // Execute function
        $PHPDateObject = Helpers::adjustDateByMonths($dateValue, $adjustmentMonths);

        return Helpers::returnIn3FormatsObject($PHPDateObject);
    }
}
