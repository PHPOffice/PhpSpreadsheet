<?php

namespace PHPExcel\Shared;

/**
 * \PHPExcel\Shared\Date
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @category   PHPExcel
 * @package    PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Date
{
    /** constants */
    const CALENDAR_WINDOWS_1900 = 1900;        //    Base date of 1st Jan 1900 = 1.0
    const CALENDAR_MAC_1904 = 1904;            //    Base date of 2nd Jan 1904 = 1.0

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
     * @param     integer    $baseDate           Excel base date (1900 or 1904)
     * @return    boolean                        Success or failure
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
     * @return     integer    Excel base date (1900 or 1904)
     */
    public static function getExcelCalendar()
    {
        return self::$excelCalendar;
    }

    /**
     * Set the Default timezone to use for dates
     *
     * @param     string|\DateTimeZone    $timezone    The timezone to set for all Excel datetimestamp to PHP DateTime Object conversions
     * @return    boolean                              Success or failure
     * @throws    \Exception
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
     * @param     string|\DateTimeZone    $timezone    The timezone to validate, either as a timezone string or object
     * @return    \DateTimeZone                        The timezone as a timezone object
     * @throws    \Exception
     */
    protected static function validateTimeZone($timeZone) {
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
     * @param     integer|float    $dateValue        Excel date/time value
     * @param     \DateTimeZone|string|null          $timezone            The timezone to assume for the Excel timestamp,
     *                                                                        if you don't want to treat it as a UTC value
     * @return    \DateTime                          PHP date/time object
     * @throws    \Exception
     */
	public static function excelToDateTimeObject($excelTimestamp = 0, $timeZone = null) {
        $timeZone = ($timeZone === null) ? self::getDefaultTimezone() : self::validateTimeZone($timeZone);
        if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
            $baseDate = ($excelTimestamp < 60) ? new \DateTime('1899-12-31', $timeZone) : new \DateTime('1899-12-30', $timeZone);
        } else {
            $baseDate = new \DateTime('1904-01-01', $timeZone);
        }
		$days = floor($excelTimestamp);
        $partDay = $excelTimestamp - $days;
        $hours = floor($partDay * 24);
        $partDay = $partDay * 24 - $hours;
        $minutes = floor($partDay * 60);
        $partDay = $partDay * 60 - $minutes;
        $seconds = floor($partDay * 60);
//        $fraction = $partDay - $seconds;

        $interval = '+' . $days . ' days';
		return $baseDate->modify($interval)
            ->setTime($hours, $minutes, $seconds);
	}

    /**
     * Convert a MS serialized datetime value from Excel to a unix timestamp
     *
     * @param     integer|float    $dateValue        Excel date/time value
     * @return    integer                            Unix timetamp for this date/time
     * @throws    \Exception
     */
	public static function excelToTimestamp($excelTimestampexcelTimestamp = 0, $timeZone = null) {
	    return self::excelToDateTimeObject($excelTimestamp, $timeZone)
            ->format('U');
	}


    /**
     *    Convert a date from PHP to Excel
     *
     *    @param    mixed        $dateValue            PHP serialized date/time or date object
     *    @param    boolean        $adjustToTimezone    Flag indicating whether $dateValue should be treated as
     *                                                    a UST timestamp, or adjusted to UST
     *    @param    string         $timezone            The timezone for finding the adjustment from UST
     *    @return    mixed        Excel date/time value
     *                            or boolean FALSE on failure
     */
    public static function PHPToExcel($dateValue = 0, $adjustToTimezone = false, $timezone = null)
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');

        $timezoneAdjustment = ($adjustToTimezone) ?
            PHPExcel_Shared_TimeZone::getTimezoneAdjustment($timezone ? $timezone : $saveTimeZone, $dateValue) :
            0;

        $retValue = false;
        if ((is_object($dateValue)) && ($dateValue instanceof \DateTime)) {
            $dateValue->add(new \DateInterval('PT' . $timezoneAdjustment . 'S'));
            $retValue = self::formattedPHPToExcel($dateValue->format('Y'), $dateValue->format('m'), $dateValue->format('d'), $dateValue->format('H'), $dateValue->format('i'), $dateValue->format('s'));
        } elseif (is_numeric($dateValue)) {
            $dateValue += $timezoneAdjustment;
            $retValue = self::formattedPHPToExcel(date('Y', $dateValue), date('m', $dateValue), date('d', $dateValue), date('H', $dateValue), date('i', $dateValue), date('s', $dateValue));
        } elseif (is_string($dateValue)) {
            $retValue = self::stringToExcel($dateValue);
        }
        date_default_timezone_set($saveTimeZone);

        return $retValue;
    }


    /**
     * formattedPHPToExcel
     *
     * @param    integer    $year
     * @param    integer    $month
     * @param    integer    $day
     * @param    integer    $hours
     * @param    integer    $minutes
     * @param    integer    $seconds
     * @return   integer    Excel date/time value
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
     * @param   \PHPExcel\Cell    $pCell
     * @return  boolean
     */
    public static function isDateTime(\PHPExcel\Cell $pCell)
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
     * @param   \PHPExcel\Style\NumberFormat    $pFormat
     * @return  boolean
     */
    public static function isDateTimeFormat(\PHPExcel\Style\NumberFormat $pFormat)
    {
        return self::isDateTimeFormatCode($pFormat->getFormatCode());
    }


    private static $possibleDateFormatCharacters = 'eymdHs';

    /**
     * Is a given number format code a date/time?
     *
     * @param     string    $pFormatCode
     * @return     boolean
     */
    public static function isDateTimeFormatCode($pFormatCode = '')
    {
        if (strtolower($pFormatCode) === strtolower(\PHPExcel\Style\NumberFormat::FORMAT_GENERAL)) {
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
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_YYYYMMDD:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_DDMMYYYY:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_DMYSLASH:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_DMYMINUS:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_DMMINUS:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_MYMINUS:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_DATETIME:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME1:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME2:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME3:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME4:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME5:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME6:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME7:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_TIME8:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_XLSX14:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_XLSX15:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_XLSX16:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_XLSX17:
            case \PHPExcel\Style\NumberFormat::FORMAT_DATE_XLSX22:
                return true;
        }

        //    Typically number, currency or accounting (or occasionally fraction) formats
        if ((substr($pFormatCode, 0, 1) == '_') || (substr($pFormatCode, 0, 2) == '0 ')) {
            return false;
        }
        // Try checking for any of the date formatting characters that don't appear within square braces
        if (preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i', $pFormatCode)) {
            //    We might also have a format mask containing quoted strings...
            //        we don't want to test for any of our characters within the quoted blocks
            if (strpos($pFormatCode, '"') !== false) {
                $segMatcher = false;
                foreach (explode('"', $pFormatCode) as $subVal) {
                    //    Only test in alternate array entries (the non-quoted blocks)
                    if (($segMatcher = !$segMatcher) &&
                        (preg_match('/(^|\])[^\[]*['.self::$possibleDateFormatCharacters.']/i', $subVal))) {
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
     * @return    float|FALSE        Excel date/time serial value
     */
    public static function stringToExcel($dateValue = '')
    {
        if (strlen($dateValue) < 2) {
            return false;
        }
        if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue)) {
            return false;
        }

        $dateValueNew = \PHPExcel\Calculation\DateTime::DATEVALUE($dateValue);

        if ($dateValueNew === \PHPExcel\Calculation\Functions::VALUE()) {
            return false;
        }

        if (strpos($dateValue, ':') !== false) {
            $timeValue = \PHPExcel\Calculation\DateTime::TIMEVALUE($dateValue);
            if ($timeValue === \PHPExcel\Calculation\Functions::VALUE()) {
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
     * @return    integer|string     Month number (1 - 12), or the original string argument if it isn't a valid month name
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
     * @return    integer|string      The integer value with any ordinal stripped, or the original string argument if it isn't a valid numeric
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
