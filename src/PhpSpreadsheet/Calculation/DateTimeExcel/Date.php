<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Date
{
    use ArrayEnabled;

    /**
     * DATE.
     *
     * The DATE function returns a value that represents a particular date.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        DATE(year,month,day)
     *
     * PhpSpreadsheet is a lot more forgiving than MS Excel when passing non numeric values to this function.
     * A Month name or abbreviation (English only at this point) such as 'January' or 'Jan' will still be accepted,
     *     as will a day value with a suffix (e.g. '21st' rather than simply 21); again only English language.
     *
     * @param array|int $year The value of the year argument can include one to four digits.
     *                                Excel interprets the year argument according to the configured
     *                                date system: 1900 or 1904.
     *                                If year is between 0 (zero) and 1899 (inclusive), Excel adds that
     *                                value to 1900 to calculate the year. For example, DATE(108,1,2)
     *                                returns January 2, 2008 (1900+108).
     *                                If year is between 1900 and 9999 (inclusive), Excel uses that
     *                                value as the year. For example, DATE(2008,1,2) returns January 2,
     *                                2008.
     *                                If year is less than 0 or is 10000 or greater, Excel returns the
     *                                #NUM! error value.
     * @param array|int $month A positive or negative integer representing the month of the year
     *                                from 1 to 12 (January to December).
     *                                If month is greater than 12, month adds that number of months to
     *                                the first month in the year specified. For example, DATE(2008,14,2)
     *                                returns the serial number representing February 2, 2009.
     *                                If month is less than 1, month subtracts the magnitude of that
     *                                number of months, plus 1, from the first month in the year
     *                                specified. For example, DATE(2008,-3,2) returns the serial number
     *                                representing September 2, 2007.
     * @param array|int $day A positive or negative integer representing the day of the month
     *                                from 1 to 31.
     *                                If day is greater than the number of days in the month specified,
     *                                day adds that number of days to the first day in the month. For
     *                                example, DATE(2008,1,35) returns the serial number representing
     *                                February 4, 2008.
     *                                If day is less than 1, day subtracts the magnitude that number of
     *                                days, plus one, from the first day of the month specified. For
     *                                example, DATE(2008,1,-15) returns the serial number representing
     *                                December 16, 2007.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function fromYMD($year, $month, $day)
    {
        if (is_array($year) || is_array($month) || is_array($day)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $year, $month, $day);
        }

        $baseYear = SharedDateHelper::getExcelCalendar();

        try {
            $year = self::getYear($year, $baseYear);
            $month = self::getMonth($month);
            $day = self::getDay($day);
            self::adjustYearMonth($year, $month, $baseYear);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $excelDateValue = SharedDateHelper::formattedPHPToExcel($year, $month, $day);

        return Helpers::returnIn3FormatsFloat($excelDateValue);
    }

    /**
     * Convert year from multiple formats to int.
     *
     * @param mixed $year
     */
    private static function getYear($year, int $baseYear): int
    {
        $year = ($year !== null) ? StringHelper::testStringAsNumeric((string) $year) : 0;
        if (!is_numeric($year)) {
            throw new Exception(ExcelError::VALUE());
        }
        $year = (int) $year;

        if ($year < ($baseYear - 1900)) {
            throw new Exception(ExcelError::NAN());
        }
        if ((($baseYear - 1900) !== 0) && ($year < $baseYear) && ($year >= 1900)) {
            throw new Exception(ExcelError::NAN());
        }

        if (($year < $baseYear) && ($year >= ($baseYear - 1900))) {
            $year += 1900;
        }

        return (int) $year;
    }

    /**
     * Convert month from multiple formats to int.
     *
     * @param mixed $month
     */
    private static function getMonth($month): int
    {
        if (($month !== null) && (!is_numeric($month))) {
            $month = SharedDateHelper::monthStringToNumber($month);
        }

        $month = ($month !== null) ? StringHelper::testStringAsNumeric((string) $month) : 0;
        if (!is_numeric($month)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (int) $month;
    }

    /**
     * Convert day from multiple formats to int.
     *
     * @param mixed $day
     */
    private static function getDay($day): int
    {
        if (($day !== null) && (!is_numeric($day))) {
            $day = SharedDateHelper::dayStringToNumber($day);
        }

        $day = ($day !== null) ? StringHelper::testStringAsNumeric((string) $day) : 0;
        if (!is_numeric($day)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (int) $day;
    }

    private static function adjustYearMonth(int &$year, int &$month, int $baseYear): void
    {
        if ($month < 1) {
            //    Handle year/month adjustment if month < 1
            --$month;
            $year += ceil($month / 12) - 1;
            $month = 13 - abs($month % 12);
        } elseif ($month > 12) {
            //    Handle year/month adjustment if month > 12
            $year += floor($month / 12);
            $month = ($month % 12);
        }

        // Re-validate the year parameter after adjustments
        if (($year < $baseYear) || ($year >= 10000)) {
            throw new Exception(ExcelError::NAN());
        }
    }
}
