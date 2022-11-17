<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use DateTimeInterface;

/**
 * @deprecated 1.18.0
 */
class DateTime
{
    /**
     * Identify if a year is a leap year or not.
     *
     * @deprecated 1.18.0
     *      Use the isLeapYear method in the DateTimeExcel\Helpers class instead
     * @see DateTimeExcel\Helpers::isLeapYear()
     *
     * @param int|string $year The year to test
     *
     * @return bool TRUE if the year is a leap year, otherwise FALSE
     */
    public static function isLeapYear($year)
    {
        return DateTimeExcel\Helpers::isLeapYear($year);
    }

    /**
     * getDateValue.
     *
     * @deprecated 1.18.0
     *      Use the getDateValue method in the DateTimeExcel\Helpers class instead
     * @see DateTimeExcel\Helpers::getDateValue()
     *
     * @param mixed $dateValue
     *
     * @return mixed Excel date/time serial value, or string if error
     */
    public static function getDateValue($dateValue)
    {
        try {
            return DateTimeExcel\Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
     * @deprecated 1.18.0
     *      Use the now method in the DateTimeExcel\Current class instead
     * @see DateTimeExcel\Current::now()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATETIMENOW()
    {
        return DateTimeExcel\Current::now();
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
     * @deprecated 1.18.0
     *      Use the today method in the DateTimeExcel\Current class instead
     * @see DateTimeExcel\Current::today()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATENOW()
    {
        return DateTimeExcel\Current::today();
    }

    /**
     * DATE.
     *
     * The DATE function returns a value that represents a particular date.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     *
     * Excel Function:
     *        DATE(year,month,day)
     *
     * @deprecated 1.18.0
     *      Use the fromYMD method in the DateTimeExcel\Date class instead
     * @see DateTimeExcel\Date::fromYMD()
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
        return DateTimeExcel\Date::fromYMD($year, $month, $day);
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
     * @deprecated 1.18.0
     *      Use the fromHMS method in the DateTimeExcel\Time class instead
     * @see DateTimeExcel\Time::fromHMS()
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
        return DateTimeExcel\Time::fromHMS($hour, $minute, $second);
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
     * @deprecated 1.18.0
     *      Use the fromString method in the DateTimeExcel\DateValue class instead
     * @see DateTimeExcel\DateValue::fromString()
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
    public static function DATEVALUE($dateValue)
    {
        return DateTimeExcel\DateValue::fromString($dateValue);
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
     * @deprecated 1.18.0
     *      Use the fromString method in the DateTimeExcel\TimeValue class instead
     * @see DateTimeExcel\TimeValue::fromString()
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
        return DateTimeExcel\TimeValue::fromString($timeValue);
    }

    /**
     * DATEDIF.
     *
     * Excel Function:
     *        DATEDIF(startdate, enddate, unit)
     *
     * @deprecated 1.18.0
     *      Use the interval method in the DateTimeExcel\Difference class instead
     * @see DateTimeExcel\Difference::interval()
     *
     * @param mixed $startDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param mixed $endDate Excel date serial value, PHP date/time stamp, PHP DateTime object
     *                                    or a standard date string
     * @param array|string $unit
     *
     * @return array|int|string Interval between the dates
     */
    public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D')
    {
        return DateTimeExcel\Difference::interval($startDate, $endDate, $unit);
    }

    /**
     * DAYS.
     *
     * Returns the number of days between two dates
     *
     * Excel Function:
     *        DAYS(endDate, startDate)
     *
     * @deprecated 1.18.0
     *      Use the between method in the DateTimeExcel\Days class instead
     * @see DateTimeExcel\Days::between()
     *
     * @param array|DateTimeInterface|float|int|string $endDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     * @param array|DateTimeInterface|float|int|string $startDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     *
     * @return array|int|string Number of days between start date and end date or an error
     */
    public static function DAYS($endDate = 0, $startDate = 0)
    {
        return DateTimeExcel\Days::between($endDate, $startDate);
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
     * @deprecated 1.18.0
     *      Use the between method in the DateTimeExcel\Days360 class instead
     * @see DateTimeExcel\Days360::between()
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param array|bool $method US or European Method
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
     * @return array|int|string Number of days between start date and end date
     */
    public static function DAYS360($startDate = 0, $endDate = 0, $method = false)
    {
        return DateTimeExcel\Days360::between($startDate, $endDate, $method);
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
     *
     * @deprecated 1.18.0
     *      Use the fraction method in the DateTimeExcel\YearFrac class instead
     * @see DateTimeExcel\YearFrac::fraction()
     *
     * See https://lists.oasis-open.org/archives/office-formula/200806/msg00039.html
     *     for description of algorithm used in Excel
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param array|int $method Method used for the calculation
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return array|float|string fraction of the year, or a string containing an error
     */
    public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0)
    {
        return DateTimeExcel\YearFrac::fraction($startDate, $endDate, $method);
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
     * @deprecated 1.18.0
     *      Use the count method in the DateTimeExcel\NetworkDays class instead
     * @see DateTimeExcel\NetworkDays::count()
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     * @param mixed $dateArgs
     *
     * @return array|int|string Interval between the dates
     */
    public static function NETWORKDAYS($startDate, $endDate, ...$dateArgs)
    {
        return DateTimeExcel\NetworkDays::count($startDate, $endDate, ...$dateArgs);
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
     * @deprecated 1.18.0
     *      Use the date method in the DateTimeExcel\WorkDay class instead
     * @see DateTimeExcel\WorkDay::date()
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param int $endDays The number of nonweekend and nonholiday days before or after
     *                                        startDate. A positive value for days yields a future date; a
     *                                        negative value yields a past date.
     * @param mixed $dateArgs
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function WORKDAY($startDate, $endDays, ...$dateArgs)
    {
        return DateTimeExcel\WorkDay::date($startDate, $endDays, ...$dateArgs);
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
     * @deprecated 1.18.0
     *      Use the day method in the DateTimeExcel\DateParts class instead
     * @see DateTimeExcel\DateParts::day()
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return array|int|string Day of the month
     */
    public static function DAYOFMONTH($dateValue = 1)
    {
        return DateTimeExcel\DateParts::day($dateValue);
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
     * @deprecated 1.18.0
     *      Use the day method in the DateTimeExcel\Week class instead
     * @see DateTimeExcel\Week::day()
     *
     * @param float|int|string $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $style A number that determines the type of return value
     *                                        1 or omitted    Numbers 1 (Sunday) through 7 (Saturday).
     *                                        2                Numbers 1 (Monday) through 7 (Sunday).
     *                                        3                Numbers 0 (Monday) through 6 (Sunday).
     *
     * @return array|int|string Day of the week value
     */
    public static function WEEKDAY($dateValue = 1, $style = 1)
    {
        return DateTimeExcel\Week::day($dateValue, $style);
    }

    /**
     * STARTWEEK_SUNDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_SUNDAY
     * @see DateTimeExcel\Constants::STARTWEEK_SUNDAY
     */
    const STARTWEEK_SUNDAY = 1;

    /**
     * STARTWEEK_MONDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_MONDAY
     * @see DateTimeExcel\Constants::STARTWEEK_MONDAY
     */
    const STARTWEEK_MONDAY = 2;

    /**
     * STARTWEEK_MONDAY_ALT.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_MONDAY_ALT
     * @see DateTimeExcel\Constants::STARTWEEK_MONDAY_ALT
     */
    const STARTWEEK_MONDAY_ALT = 11;

    /**
     * STARTWEEK_TUESDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_TUESDAY
     * @see DateTimeExcel\Constants::STARTWEEK_TUESDAY
     */
    const STARTWEEK_TUESDAY = 12;

    /**
     * STARTWEEK_WEDNESDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_WEDNESDAY
     * @see DateTimeExcel\Constants::STARTWEEK_WEDNESDAY
     */
    const STARTWEEK_WEDNESDAY = 13;

    /**
     * STARTWEEK_THURSDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_THURSDAY
     * @see DateTimeExcel\Constants::STARTWEEK_THURSDAY
     */
    const STARTWEEK_THURSDAY = 14;

    /**
     * STARTWEEK_FRIDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_FRIDAY
     * @see DateTimeExcel\Constants::STARTWEEK_FRIDAY
     */
    const STARTWEEK_FRIDAY = 15;

    /**
     * STARTWEEK_SATURDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_SATURDAY
     * @see DateTimeExcel\Constants::STARTWEEK_SATURDAY
     */
    const STARTWEEK_SATURDAY = 16;

    /**
     * STARTWEEK_SUNDAY_ALT.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_SUNDAY_ALT
     * @see DateTimeExcel\Constants::STARTWEEK_SUNDAY_ALT
     */
    const STARTWEEK_SUNDAY_ALT = 17;

    /**
     * DOW_SUNDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_SUNDAY
     * @see DateTimeExcel\Constants::DOW_SUNDAY
     */
    const DOW_SUNDAY = 1;

    /**
     * DOW_MONDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_MONDAY
     * @see DateTimeExcel\Constants::DOW_MONDAY
     */
    const DOW_MONDAY = 2;

    /**
     * DOW_TUESDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_TUESDAY
     * @see DateTimeExcel\Constants::DOW_TUESDAY
     */
    const DOW_TUESDAY = 3;

    /**
     * DOW_WEDNESDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_WEDNESDAY
     * @see DateTimeExcel\Constants::DOW_WEDNESDAY
     */
    const DOW_WEDNESDAY = 4;

    /**
     * DOW_THURSDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_THURSDAY
     * @see DateTimeExcel\Constants::DOW_THURSDAY
     */
    const DOW_THURSDAY = 5;

    /**
     * DOW_FRIDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_FRIDAY
     * @see DateTimeExcel\Constants::DOW_FRIDAY
     */
    const DOW_FRIDAY = 6;

    /**
     * DOW_SATURDAY.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::DOW_SATURDAY
     * @see DateTimeExcel\Constants::DOW_SATURDAY
     */
    const DOW_SATURDAY = 7;

    /**
     * STARTWEEK_MONDAY_ISO.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::STARTWEEK_MONDAY_ISO
     * @see DateTimeExcel\Constants::STARTWEEK_MONDAY_ISO
     */
    const STARTWEEK_MONDAY_ISO = 21;

    /**
     * METHODARR.
     *
     * @deprecated 1.18.0
     *  Use DateTimeExcel\Constants::METHODARR
     * @see DateTimeExcel\Constants::METHODARR
     */
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
     * @deprecated 1.18.0
     *      Use the number method in the DateTimeExcel\Week class instead
     * @see DateTimeExcel\Week::number()
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
     * @return array|int|string Week Number
     */
    public static function WEEKNUM($dateValue = 1, $method = /** @scrutinizer ignore-deprecated */ self::STARTWEEK_SUNDAY)
    {
        return DateTimeExcel\Week::number($dateValue, $method);
    }

    /**
     * ISOWEEKNUM.
     *
     * Returns the ISO 8601 week number of the year for a specified date.
     *
     * Excel Function:
     *        ISOWEEKNUM(dateValue)
     *
     * @deprecated 1.18.0
     *      Use the isoWeekNumber method in the DateTimeExcel\Week class instead
     * @see DateTimeExcel\Week::isoWeekNumber()
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return array|int|string Week Number
     */
    public static function ISOWEEKNUM($dateValue = 1)
    {
        return DateTimeExcel\Week::isoWeekNumber($dateValue);
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
     * @deprecated 1.18.0
     *      Use the month method in the DateTimeExcel\DateParts class instead
     * @see DateTimeExcel\DateParts::month()
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return array|int|string Month of the year
     */
    public static function MONTHOFYEAR($dateValue = 1)
    {
        return DateTimeExcel\DateParts::month($dateValue);
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
     * @deprecated 1.18.0
     *      Use the ear method in the DateTimeExcel\DateParts class instead
     * @see DateTimeExcel\DateParts::year()
     *
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *
     * @return array|int|string Year
     */
    public static function YEAR($dateValue = 1)
    {
        return DateTimeExcel\DateParts::year($dateValue);
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
     * @deprecated 1.18.0
     *      Use the hour method in the DateTimeExcel\TimeParts class instead
     * @see DateTimeExcel\TimeParts::hour()
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return array|int|string Hour
     */
    public static function HOUROFDAY($timeValue = 0)
    {
        return DateTimeExcel\TimeParts::hour($timeValue);
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
     * @deprecated 1.18.0
     *      Use the minute method in the DateTimeExcel\TimeParts class instead
     * @see DateTimeExcel\TimeParts::minute()
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return array|int|string Minute
     */
    public static function MINUTE($timeValue = 0)
    {
        return DateTimeExcel\TimeParts::minute($timeValue);
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
     * @deprecated 1.18.0
     *      Use the second method in the DateTimeExcel\TimeParts class instead
     * @see DateTimeExcel\TimeParts::second()
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *
     * @return array|int|string Second
     */
    public static function SECOND($timeValue = 0)
    {
        return DateTimeExcel\TimeParts::second($timeValue);
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
     * @deprecated 1.18.0
     *      Use the adjust method in the DateTimeExcel\Edate class instead
     * @see DateTimeExcel\Month::adjust()
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
        return DateTimeExcel\Month::adjust($dateValue, $adjustmentMonths);
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
     * @deprecated 1.18.0
     *      Use the lastDay method in the DateTimeExcel\EoMonth class instead
     * @see DateTimeExcel\Month::lastDay()
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
        return DateTimeExcel\Month::lastDay($dateValue, $adjustmentMonths);
    }
}
