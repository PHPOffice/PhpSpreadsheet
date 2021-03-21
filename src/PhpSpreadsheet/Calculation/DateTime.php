<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use DateTimeInterface;

class DateTime
{
    /**
     * Identify if a year is a leap year or not.
     *
     * @Deprecated 2.0.0 Use the method isLeapYear in the DateTimeExcel\Helpers class instead
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
     * @Deprecated 2.0.0 Use the method getDateValueNoThrow in the DateTimeExcel\Helpers class instead
     *
     * @param mixed $dateValue
     *
     * @return mixed Excel date/time serial value, or string if error
     */
    public static function getDateValue($dateValue)
    {
        return DateTimeExcel\Helpers::getDateValueNoThrow($dateValue);
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
     * @Deprecated 2.0.0 Use the funcNow method in the DateTimeExcel\Now class instead
     *
     * Excel Function:
     *        NOW()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATETIMENOW()
    {
        return DateTimeExcel\Now::funcNow();
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
     * @Deprecated 2.0.0 Use the funcToday method in the DateTimeExcel\Today class instead
     *
     * Excel Function:
     *        TODAY()
     *
     * @return mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     */
    public static function DATENOW()
    {
        return DateTimeExcel\Today::funcToday();
    }

    /**
     * DATE.
     *
     * The DATE function returns a value that represents a particular date.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the date
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * @Deprecated 2.0.0 Use the funcDate method in the DateTimeExcel\Date class instead
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
        return DateTimeExcel\Datefunc::funcDate($year, $month, $day);
    }

    /**
     * TIME.
     *
     * The TIME function returns a value that represents a particular time.
     *
     * NOTE: When used in a Cell Formula, MS Excel changes the cell format so that it matches the time
     * format of your regional settings. PhpSpreadsheet does not change cell formatting in this way.
     *
     * @Deprecated 2.0.0 Use the funcTime method in the DateTimeExcel\Time class instead
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
        return DateTimeExcel\Time::funcTime($hour, $minute, $second);
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
     * @Deprecated 2.0.0 Use the funcDateValue method in the DateTimeExcel\DateValue class instead
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
        return DateTimeExcel\DateValue::funcDateValue($dateValue);
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
     * @Deprecated 2.0.0 Use the funcTimeValue method in the DateTimeExcel\TimeValue class instead
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
        return DateTimeExcel\TimeValue::funcTimeValue($timeValue);
    }

    /**
     * DATEDIF.
     *
     * @Deprecated 2.0.0 Use the funcDateDif method in the DateTimeExcel\DateDif class instead
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
        return DateTimeExcel\DateDif::funcDateDif($startDate, $endDate, $unit);
    }

    /**
     * DAYS.
     *
     * Returns the number of days between two dates
     *
     * @Deprecated 2.0.0 Use the funcDays method in the DateTimeExcel\Days class instead
     *
     * Excel Function:
     *        DAYS(endDate, startDate)
     *
     * @param DateTimeInterface|float|int|string $endDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     * @param DateTimeInterface|float|int|string $startDate Excel date serial value (float),
     * PHP date timestamp (integer), PHP DateTime object, or a standard date string
     *
     * @return int|string Number of days between start date and end date or an error
     */
    public static function DAYS($endDate = 0, $startDate = 0)
    {
        return DateTimeExcel\Days::funcDays($endDate, $startDate);
    }

    /**
     * DAYS360.
     *
     * Returns the number of days between two dates based on a 360-day year (twelve 30-day months),
     * which is used in some accounting calculations. Use this function to help compute payments if
     * your accounting system is based on twelve 30-day months.
     *
     * @Deprecated 2.0.0 Use the funcDays360 method in the DateTimeExcel\Days360 class instead
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
        return DateTimeExcel\Days360::funcDays360($startDate, $endDate, $method);
    }

    /**
     * YEARFRAC.
     *
     * Calculates the fraction of the year represented by the number of whole days between two dates
     * (the start_date and the end_date).
     * Use the YEARFRAC worksheet function to identify the proportion of a whole year's benefits or
     * obligations to assign to a specific term.
     *
     * @Deprecated 2.0.0 Use the funcYearFrac method in the DateTimeExcel\YearFrac class instead
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
        return DateTimeExcel\YearFrac::funcYearFrac($startDate, $endDate, $method);
    }

    /**
     * NETWORKDAYS.
     *
     * Returns the number of whole working days between start_date and end_date. Working days
     * exclude weekends and any dates identified in holidays.
     * Use NETWORKDAYS to calculate employee benefits that accrue based on the number of days
     * worked during a specific term.
     *
     * @Deprecated 2.0.0 Use the funcNetworkDays method in the DateTimeExcel\NetworkDays class instead
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
        return DateTimeExcel\NetworkDays::funcNetworkDays($startDate, $endDate, ...$dateArgs);
    }

    /**
     * WORKDAY.
     *
     * Returns the date that is the indicated number of working days before or after a date (the
     * starting date). Working days exclude weekends and any dates identified as holidays.
     * Use WORKDAY to exclude weekends or holidays when you calculate invoice due dates, expected
     * delivery times, or the number of days of work performed.
     *
     * @Deprecated 2.0.0 Use the funcWorkDay method in the DateTimeExcel\WorkDay class instead
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
        return DateTimeExcel\WorkDay::funcWorkDay($startDate, $endDays, ...$dateArgs);
    }

    /**
     * DAYOFMONTH.
     *
     * Returns the day of the month, for a specified date. The day is given as an integer
     * ranging from 1 to 31.
     *
     * @Deprecated 2.0.0 Use the funcDay method in the DateTimeExcel\Day class instead
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
        return DateTimeExcel\Day::funcDay($dateValue);
    }

    /**
     * WEEKDAY.
     *
     * Returns the day of the week for a specified date. The day is given as an integer
     * ranging from 0 to 7 (dependent on the requested style).
     *
     * @Deprecated 2.0.0 Use the funcWeekDay method in the DateTimeExcel\WeekDay class instead
     *
     * Excel Function:
     *        WEEKDAY(dateValue[,style])
     *
     * @param float|int|string $dateValue Excel date serial value (float), PHP date timestamp (integer),
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
        return DateTimeExcel\WeekDay::funcWeekDay($dateValue, $style);
    }

    /**
     * STARTWEEK_SUNDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_SUNDAY instead
     */
    const STARTWEEK_SUNDAY = 1;

    /**
     * STARTWEEK_MONDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_MONDAY instead
     */
    const STARTWEEK_MONDAY = 2;

    /**
     * STARTWEEK_MONDAY_ALT.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_MONDAY_ALT instead
     */
    const STARTWEEK_MONDAY_ALT = 11;

    /**
     * STARTWEEK_TUESDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_TUESDAY instead
     */
    const STARTWEEK_TUESDAY = 12;

    /**
     * STARTWEEK_WEDNESDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_WEDNESDAY instead
     */
    const STARTWEEK_WEDNESDAY = 13;

    /**
     * STARTWEEK_THURSDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_THURSDAY instead
     */
    const STARTWEEK_THURSDAY = 14;

    /**
     * STARTWEEK_FRIDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_FRIDAY instead
     */
    const STARTWEEK_FRIDAY = 15;

    /**
     * STARTWEEK_SATURDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_SATURDAY instead
     */
    const STARTWEEK_SATURDAY = 16;

    /**
     * STARTWEEK_SUNDAY_ALT.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_SUNDAY_ALT instead
     */
    const STARTWEEK_SUNDAY_ALT = 17;

    /**
     * DOW_SUNDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_SUNDAY instead
     */
    const DOW_SUNDAY = 1;

    /**
     * DOW_MONDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_MONDAY instead
     */
    const DOW_MONDAY = 2;

    /**
     * DOW_TUESDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_TUESDAY instead
     */
    const DOW_TUESDAY = 3;

    /**
     * DOW_WEDNESDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_WEDNESDAY instead
     */
    const DOW_WEDNESDAY = 4;

    /**
     * DOW_THURSDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_THURSDAY instead
     */
    const DOW_THURSDAY = 5;

    /**
     * DOW_FRIDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_FRIDAY instead
     */
    const DOW_FRIDAY = 6;

    /**
     * DOW_SATURDAY.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\DOW_SATURDAY instead
     */
    const DOW_SATURDAY = 7;

    /**
     * STARTWEEK_MONDAY_ISO.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\STARTWEEK_MONDAY_ISO instead
     */
    const STARTWEEK_MONDAY_ISO = 21;

    /**
     * METHODARR.
     *
     * @Deprecated 2.0.0
     *
     * @see Use DateTimeExcel\Constants\METHODARR instead
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
     * @Deprecated 2.0.0 Use the funcWeekNum method in the DateTimeExcel\WeekNum class instead
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
        return DateTimeExcel\WeekNum::funcWeekNum($dateValue, $method);
    }

    /**
     * ISOWEEKNUM.
     *
     * Returns the ISO 8601 week number of the year for a specified date.
     *
     * @Deprecated 2.0.0 Use the funcIsoWeeknum method in the DateTimeExcel\Isoweeknum class instead
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
        return DateTimeExcel\IsoweekNum::funcIsoWeekNum($dateValue);
    }

    /**
     * MONTHOFYEAR.
     *
     * Returns the month of a date represented by a serial number.
     * The month is given as an integer, ranging from 1 (January) to 12 (December).
     *
     * @Deprecated 2.0.0 Use the funcMonth method in the DateTimeExcel\Month class instead
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
        return DateTimeExcel\Month::funcMonth($dateValue);
    }

    /**
     * YEAR.
     *
     * Returns the year corresponding to a date.
     * The year is returned as an integer in the range 1900-9999.
     *
     * @Deprecated 2.0.0 Use the funcYear method in the DateTimeExcel\Year class instead
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
        return DateTimeExcel\Year::funcYear($dateValue);
    }

    /**
     * HOUROFDAY.
     *
     * Returns the hour of a time value.
     * The hour is given as an integer, ranging from 0 (12:00 A.M.) to 23 (11:00 P.M.).
     *
     * @Deprecated 2.0.0 Use the funcHour method in the DateTimeExcel\Hour class instead
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
        return DateTimeExcel\Hour::funcHour($timeValue);
    }

    /**
     * MINUTE.
     *
     * Returns the minutes of a time value.
     * The minute is given as an integer, ranging from 0 to 59.
     *
     * @Deprecated 2.0.0 Use the funcMinute method in the DateTimeExcel\Minute class instead
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
        return DateTimeExcel\Minute::funcMinute($timeValue);
    }

    /**
     * SECOND.
     *
     * Returns the seconds of a time value.
     * The second is given as an integer in the range 0 (zero) to 59.
     *
     * @Deprecated 2.0.0 Use the funcSecond method in the DateTimeExcel\Second class instead
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
        return DateTimeExcel\Second::funcSecond($timeValue);
    }

    /**
     * EDATE.
     *
     * Returns the serial number that represents the date that is the indicated number of months
     * before or after a specified date (the start_date).
     * Use EDATE to calculate maturity dates or due dates that fall on the same day of the month
     * as the date of issue.
     *
     * @Deprecated 2.0.0 Use the funcEDate method in the DateTimeExcel\EDate class instead
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
        return DateTimeExcel\EDate::funcEDate($dateValue, $adjustmentMonths);
    }

    /**
     * EOMONTH.
     *
     * Returns the date value for the last day of the month that is the indicated number of months
     * before or after start_date.
     * Use EOMONTH to calculate maturity dates or due dates that fall on the last day of the month.
     *
     * @Deprecated 2.0.0 Use the funcEoMonth method in the DateTimeExcel\EoMonth class instead
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
        return DateTimeExcel\EoMonth::funcEoMonth($dateValue, $adjustmentMonths);
    }
}
