<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Month
{
    use ArrayEnabled;

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
     *                         Or can be an array of date values
     * @param array|int $adjustmentMonths The number of months before or after start_date.
     *                                        A positive value for months yields a future date;
     *                                        a negative value yields a past date.
     *                         Or can be an array of adjustment values
     *
     * @return array|mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function adjust($dateValue, $adjustmentMonths)
    {
        if (is_array($dateValue) || is_array($adjustmentMonths)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $dateValue, $adjustmentMonths);
        }

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

    /**
     * EOMONTH.
     *
     * Returns the date value for the last day of the month that is the indicated number of months
     * before or after start_date.
     * Use EOMONTH to calculate maturity dates or due dates that fall on the last day of the month.
     *
     * Excel Function:
     *        EOMONTH(dateValue,adjustmentMonths)
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     *                         Or can be an array of date values
     * @param array|int $adjustmentMonths The number of months before or after start_date.
     *                                        A positive value for months yields a future date;
     *                                        a negative value yields a past date.
     *                         Or can be an array of adjustment values
     *
     * @return array|mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function lastDay($dateValue, $adjustmentMonths)
    {
        if (is_array($dateValue) || is_array($adjustmentMonths)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $dateValue, $adjustmentMonths);
        }

        try {
            $dateValue = Helpers::getDateValue($dateValue, false);
            $adjustmentMonths = Helpers::validateNumericNull($adjustmentMonths);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        // Execute function
        $PHPDateObject = Helpers::adjustDateByMonths($dateValue, $adjustmentMonths + 1);
        $adjustDays = (int) $PHPDateObject->format('d');
        $adjustDaysString = '-' . $adjustDays . ' days';
        $PHPDateObject->modify($adjustDaysString);

        return Helpers::returnIn3FormatsObject($PHPDateObject);
    }
}
