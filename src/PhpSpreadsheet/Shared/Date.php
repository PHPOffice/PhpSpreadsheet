<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Date
{
    /** constants */
    const CALENDAR_WINDOWS_1900 = 1900; //    Base date of 1st Jan 1900 = 1.0
    const CALENDAR_MAC_1904 = 1904; //    Base date of 2nd Jan 1904 = 1.0

    /*
     * Names of the months of the year, indexed by shortname
     * Planned usage for locale settings
     *
     * @public
     * @var    string[]
     */
    public static $monthNames = [
        'Jan' => 'January',
        'Feb' => 'February',
        'Mar' => 'March',
        'Apr' => 'April',
        'May' => 'May',
        'Jun' => 'June',
        'Jul' => 'July',
        'Aug' => 'August',
        'Sep' => 'September',
        'Oct' => 'October',
        'Nov' => 'November',
        'Dec' => 'December',
    ];

    /*
     * @public
     * @var    string[]
     */
    public static $numberSuffixes = [
        'st',
        'nd',
        'rd',
        'th',
    ];

    /*
     * Base calendar year to use for calculations
     * Value is either CALENDAR_WINDOWS_1900 (1900) or CALENDAR_MAC_1904 (1904)
     *
     * @private
     * @var    int
     */
    protected static $excelCalendar = self::CALENDAR_WINDOWS_1900;

    /*
     * Default timezone to use for DateTime objects
     *
     * @private
     * @var    null|\DateTimeZone
     */
    protected static $defaultTimeZone;

    /**
     * Set the Excel calendar (Windows 1900 or Mac 1904)
     *
     * @param     int    $baseDate           Excel base date (1900 or 1904)
     * @return    bool                        Success or failure
     */
    public static function setExcelCalendar($baseDate)
    {
        if (($baseDate == self::CALENDAR_WINDOWS_1900) ||
            ($baseDate == self::CALENDAR_MAC_1904)) {
            self::$excelCalendar = $baseDate;

            return true;
        }

        return false;
    }

    /**
     * Return the Excel calendar (Windows 1900 or Mac 1904)
     *
     * @return     int    Excel base date (1900 or 1904)
     */
    public static function getExcelCalendar()
    {
        return self::$excelCalendar;
    }

    /**
     * Set the Default timezone to use for dates
     *
     * @param     string|\DateTimeZone    $timeZone    The timezone to set for all Excel datetimestamp to PHP DateTime Object conversions
     * @throws    \Exception
     * @return    bool                              Success or failure
     * @return    bool                              Success or failure
     */
    public static function setDefaultTimezone($timeZone)
    {
        if ($timeZone = self::validateTimeZone($timeZone)) {
            self::$defaultTimeZone = $timeZone;

            return true;
        }

        return false;
    }

    /**
     * Return the Default timezone being used for dates
     *
     * @return     \DateTimeZone    The timezone being used as default for Excel timestamp to PHP DateTime object
     */
    public static function getDefaultTimezone()
    {
        if (self::$defaultTimeZone === null) {
            self::$defaultTimeZone = new \DateTimeZone('UTC');
        }

        return self::$defaultTimeZone;
    }

    /**
     * Validate a timezone
     *
     * @param     string|\DateTimeZone    $timeZone    The timezone to validate, either as a timezone string or object
     * @throws    \Exception
     * @return    \DateTimeZone                        The timezone as a timezone object
     * @return    \DateTimeZone                        The timezone as a timezone object
     */
    protected static function validateTimeZone($timeZone)
    {
        if (is_object($timeZone) && $timeZone instanceof \DateTimeZone) {
            return $timeZone;
        } elseif (is_string($timeZone)) {
            return new \DateTimeZone($timeZone);
        }
        throw new \Exception('Invalid timezone');
    }

    /**
     * Convert a MS serialized datetime value from Excel to a PHP Date/Time object
     *
     * @param     int|float                      $excelTimestamp      MS Excel serialized date/time value
     * @param     \DateTimeZone|string|null          $timeZone            The timezone to assume for the Excel timestamp,
     *                                                                        if you don't want to treat it as a UTC value
     *                                                                    Use the default (UST) unless you absolutely need a conversion
     * @throws    \Exception
     * @return    \DateTime                          PHP date/time object
     */
    public static function excelToDateTimeObject($excelTimestamp = 0, $timeZone = null)
    {
        $timeZone = ($timeZone === null) ? self::getDefaultTimezone() : self::validateTimeZone($timeZone);
        if ($excelTimestamp < 1.0) {
            // Unix timestamp base date
            $baseDate = new \DateTime('1970-01-01', $timeZone);
        } else {
            // MS Excel calendar base dates
            if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
                // Allow adjustment for 1900 Leap Year in MS Excel
                $baseDate = ($excelTimestamp < 60) ? new \DateTime('1899-12-31', $timeZone) : new \DateTime('1899-12-30', $timeZone);
            } else {
                $baseDate = new \DateTime('1904-01-01', $timeZone);
            }
        }
        $days = floor($excelTimestamp);
        $partDay = $excelTimestamp - $days;
        $hours = floor($partDay * 24);
        $partDay = $partDay * 24 - $hours;
        $minutes = floor($partDay * 60);
        $partDay = $partDay * 60 - $minutes;
        $seconds = round($partDay * 60);

        $interval = '+' . $days . ' days';

        return $baseDate->modify($interval)
            ->setTime($hours, $minutes, $seconds);
    }

    /**
     * Convert a MS serialized datetime value from Excel to a unix timestamp
     *
     * @param     int|float                      $excelTimestamp        MS Excel serialized date/time value
     * @param     \DateTimeZone|string|null          $timeZone            The timezone to assume for the Excel timestamp,
     *                                                                        if you don't want to treat it as a UTC value
     *                                                                    Use the default (UST) unless you absolutely need a conversion
     * @throws    \Exception
     * @return    int                            Unix timetamp for this date/time
     */
    public static function excelToTimestamp($excelTimestamp = 0, $timeZone = null)
    {
        return (int) self::excelToDateTimeObject($excelTimestamp, $timeZone)
            ->format('U');
    }

    /**
     *    Convert a date from PHP to an MS Excel serialized date/time value
     *
     *    @param    mixed            $dateValue            Unix Timestamp or PHP DateTime object or a string
     *    @return   float|bool    Excel date/time value
     *                                  or boolean FALSE on failure
     */
    public static function PHPToExcel($dateValue = 0)
    {
        if ((is_object($dateValue)) && ($dateValue instanceof \DateTimeInterface)) {
            return self::dateTimeToExcel($dateValue);
        } elseif (is_numeric($dateValue)) {
            return self::timestampToExcel($dateValue);
        } elseif (is_string($dateValue)) {
            return self::stringToExcel($dateValue);
        }

        return false;
    }

    /**
     *    Convert a PHP DateTime object to an MS Excel serialized date/time value
     *
     *    @param    \DateTimeInterface    $dateValue            PHP DateTime object
     *    @return   float                 MS Excel serialized date/time value
     */
    public static function dateTimeToExcel(\DateTimeInterface $dateValue = null)
    {
        return self::formattedPHPToExcel(
            $dateValue->format('Y'),
            $dateValue->format('m'),
            $dateValue->format('d'),
            $dateValue->format('H'),
            $dateValue->format('i'),
            $dateValue->format('s')
        );
    }

    /**
     *    Convert a Unix timestamp to an MS Excel serialized date/time value
     *
     *    @param    \DateTimeInterface    $dateValue       Unix Timestamp
     *    @return   float                 MS Excel serialized date/time value
     */
    public static function timestampToExcel($dateValue = 0)
    {
        if (!is_numeric($dateValue)) {
            return false;
        }

        return self::dateTimeToExcel(new \DateTime('@' . $dateValue));
    }

    /**
     * formattedPHPToExcel
     *
     * @param    int    $year
     * @param    int    $month
     * @param    int    $day
     * @param    int    $hours
     * @param    int    $minutes
     * @param    int    $seconds
     * @return   float    Excel date/time value
     */
    public static function formattedPHPToExcel($year, $month, $day, $hours = 0, $minutes = 0, $seconds = 0)
    {
        if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
            //
            //    Fudge factor for the erroneous fact that the year 1900 is treated as a Leap Year in MS Excel
            //    This affects every date following 28th February 1900
            //
            $excel1900isLeapYear = true;
            if (($year == 1900) && ($month <= 2)) {
                $excel1900isLeapYear = false;
            }
            $myexcelBaseDate = 2415020;
        } else {
            $myexcelBaseDate = 2416481;
            $excel1900isLeapYear = false;
        }

        //    Julian base date Adjustment
        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        //    Calculate the Julian Date, then subtract the Excel base date (JD 2415020 = 31-Dec-1899 Giving Excel Date of 0)
        $century = substr($year, 0, 2);
        $decade = substr($year, 2, 2);
        $excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myexcelBaseDate + $excel1900isLeapYear;

        $excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

        return (float) $excelDate + $excelTime;
    }

    /**
     * Is a given cell a date/time?
     *
     * @param   \PhpOffice\PhpSpreadsheet\Cell    $pCell
     * @return  bool
     */
    public static function isDateTime(\PhpOffice\PhpSpreadsheet\Cell $pCell)
    {
        return self::isDateTimeFormat(
            $pCell->getWorksheet()->getStyle(
                $pCell->getCoordinate()
            )->getNumberFormat()
        );
    }

    /**
     * Is a given number format a date/time?
     *
     * @param   \PhpOffice\PhpSpreadsheet\Style\NumberFormat    $pFormat
     * @return  bool
     */
    public static function isDateTimeFormat(\PhpOffice\PhpSpreadsheet\Style\NumberFormat $pFormat)
    {
        return self::isDateTimeFormatCode($pFormat->getFormatCode());
    }

    private static $possibleDateFormatCharacters = 'eymdHs';

    /**
     * Is a given number format code a date/time?
     *
     * @param     string    $pFormatCode
     * @return     bool
     */
    public static function isDateTimeFormatCode($pFormatCode = '')
    {
        if (strtolower($pFormatCode) === strtolower(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL)) {
            //    "General" contains an epoch letter 'e', so we trap for it explicitly here (case-insensitive check)
            return false;
        }
        if (preg_match('/[0#]E[+-]0/i', $pFormatCode)) {
            //    Scientific format
            return false;
        }

        // Switch on formatcode
        switch ($pFormatCode) {
            //    Explicitly defined date formats
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYMINUS:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMMINUS:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_MYMINUS:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME1:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME2:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME5:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME6:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME7:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME8:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX14:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX15:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX16:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX17:
            case \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_XLSX22:
                return true;
        }

        //    Typically number, currency or accounting (or occasionally fraction) formats
        if ((substr($pFormatCode, 0, 1) == '_') || (substr($pFormatCode, 0, 2) == '0 ')) {
            return false;
        }
        // Try checking for any of the date formatting characters that don't appear within square braces
        if (preg_match('/(^|\])[^\[]*[' . self::$possibleDateFormatCharacters . ']/i', $pFormatCode)) {
            //    We might also have a format mask containing quoted strings...
            //        we don't want to test for any of our characters within the quoted blocks
            if (strpos($pFormatCode, '"') !== false) {
                $segMatcher = false;
                foreach (explode('"', $pFormatCode) as $subVal) {
                    //    Only test in alternate array entries (the non-quoted blocks)
                    if (($segMatcher = !$segMatcher) &&
                        (preg_match('/(^|\])[^\[]*[' . self::$possibleDateFormatCharacters . ']/i', $subVal))) {
                        return true;
                    }
                }

                return false;
            }

            return true;
        }

        // No date...
        return false;
    }

    /**
     * Convert a date/time string to Excel time
     *
     * @param    string    $dateValue        Examples: '2009-12-31', '2009-12-31 15:59', '2009-12-31 15:59:10'
     * @return    float|false        Excel date/time serial value
     */
    public static function stringToExcel($dateValue = '')
    {
        if (strlen($dateValue) < 2) {
            return false;
        }
        if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue)) {
            return false;
        }

        $dateValueNew = \PhpOffice\PhpSpreadsheet\Calculation\DateTime::DATEVALUE($dateValue);

        if ($dateValueNew === \PhpOffice\PhpSpreadsheet\Calculation\Functions::VALUE()) {
            return false;
        }

        if (strpos($dateValue, ':') !== false) {
            $timeValue = \PhpOffice\PhpSpreadsheet\Calculation\DateTime::TIMEVALUE($dateValue);
            if ($timeValue === \PhpOffice\PhpSpreadsheet\Calculation\Functions::VALUE()) {
                return false;
            }
            $dateValueNew += $timeValue;
        }

        return $dateValueNew;
    }

    /**
     * Converts a month name (either a long or a short name) to a month number
     *
     * @param     string    $month    Month name or abbreviation
     * @return    int|string     Month number (1 - 12), or the original string argument if it isn't a valid month name
     */
    public static function monthStringToNumber($month)
    {
        $monthIndex = 1;
        foreach (self::$monthNames as $shortMonthName => $longMonthName) {
            if (($month === $longMonthName) || ($month === $shortMonthName)) {
                return $monthIndex;
            }
            ++$monthIndex;
        }

        return $month;
    }

    /**
     * Strips an ordinal froma numeric value
     *
     * @param     string    $day      Day number with an ordinal
     * @return    int|string      The integer value with any ordinal stripped, or the original string argument if it isn't a valid numeric
     */
    public static function dayStringToNumber($day)
    {
        $strippedDayValue = (str_replace(self::$numberSuffixes, '', $day));
        if (is_numeric($strippedDayValue)) {
            return (integer) $strippedDayValue;
        }

        return $day;
    }
}
