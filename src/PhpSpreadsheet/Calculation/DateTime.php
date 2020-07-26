<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use DateTimeImmutable;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DateTime
{
    /**
     * Identify if a year is a leap year or not.
     *
     * @param int|string $year The year to test
     *
     * @return bool TRUE if the year is a leap year, otherwise FALSE
     */
    public static function isLeapYear($year)
    {
        return (($year % 4) === 0) && (($year % 100) !== 0) || (($year % 400) === 0);
    }

    /**
     * Return the number of days between two dates based on a 360 day calendar.
     *
     * @param int $startDay Day of month of the start date
     * @param int $startMonth Month of the start date
     * @param int $startYear Year of the start date
     * @param int $endDay Day of month of the start date
     * @param int $endMonth Month of the start date
     * @param int $endYear Year of the start date
     * @param bool $methodUS Whether to use the US method or the European method of calculation
     *
     * @return int Number of days between the start date and the end date
     */
    private static function dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $methodUS)
    {
        if ($startDay == 31) {
            --$startDay;
        } elseif ($methodUS && ($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !self::isLeapYear($startYear))))) {
            $startDay = 30;
        }
        if ($endDay == 31) {
            if ($methodUS && $startDay != 30) {
                $endDay = 1;
                if ($endMonth == 12) {
                    ++$endYear;
                    $endMonth = 1;
                } else {
                    ++$endMonth;
                }
            } else {
                $endDay = 30;
            }
        }

        return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
    }

    /**
     * getDateValue.
     *
     * @param mixed $dateValue
     *
     * @return mixed Excel date/time serial value, or string if error
     */
    public static function getDateValue($dateValue)
    {
        if (!is_numeric($dateValue)) {
            if ((is_object($dateValue)) && ($dateValue instanceof DateTimeInterface)) {
                $dateValue = Date::PHPToExcel($dateValue);
            } else {
                $saveReturnDateType = Functions::getReturnDateType();
                Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
                $dateValue = self::DATEVALUE($dateValue);
                Functions::setReturnDateType($saveReturnDateType);
            }
        }

        return $dateValue;
    }

    /**
     * getTimeValue.
     *
     * @param string $timeValue
     *
     * @return mixed Excel date/time serial value, or string if error
     */
    private static function getTimeValue($timeValue)
    {
        $saveReturnDateType = Functions::getReturnDateType();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        $timeValue = self::TIMEVALUE($timeValue);
        Functions::setReturnDateType($saveReturnDateType);

        return $timeValue;
    }

    private static function adjustDateByMonths($dateValue = 0, $adjustmentMonths = 0)
    {
        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        $oMonth = (int) $PHPDateObject->format('m');
        $oYear = (int) $PHPDateObject->format('Y');

        $adjustmentMonthsString = (string) $adjustmentMonths;
        if ($adjustmentMonths > 0) {
            $adjustmentMonthsString = '+' . $adjustmentMonths;
        }
        if ($adjustmentMonths != 0) {
            $PHPDateObject->modify($adjustmentMonthsString . ' months');
        }
        $nMonth = (int) $PHPDateObject->format('m');
        $nYear = (int) $PHPDateObject->format('Y');

        $monthDiff = ($nMonth - $oMonth) + (($nYear - $oYear) * 12);
        if ($monthDiff != $adjustmentMonths) {
            $adjustDays = (int) $PHPDateObject->format('d');
            $adjustDaysString = '-' . $adjustDays . ' days';
            $PHPDateObject->modify($adjustDaysString);
        }

        return $PHPDateObject;
    }

    /**
     * DATETIMENOW.
     *
     * Returns the current date and time.
     * The NOW function is useful when you need to display the current date and time on a worksheet or
     * calculate a value based on the current date and time, and have that value updated each time you
     * open the worksheet.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * and time format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        NOW()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATETIMENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $retValue = (float) Date::PHPToExcel(time());

                break;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                $retValue = (int) time();

                break;
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $retValue = new \DateTime();

                break;
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }

    /**
     * DATENOW.
     *
     * Returns the current date.
     * The NOW function is useful when you need to display the current date and time on a worksheet or
     * calculate a value based on the current date and time, and have that value updated each time you
     * open the worksheet.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * and time format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        TODAY()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        $excelDateTime = floor(Date::PHPToExcel(time()));
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $retValue = (float) $excelDateTime;

                break;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                $retValue = (int) Date::excelToTimestamp($excelDateTime);

                break;
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $retValue = Date::excelToDateTimeObject($excelDateTime);

                break;
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }

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
     * @param int $year The value of the year argument can include one to four digits.
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
     * @param int $month A positive or negative integer representing the month of the year
     *                                from 1 to 12 (January to December).
     *                                If month is greater than 12, month adds that number of months to
     *                                the first month in the year specified. For example, DATE(2008,14,2)
     *                                returns the serial number representing February 2, 2009.
     *                                If month is less than 1, month subtracts the magnitude of that
     *                                number of months, plus 1, from the first month in the year
     *                                specified. For example, DATE(2008,-3,2) returns the serial number
     *                                representing September 2, 2007.
     * @param int $day A positive or negative integer representing the day of the month
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
     */
    public static function DATE($year = 0, $month = 1, $day = 1)
    {
        $year = Functions::flattenSingleValue($year);
        $month = Functions::flattenSingleValue($month);
        $day = Functions::flattenSingleValue($day);

        if (($month !== null) && (!is_numeric($month))) {
            $month = Date::monthStringToNumber($month);
        }

        if (($day !== null) && (!is_numeric($day))) {
            $day = Date::dayStringToNumber($day);
        }

        $year = ($year !== null) ? StringHelper::testStringAsNumeric($year) : 0;
        $month = ($month !== null) ? StringHelper::testStringAsNumeric($month) : 0;
        $day = ($day !== null) ? StringHelper::testStringAsNumeric($day) : 0;
        if (
            (!is_numeric($year)) ||
            (!is_numeric($month)) ||
            (!is_numeric($day))
        ) {
            return Functions::VALUE();
        }
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;

        $baseYear = Date::getExcelCalendar();
        // Validate parameters
        if ($year < ($baseYear - 1900)) {
            return Functions::NAN();
        }
        if ((($baseYear - 1900) != 0) && ($year < $baseYear) && ($year >= 1900)) {
            return Functions::NAN();
        }

        if (($year < $baseYear) && ($year >= ($baseYear - 1900))) {
            $year += 1900;
        }

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
            return Functions::NAN();
        }

        // Execute function
        $excelDateValue = Date::formattedPHPToExcel($year, $month, $day);
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) $excelDateValue;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp($excelDateValue);
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return Date::excelToDateTimeObject($excelDateValue);
        }
    }

    /**
     * TIME.
     *
     * The TIME function returns a value that represents a particular time.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the time
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        TIME(hour,minute,second)
     *
     * @param int $hour A number from 0 (zero) to 32767 representing the hour.
     *                                    Any value greater than 23 will be divided by 24 and the remainder
     *                                    will be treated as the hour value. For example, TIME(27,0,0) =
     *                                    TIME(3,0,0) = .125 or 3:00 AM.
     * @param int $minute A number from 0 to 32767 representing the minute.
     *                                    Any value greater than 59 will be converted to hours and minutes.
     *                                    For example, TIME(0,750,0) = TIME(12,30,0) = .520833 or 12:30 PM.
     * @param int $second A number from 0 to 32767 representing the second.
     *                                    Any value greater than 59 will be converted to hours, minutes,
     *                                    and seconds. For example, TIME(0,0,2000) = TIME(0,33,22) = .023148
     *                                    or 12:33:20 AM
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function TIME($hour = 0, $minute = 0, $second = 0)
    {
        $hour = Functions::flattenSingleValue($hour);
        $minute = Functions::flattenSingleValue($minute);
        $second = Functions::flattenSingleValue($second);

        if ($hour == '') {
            $hour = 0;
        }
        if ($minute == '') {
            $minute = 0;
        }
        if ($second == '') {
            $second = 0;
        }

        if ((!is_numeric($hour)) || (!is_numeric($minute)) || (!is_numeric($second))) {
            return Functions::VALUE();
        }
        $hour = (int) $hour;
        $minute = (int) $minute;
        $second = (int) $second;

        if ($second < 0) {
            $minute += floor($second / 60);
            $second = 60 - abs($second % 60);
            if ($second == 60) {
                $second = 0;
            }
        } elseif ($second >= 60) {
            $minute += floor($second / 60);
            $second = $second % 60;
        }
        if ($minute < 0) {
            $hour += floor($minute / 60);
            $minute = 60 - abs($minute % 60);
            if ($minute == 60) {
                $minute = 0;
            }
        } elseif ($minute >= 60) {
            $hour += floor($minute / 60);
            $minute = $minute % 60;
        }

        if ($hour > 23) {
            $hour = $hour % 24;
        } elseif ($hour < 0) {
            return Functions::NAN();
        }

        // Execute function
        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                $date = 0;
                $calendar = Date::getExcelCalendar();
                if ($calendar != Date::CALENDAR_WINDOWS_1900) {
                    $date = 1;
                }

                return (float) Date::formattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::formattedPHPToExcel(1970, 1, 1, $hour, $minute, $second)); // -2147468400; //    -2147472000 + 3600
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                $dayAdjust = 0;
                if ($hour < 0) {
                    $dayAdjust = floor($hour / 24);
                    $hour = 24 - abs($hour % 24);
                    if ($hour == 24) {
                        $hour = 0;
                    }
                } elseif ($hour >= 24) {
                    $dayAdjust = floor($hour / 24);
                    $hour = $hour % 24;
                }
                $phpDateObject = new \DateTime('1900-01-01 ' . $hour . ':' . $minute . ':' . $second);
                if ($dayAdjust != 0) {
                    $phpDateObject->modify($dayAdjust . ' days');
                }

                return $phpDateObject;
        }
    }

    /**
     * DATEVALUE.
     *
     * Returns a value that represents a particular date.
     * Use DATEVALUE to convert a date represented by a text string to an Excel or PHP date/time stamp
     * value.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        DATEVALUE(dateValue)
     *
     * @param string $dateValue Text that represents a date in a Microsoft Excel date format.
     *                                    For example, "1/30/2008" or "30-Jan-2008" are text strings within
     *                                    quotation marks that represent dates. Using the default date
     *                                    system in Excel for Windows, date_text must represent a date from
     *                                    January 1, 1900, to December 31, 9999. Using the default date
     *                                    system in Excel for the Macintosh, date_text must represent a date
     *                                    from January 1, 1904, to December 31, 9999. DATEVALUE returns the
     *                                    #VALUE! error value if date_text is out of this range.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATEVALUE($dateValue = 1)
    {
        $dateValue = trim(Functions::flattenSingleValue($dateValue), '"');
        //    Strip any ordinals because they're allowed in Excel (English only)
        $dateValue = preg_replace('/(\d)(st|nd|rd|th)([ -\/])/Ui', '$1$3', $dateValue);
        //    Convert separators (/ . or space) to hyphens (should also handle dot used for ordinals in some countries, e.g. Denmark, Germany)
        $dateValue = str_replace(['/', '.', '-', '  '], ' ', $dateValue);

        $yearFound = false;
        $t1 = explode(' ', $dateValue);
        foreach ($t1 as &$t) {
            if ((is_numeric($t)) && ($t > 31)) {
                if ($yearFound) {
                    return Functions::VALUE();
                }
                if ($t < 100) {
                    $t += 1900;
                }
                $yearFound = true;
            }
        }
        if ((count($t1) == 1) && (strpos($t, ':') !== false)) {
            //    We've been fed a time value without any date
            return 0.0;
        } elseif (count($t1) == 2) {
            //    We only have two parts of the date: either day/month or month/year
            if ($yearFound) {
                array_unshift($t1, 1);
            } else {
                if ($t1[1] > 29) {
                    $t1[1] += 1900;
                    array_unshift($t1, 1);
                } else {
                    $t1[] = date('Y');
                }
            }
        }
        unset($t);
        $dateValue = implode(' ', $t1);

        $PHPDateArray = date_parse($dateValue);
        if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
            $testVal1 = strtok($dateValue, '- ');
            if ($testVal1 !== false) {
                $testVal2 = strtok('- ');
                if ($testVal2 !== false) {
                    $testVal3 = strtok('- ');
                    if ($testVal3 === false) {
                        $testVal3 = strftime('%Y');
                    }
                } else {
                    return Functions::VALUE();
                }
            } else {
                return Functions::VALUE();
            }
            if ($testVal1 < 31 && $testVal2 < 12 && $testVal3 < 12 && strlen($testVal3) == 2) {
                $testVal3 += 2000;
            }
            $PHPDateArray = date_parse($testVal1 . '-' . $testVal2 . '-' . $testVal3);
            if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
                $PHPDateArray = date_parse($testVal2 . '-' . $testVal1 . '-' . $testVal3);
                if (($PHPDateArray === false) || ($PHPDateArray['error_count'] > 0)) {
                    return Functions::VALUE();
                }
            }
        }

        if (($PHPDateArray !== false) && ($PHPDateArray['error_count'] == 0)) {
            // Execute function
            if ($PHPDateArray['year'] == '') {
                $PHPDateArray['year'] = strftime('%Y');
            }
            if ($PHPDateArray['year'] < 1900) {
                return Functions::VALUE();
            }
            if ($PHPDateArray['month'] == '') {
                $PHPDateArray['month'] = strftime('%m');
            }
            if ($PHPDateArray['day'] == '') {
                $PHPDateArray['day'] = strftime('%d');
            }
            if (!checkdate($PHPDateArray['month'], $PHPDateArray['day'], $PHPDateArray['year'])) {
                return Functions::VALUE();
            }
            $excelDateValue = floor(
                Date::formattedPHPToExcel(
                    $PHPDateArray['year'],
                    $PHPDateArray['month'],
                    $PHPDateArray['day'],
                    $PHPDateArray['hour'],
                    $PHPDateArray['minute'],
                    $PHPDateArray['second']
                )
            );
            switch (Functions::getReturnDateType()) {
                case Functions::RETURNDATE_EXCEL:
                    return (float) $excelDateValue;
                case Functions::RETURNDATE_UNIX_TIMESTAMP:
                    return (int) Date::excelToTimestamp($excelDateValue);
                case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                    return new \DateTime($PHPDateArray['year'] . '-' . $PHPDateArray['month'] . '-' . $PHPDateArray['day'] . ' 00:00:00');
            }
        }

        return Functions::VALUE();
    }

    /**
     * TIMEVALUE.
     *
     * Returns a value that represents a particular time.
     * Use TIMEVALUE to convert a time represented by a text string to an Excel or PHP date/time stamp
     * value.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the time
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * Excel Function:
     *        TIMEVALUE(timeValue)
     *
     * @param string $timeValue A text string that represents a time in any one of the Microsoft
     *                                    Excel time formats; for example, "6:45 PM" and "18:45" text strings
     *                                    within quotation marks that represent time.
     *                                    Date information in time_text is ignored.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function TIMEVALUE($timeValue)
    {
        $timeValue = trim(Functions::flattenSingleValue($timeValue), '"');
        $timeValue = str_replace(['/', '.'], '-', $timeValue);

        $arraySplit = preg_split('/[\/:\-\s]/', $timeValue);
        if ((count($arraySplit) == 2 || count($arraySplit) == 3) && $arraySplit[0] > 24) {
            $arraySplit[0] = ($arraySplit[0] % 24);
            $timeValue = implode(':', $arraySplit);
        }

        $PHPDateArray = date_parse($timeValue);
        if (($PHPDateArray !== false) && ($PHPDateArray['error_count'] == 0)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $excelDateValue = Date::formattedPHPToExcel(
                    $PHPDateArray['year'],
                    $PHPDateArray['month'],
                    $PHPDateArray['day'],
                    $PHPDateArray['hour'],
                    $PHPDateArray['minute'],
                    $PHPDateArray['second']
                );
            } else {
                $excelDateValue = Date::formattedPHPToExcel(1900, 1, 1, $PHPDateArray['hour'], $PHPDateArray['minute'], $PHPDateArray['second']) - 1;
            }

            switch (Functions::getReturnDateType()) {
                case Functions::RETURNDATE_EXCEL:
                    return (float) $excelDateValue;
                case Functions::RETURNDATE_UNIX_TIMESTAMP:
                    return (int) $phpDateValue = Date::excelToTimestamp($excelDateValue + 25569) - 3600;
                case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                    return new \DateTime('1900-01-01 ' . $PHPDateArray['hour'] . ':' . $PHPDateArray['minute'] . ':' . $PHPDateArray['second']);
            }
        }

        return Functions::VALUE();
    }

    /**
     * DATEDIF.
     *
     * @param mixed $startDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param mixed $endDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param string $unit
     *
     * @return int|string Interval between the dates
     */
    public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D')
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        $unit = strtoupper(Functions::flattenSingleValue($unit));

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }

        // Validate parameters
        if ($startDate > $endDate) {
            return Functions::NAN();
        }

        // Execute function
        $difference = $endDate - $startDate;

        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $startDays = $PHPStartDateObject->format('j');
        $startMonths = $PHPStartDateObject->format('n');
        $startYears = $PHPStartDateObject->format('Y');

        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);
        $endDays = $PHPEndDateObject->format('j');
        $endMonths = $PHPEndDateObject->format('n');
        $endYears = $PHPEndDateObject->format('Y');

        $PHPDiffDateObject = $PHPEndDateObject->diff($PHPStartDateObject);

        switch ($unit) {
            case 'D':
                $retVal = (int) $difference;

                break;
            case 'M':
                $retVal = (int) 12 * $PHPDiffDateObject->format('%y') + $PHPDiffDateObject->format('%m');

                break;
            case 'Y':
                $retVal = (int) $PHPDiffDateObject->format('%y');

                break;
            case 'MD':
                if ($endDays < $startDays) {
                    $retVal = $endDays;
                    $PHPEndDateObject->modify('-' . $endDays . ' days');
                    $adjustDays = $PHPEndDateObject->format('j');
                    $retVal += ($adjustDays - $startDays);
                } else {
                    $retVal = (int) $PHPDiffDateObject->format('%d');
                }

                break;
            case 'YM':
                $retVal = (int) $PHPDiffDateObject->format('%m');

                break;
            case 'YD':
                $retVal = (int) $difference;
                if ($endYears > $startYears) {
                    $isLeapStartYear = $PHPStartDateObject->format('L');
                    $wasLeapEndYear = $PHPEndDateObject->format('L');

                    // Adjust end year to be as close as possible as start year
                    while ($PHPEndDateObject >= $PHPStartDateObject) {
                        $PHPEndDateObject->modify('-1 year');
                        $endYears = $PHPEndDateObject->format('Y');
                    }
                    $PHPEndDateObject->modify('+1 year');

                    // Get the result
                    $retVal = $PHPEndDateObject->diff($PHPStartDateObject)->days;

                    // Adjust for leap years cases
                    $isLeapEndYear = $PHPEndDateObject->format('L');
                    $limit = new \DateTime($PHPEndDateObject->format('Y-02-29'));
                    if (!$isLeapStartYear && !$wasLeapEndYear && $isLeapEndYear && $PHPEndDateObject >= $limit) {
                        --$retVal;
                    }
                }

                break;
            default:
                $retVal = Functions::VALUE();
        }

        return $retVal;
    }

    /**
     * DAYS.
     *
     * Returns the number of days between two dates
     *
     * Excel Function:
     *        DAYS(endDate, startDate)
     *
     * @param DateTimeImmutable|float|int|string $endDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     * @param DateTimeImmutable|float|int|string $startDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     *
     * @return int|string Number of days between start date and end date or an error
     */
    public static function DAYS($endDate = 0, $startDate = 0)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);

        $startDate = self::getDateValue($startDate);
        if (is_string($startDate)) {
            return Functions::VALUE();
        }

        $endDate = self::getDateValue($endDate);
        if (is_string($endDate)) {
            return Functions::VALUE();
        }

        // Execute function
        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);

        $diff = $PHPStartDateObject->diff($PHPEndDateObject);
        $days = $diff->days;

        if ($diff->invert) {
            $days = -$days;
        }

        return $days;
    }

    /**
     * DAYS360.
     *
     * Returns the number of days between two dates based on a 360-day year (twelve 30-day months),
     * which is used in some accounting calculations. Use this function to help compute payments if
     * your accounting system is based on twelve 30-day months.
     *
     * Excel Function:
     *        DAYS360(startDate,endDate[,method])
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param bool $method US or European Method
     *                                        FALSE or omitted: U.S. (NASD) method. If the starting date is
     *                                        the last day of a month, it becomes equal to the 30th of the
     *                                        same month. If the ending date is the last day of a month and
     *                                        the starting date is earlier than the 30th of a month, the
     *                                        ending date becomes equal to the 1st of the next month;
     *                                        otherwise the ending date becomes equal to the 30th of the
     *                                        same month.
     *                                        TRUE: European method. Starting dates and ending dates that
     *                                        occur on the 31st of a month become equal to the 30th of the
     *                                        same month.
     *
     * @return int|string Number of days between start date and end date
     */
    public static function DAYS360($startDate = 0, $endDate = 0, $method = false)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }

        if (!is_bool($method)) {
            return Functions::VALUE();
        }

        // Execute function
        $PHPStartDateObject = Date::excelToDateTimeObject($startDate);
        $startDay = $PHPStartDateObject->format('j');
        $startMonth = $PHPStartDateObject->format('n');
        $startYear = $PHPStartDateObject->format('Y');

        $PHPEndDateObject = Date::excelToDateTimeObject($endDate);
        $endDay = $PHPEndDateObject->format('j');
        $endMonth = $PHPEndDateObject->format('n');
        $endYear = $PHPEndDateObject->format('Y');

        return self::dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, !$method);
    }

    /**
     * YEARFRAC.
     *
     * Calculates the fraction of the year represented by the number of whole days between two dates
     * (the start_date and the end_date).
     * Use the YEARFRAC worksheet function to identify the proportion of a whole year's benefits or
     * obligations to assign to a specific term.
     *
     * Excel Function:
     *        YEARFRAC(startDate,endDate[,method])
     * See https://lists.oasis-open.org/archives/office-formula/200806/msg00039.html
     *     for description of algorithm used in Excel
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $method Method used for the calculation
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string fraction of the year, or a string containing an error
     */
    public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0)
    {
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        $method = Functions::flattenSingleValue($method);

        if (is_string($startDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        if (is_string($endDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }
        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        if (((is_numeric($method)) && (!is_string($method))) || ($method == '')) {
            switch ($method) {
                case 0:
                    return self::DAYS360($startDate, $endDate) / 360;
                case 1:
                    $days = self::DATEDIF($startDate, $endDate);
                    $startYear = self::YEAR($startDate);
                    $endYear = self::YEAR($endDate);
                    $years = $endYear - $startYear + 1;
                    $startMonth = self::MONTHOFYEAR($startDate);
                    $startDay = self::DAYOFMONTH($startDate);
                    $endMonth = self::MONTHOFYEAR($endDate);
                    $endDay = self::DAYOFMONTH($endDate);
                    $startMonthDay = 100 * $startMonth + $startDay;
                    $endMonthDay = 100 * $endMonth + $endDay;
                    if ($years == 1) {
                        if (self::isLeapYear($endYear)) {
                            $tmpCalcAnnualBasis = 366;
                        } else {
                            $tmpCalcAnnualBasis = 365;
                        }
                    } elseif ($years == 2 && $startMonthDay >= $endMonthDay) {
                        if (self::isLeapYear($startYear)) {
                            if ($startMonthDay <= 229) {
                                $tmpCalcAnnualBasis = 366;
                            } else {
                                $tmpCalcAnnualBasis = 365;
                            }
                        } elseif (self::isLeapYear($endYear)) {
                            if ($endMonthDay >= 229) {
                                $tmpCalcAnnualBasis = 366;
                            } else {
                                $tmpCalcAnnualBasis = 365;
                            }
                        } else {
                            $tmpCalcAnnualBasis = 365;
                        }
                    } else {
                        $tmpCalcAnnualBasis = 0;
                        for ($year = $startYear; $year <= $endYear; ++$year) {
                            $tmpCalcAnnualBasis += self::isLeapYear($year) ? 366 : 365;
                        }
                        $tmpCalcAnnualBasis /= $years;
                    }

                    return $days / $tmpCalcAnnualBasis;
                case 2:
                    return self::DATEDIF($startDate, $endDate) / 360;
                case 3:
                    return self::DATEDIF($startDate, $endDate) / 365;
                case 4:
                    return self::DAYS360($startDate, $endDate, true) / 360;
            }
        }

        return Functions::VALUE();
    }

    /**
     * NETWORKDAYS.
     *
     * Returns the number of whole working days between start_date and end_date. Working days
     * exclude weekends and any dates identified in holidays.
     * Use NETWORKDAYS to calculate employee benefits that accrue based on the number of days
     * worked during a specific term.
     *
     * Excel Function:
     *        NETWORKDAYS(startDate,endDate[,holidays[,holiday[,...]]])
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     *
     * @return int|string Interval between the dates
     */
    public static function NETWORKDAYS($startDate, $endDate, ...$dateArgs)
    {
        //    Retrieve the mandatory start and end date that are referenced in the function definition
        $startDate = Functions::flattenSingleValue($startDate);
        $endDate = Functions::flattenSingleValue($endDate);
        //    Get the optional days
        $dateArgs = Functions::flattenArray($dateArgs);

        //    Validate the start and end dates
        if (is_string($startDate = $sDate = self::getDateValue($startDate))) {
            return Functions::VALUE();
        }
        $startDate = (float) floor($startDate);
        if (is_string($endDate = $eDate = self::getDateValue($endDate))) {
            return Functions::VALUE();
        }
        $endDate = (float) floor($endDate);

        if ($sDate > $eDate) {
            $startDate = $eDate;
            $endDate = $sDate;
        }

        // Execute function
        $startDoW = 6 - self::WEEKDAY($startDate, 2);
        if ($startDoW < 0) {
            $startDoW = 0;
        }
        $endDoW = self::WEEKDAY($endDate, 2);
        if ($endDoW >= 6) {
            $endDoW = 0;
        }

        $wholeWeekDays = floor(($endDate - $startDate) / 7) * 5;
        $partWeekDays = $endDoW + $startDoW;
        if ($partWeekDays > 5) {
            $partWeekDays -= 5;
        }

        //    Test any extra holiday parameters
        $holidayCountedArray = [];
        foreach ($dateArgs as $holidayDate) {
            if (is_string($holidayDate = self::getDateValue($holidayDate))) {
                return Functions::VALUE();
            }
            if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                if ((self::WEEKDAY($holidayDate, 2) < 6) && (!in_array($holidayDate, $holidayCountedArray))) {
                    --$partWeekDays;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
        }

        if ($sDate > $eDate) {
            return 0 - ($wholeWeekDays + $partWeekDays);
        }

        return $wholeWeekDays + $partWeekDays;
    }

    /**
     * WORKDAY.
     *
     * Returns the date that is the indicated number of working days before or after a date (the
     * starting date). Working days exclude weekends and any dates identified as holidays.
     * Use WORKDAY to exclude weekends or holidays when you calculate invoice due dates, expected
     * delivery times, or the number of days of work performed.
     *
     * Excel Function:
     *        WORKDAY(startDate,endDays[,holidays[,holiday[,...]]])
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param int $endDays The number of nonweekend and nonholiday days before or after
     *                                        startDate. A positive value for days yields a future date; a
     *                                        negative value yields a past date.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function WORKDAY($startDate, $endDays, ...$dateArgs)
    {
        //    Retrieve the mandatory start date and days that are referenced in the function definition
        $startDate = Functions::flattenSingleValue($startDate);
        $endDays = Functions::flattenSingleValue($endDays);
        //    Get the optional days
        $dateArgs = Functions::flattenArray($dateArgs);

        if ((is_string($startDate = self::getDateValue($startDate))) || (!is_numeric($endDays))) {
            return Functions::VALUE();
        }
        $startDate = (float) floor($startDate);
        $endDays = (int) floor($endDays);
        //    If endDays is 0, we always return startDate
        if ($endDays == 0) {
            return $startDate;
        }

        $decrementing = $endDays < 0;

        //    Adjust the start date if it falls over a weekend

        $startDoW = self::WEEKDAY($startDate, 3);
        if (self::WEEKDAY($startDate, 3) >= 5) {
            $startDate += ($decrementing) ? -$startDoW + 4 : 7 - $startDoW;
            ($decrementing) ? $endDays++ : $endDays--;
        }

        //    Add endDays
        $endDate = (float) $startDate + ((int) ($endDays / 5) * 7) + ($endDays % 5);

        //    Adjust the calculated end date if it falls over a weekend
        $endDoW = self::WEEKDAY($endDate, 3);
        if ($endDoW >= 5) {
            $endDate += ($decrementing) ? -$endDoW + 4 : 7 - $endDoW;
        }

        //    Test any extra holiday parameters
        if (!empty($dateArgs)) {
            $holidayCountedArray = $holidayDates = [];
            foreach ($dateArgs as $holidayDate) {
                if (($holidayDate !== null) && (trim($holidayDate) > '')) {
                    if (is_string($holidayDate = self::getDateValue($holidayDate))) {
                        return Functions::VALUE();
                    }
                    if (self::WEEKDAY($holidayDate, 3) < 5) {
                        $holidayDates[] = $holidayDate;
                    }
                }
            }
            if ($decrementing) {
                rsort($holidayDates, SORT_NUMERIC);
            } else {
                sort($holidayDates, SORT_NUMERIC);
            }
            foreach ($holidayDates as $holidayDate) {
                if ($decrementing) {
                    if (($holidayDate <= $startDate) && ($holidayDate >= $endDate)) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            --$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                } else {
                    if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            ++$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                }
                //    Adjust the calculated end date if it falls over a weekend
                $endDoW = self::WEEKDAY($endDate, 3);
                if ($endDoW >= 5) {
                    $endDate += ($decrementing) ? -$endDoW + 4 : 7 - $endDoW;
                }
            }
        }

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) $endDate;
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp($endDate);
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return Date::excelToDateTimeObject($endDate);
        }
    }

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
    public static function DAYOFMONTH($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            if ($dateValue < 0.0) {
                return Functions::NAN();
            } elseif ($dateValue < 1.0) {
                return 0;
            }
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('j');
    }

    /**
     * WEEKDAY.
     *
     * Returns the day of the week for a specified date. The day is given as an integer
     * ranging from 0 to 7 (dependent on the requested style).
     *
     * Excel Function:
     *        WEEKDAY(dateValue[,style])
     *
     * @param int $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $style A number that determines the type of return value
     *                                        1 or omitted    Numbers 1 (Sunday) through 7 (Saturday).
     *                                        2                Numbers 1 (Monday) through 7 (Sunday).
     *                                        3                Numbers 0 (Monday) through 6 (Sunday).
     *
     * @return int|string Day of the week value
     */
    public static function WEEKDAY($dateValue = 1, $style = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $style = Functions::flattenSingleValue($style);

        if (!is_numeric($style)) {
            return Functions::VALUE();
        } elseif (($style < 1) || ($style > 3)) {
            return Functions::NAN();
        }
        $style = floor($style);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        $DoW = (int) $PHPDateObject->format('w');

        $firstDay = 1;
        switch ($style) {
            case 1:
                ++$DoW;

                break;
            case 2:
                if ($DoW === 0) {
                    $DoW = 7;
                }

                break;
            case 3:
                if ($DoW === 0) {
                    $DoW = 7;
                }
                $firstDay = 0;
                --$DoW;

                break;
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            //    Test for Excel's 1900 leap year, and introduce the error as required
            if (($PHPDateObject->format('Y') == 1900) && ($PHPDateObject->format('n') <= 2)) {
                --$DoW;
                if ($DoW < $firstDay) {
                    $DoW += 7;
                }
            }
        }

        return $DoW;
    }

    const STARTWEEK_SUNDAY = 1;
    const STARTWEEK_MONDAY = 2;
    const STARTWEEK_MONDAY_ALT = 11;
    const STARTWEEK_TUESDAY = 12;
    const STARTWEEK_WEDNESDAY = 13;
    const STARTWEEK_THURSDAY = 14;
    const STARTWEEK_FRIDAY = 15;
    const STARTWEEK_SATURDAY = 16;
    const STARTWEEK_SUNDAY_ALT = 17;
    const DOW_SUNDAY = 1;
    const DOW_MONDAY = 2;
    const DOW_TUESDAY = 3;
    const DOW_WEDNESDAY = 4;
    const DOW_THURSDAY = 5;
    const DOW_FRIDAY = 6;
    const DOW_SATURDAY = 7;
    const STARTWEEK_MONDAY_ISO = 21;
    const METHODARR = [
        self::STARTWEEK_SUNDAY => self::DOW_SUNDAY,
        self::DOW_MONDAY,
        self::STARTWEEK_MONDAY_ALT => self::DOW_MONDAY,
        self::DOW_TUESDAY,
        self::DOW_WEDNESDAY,
        self::DOW_THURSDAY,
        self::DOW_FRIDAY,
        self::DOW_SATURDAY,
        self::DOW_SUNDAY,
        self::STARTWEEK_MONDAY_ISO => self::STARTWEEK_MONDAY_ISO,
    ];

    /**
     * WEEKNUM.
     *
     * Returns the week of the year for a specified date.
     * The WEEKNUM function considers the week containing January 1 to be the first week of the year.
     * However, there is a European standard that defines the first week as the one with the majority
     * of days (four or more) falling in the new year. This means that for years in which there are
     * three days or less in the first week of January, the WEEKNUM function returns week numbers
     * that are incorrect according to the European standard.
     *
     * Excel Function:
     *        WEEKNUM(dateValue[,style])
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $method Week begins on Sunday or Monday
     *                                        1 or omitted    Week begins on Sunday.
     *                                        2                Week begins on Monday.
     *                                        11               Week begins on Monday.
     *                                        12               Week begins on Tuesday.
     *                                        13               Week begins on Wednesday.
     *                                        14               Week begins on Thursday.
     *                                        15               Week begins on Friday.
     *                                        16               Week begins on Saturday.
     *                                        17               Week begins on Sunday.
     *                                        21               ISO (Jan. 4 is week 1, begins on Monday).
     *
     * @return int|string Week Number
     */
    public static function WEEKNUM($dateValue = 1, $method = self::STARTWEEK_SUNDAY)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $method = Functions::flattenSingleValue($method);

        if (!is_numeric($method)) {
            return Functions::VALUE();
        }
        $method = (int) $method;
        if (!array_key_exists($method, self::METHODARR)) {
            return Functions::NaN();
        }
        $method = self::METHODARR[$method];

        $dateValue = self::getDateValue($dateValue);
        if (is_string($dateValue)) {
            return Functions::VALUE();
        }
        if ($dateValue < 0.0) {
            return Functions::NAN();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        if ($method == self::STARTWEEK_MONDAY_ISO) {
            return (int) $PHPDateObject->format('W');
        }
        $dayOfYear = $PHPDateObject->format('z');
        $PHPDateObject->modify('-' . $dayOfYear . ' days');
        $firstDayOfFirstWeek = $PHPDateObject->format('w');
        $daysInFirstWeek = (6 - $firstDayOfFirstWeek + $method) % 7;
        $daysInFirstWeek += 7 * !$daysInFirstWeek;
        $endFirstWeek = $daysInFirstWeek - 1;
        $weekOfYear = floor(($dayOfYear - $endFirstWeek + 13) / 7);

        return (int) $weekOfYear;
    }

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
    public static function ISOWEEKNUM($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('W');
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
    public static function MONTHOFYEAR($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if (empty($dateValue)) {
            $dateValue = 1;
        }
        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

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
    public static function YEAR($dateValue = 1)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);

        if ($dateValue === null) {
            $dateValue = 1;
        } elseif (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        } elseif ($dateValue < 0.0) {
            return Functions::NAN();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);

        return (int) $PHPDateObject->format('Y');
    }

    /**
     * HOUROFDAY.
     *
     * Returns the hour of a time value.
     * The hour is given as an integer, ranging from 0 (12:00 A.M.) to 23 (11:00 P.M.).
     *
     * Excel Function:
     *        HOUR(timeValue)
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return int|string Hour
     */
    public static function HOUROFDAY($timeValue = 0)
    {
        $timeValue = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        // Execute function
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('G', $timeValue);
    }

    /**
     * MINUTE.
     *
     * Returns the minutes of a time value.
     * The minute is given as an integer, ranging from 0 to 59.
     *
     * Excel Function:
     *        MINUTE(timeValue)
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return int|string Minute
     */
    public static function MINUTE($timeValue = 0)
    {
        $timeValue = $timeTester = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        // Execute function
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('i', $timeValue);
    }

    /**
     * SECOND.
     *
     * Returns the seconds of a time value.
     * The second is given as an integer in the range 0 (zero) to 59.
     *
     * Excel Function:
     *        SECOND(timeValue)
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return int|string Second
     */
    public static function SECOND($timeValue = 0)
    {
        $timeValue = Functions::flattenSingleValue($timeValue);

        if (!is_numeric($timeValue)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return Functions::VALUE();
                }
            }
            $timeValue = self::getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return Functions::VALUE();
            }
        }
        // Execute function
        if ($timeValue >= 1) {
            $timeValue = fmod($timeValue, 1);
        } elseif ($timeValue < 0.0) {
            return Functions::NAN();
        }
        $timeValue = Date::excelToTimestamp($timeValue);

        return (int) gmdate('s', $timeValue);
    }

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
    public static function EDATE($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = Functions::flattenSingleValue($adjustmentMonths);

        if (!is_numeric($adjustmentMonths)) {
            return Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        // Execute function
        $PHPDateObject = self::adjustDateByMonths($dateValue, $adjustmentMonths);

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) Date::PHPToExcel($PHPDateObject);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::PHPToExcel($PHPDateObject));
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return $PHPDateObject;
        }
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
     * @param int $adjustmentMonths The number of months before or after start_date.
     *                                        A positive value for months yields a future date;
     *                                        a negative value yields a past date.
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function EOMONTH($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = Functions::flattenSingleValue($adjustmentMonths);

        if (!is_numeric($adjustmentMonths)) {
            return Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);

        if (is_string($dateValue = self::getDateValue($dateValue))) {
            return Functions::VALUE();
        }

        // Execute function
        $PHPDateObject = self::adjustDateByMonths($dateValue, $adjustmentMonths + 1);
        $adjustDays = (int) $PHPDateObject->format('d');
        $adjustDaysString = '-' . $adjustDays . ' days';
        $PHPDateObject->modify($adjustDaysString);

        switch (Functions::getReturnDateType()) {
            case Functions::RETURNDATE_EXCEL:
                return (float) Date::PHPToExcel($PHPDateObject);
            case Functions::RETURNDATE_UNIX_TIMESTAMP:
                return (int) Date::excelToTimestamp(Date::PHPToExcel($PHPDateObject));
            case Functions::RETURNDATE_PHP_DATETIME_OBJECT:
                return $PHPDateObject;
        }
    }
}
