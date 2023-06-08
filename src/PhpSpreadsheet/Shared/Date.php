<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Date
{
    /** constants */
    const CALENDAR_WINDOWS_1900 = 1900; //    Base date of 1st Jan 1900 = 1.0
    const CALENDAR_MAC_1904 = 1904; //    Base date of 2nd Jan 1904 = 1.0

    /**
     * Names of the months of the year, indexed by shortname
     * Planned usage for locale settings.
     *
     * @var string[]
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

    /**
     * @var string[]
     */
    public static $numberSuffixes = [
        'st',
        'nd',
        'rd',
        'th',
    ];

    /**
     * Base calendar year to use for calculations
     * Value is either CALENDAR_WINDOWS_1900 (1900) or CALENDAR_MAC_1904 (1904).
     *
     * @var int
     */
    protected static $excelCalendar = self::CALENDAR_WINDOWS_1900;

    /**
     * Default timezone to use for DateTime objects.
     *
     * @var null|DateTimeZone
     */
    protected static $defaultTimeZone;

    /**
     * Set the Excel calendar (Windows 1900 or Mac 1904).
     *
     * @param int $baseYear Excel base date (1900 or 1904)
     *
     * @return bool Success or failure
     */
    public static function setExcelCalendar($baseYear)
    {
        if (
            ($baseYear == self::CALENDAR_WINDOWS_1900) ||
            ($baseYear == self::CALENDAR_MAC_1904)
        ) {
            self::$excelCalendar = $baseYear;

            return true;
        }

        return false;
    }

    /**
     * Return the Excel calendar (Windows 1900 or Mac 1904).
     *
     * @return int Excel base date (1900 or 1904)
     */
    public static function getExcelCalendar()
    {
        return self::$excelCalendar;
    }

    /**
     * Set the Default timezone to use for dates.
     *
     * @param null|DateTimeZone|string $timeZone The timezone to set for all Excel datetimestamp to PHP DateTime Object conversions
     *
     * @return bool Success or failure
     */
    public static function setDefaultTimezone($timeZone)
    {
        try {
            $timeZone = self::validateTimeZone($timeZone);
            self::$defaultTimeZone = $timeZone;
            $retval = true;
        } catch (PhpSpreadsheetException $e) {
            $retval = false;
        }

        return $retval;
    }

    /**
     * Return the Default timezone, or UTC if default not set.
     */
    public static function getDefaultTimezone(): DateTimeZone
    {
        return self::$defaultTimeZone ?? new DateTimeZone('UTC');
    }

    /**
     * Return the Default timezone, or local timezone if default is not set.
     */
    public static function getDefaultOrLocalTimezone(): DateTimeZone
    {
        return self::$defaultTimeZone ?? new DateTimeZone(date_default_timezone_get());
    }

    /**
     * Return the Default timezone even if null.
     */
    public static function getDefaultTimezoneOrNull(): ?DateTimeZone
    {
        return self::$defaultTimeZone;
    }

    /**
     * Validate a timezone.
     *
     * @param null|DateTimeZone|string $timeZone The timezone to validate, either as a timezone string or object
     *
     * @return ?DateTimeZone The timezone as a timezone object
     */
    private static function validateTimeZone($timeZone)
    {
        if ($timeZone instanceof DateTimeZone || $timeZone === null) {
            return $timeZone;
        }
        if (in_array($timeZone, DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC))) {
            return new DateTimeZone($timeZone);
        }

        throw new PhpSpreadsheetException('Invalid timezone');
    }

    /**
     * @param mixed $value Converts a date/time in ISO-8601 standard format date string to an Excel
     *                         serialized timestamp.
     *                     See https://en.wikipedia.org/wiki/ISO_8601 for details of the ISO-8601 standard format.
     *
     * @return float|int
     */
    public static function convertIsoDate($value)
    {
        if (!is_string($value)) {
            throw new Exception('Non-string value supplied for Iso Date conversion');
        }

        $date = new DateTime($value);
        $dateErrors = DateTime::getLastErrors();

        if (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) {
            throw new Exception("Invalid string $value supplied for datatype Date");
        }

        $newValue = SharedDate::PHPToExcel($date);
        if ($newValue === false) {
            throw new Exception("Invalid string $value supplied for datatype Date");
        }

        if (preg_match('/^\\s*\\d?\\d:\\d\\d(:\\d\\d([.]\\d+)?)?\\s*(am|pm)?\\s*$/i', $value) == 1) {
            $newValue = fmod($newValue, 1.0);
        }

        return $newValue;
    }

    /**
     * Convert a MS serialized datetime value from Excel to a PHP Date/Time object.
     *
     * @param float|int $excelTimestamp MS Excel serialized date/time value
     * @param null|DateTimeZone|string $timeZone The timezone to assume for the Excel timestamp,
     *                                           if you don't want to treat it as a UTC value
     *                                           Use the default (UTC) unless you absolutely need a conversion
     *
     * @return DateTime PHP date/time object
     */
    public static function excelToDateTimeObject($excelTimestamp, $timeZone = null)
    {
        $timeZone = ($timeZone === null) ? self::getDefaultTimezone() : self::validateTimeZone($timeZone);
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_EXCEL) {
            if ($excelTimestamp < 1 && self::$excelCalendar === self::CALENDAR_WINDOWS_1900) {
                // Unix timestamp base date
                $baseDate = new DateTime('1970-01-01', $timeZone);
            } else {
                // MS Excel calendar base dates
                if (self::$excelCalendar == self::CALENDAR_WINDOWS_1900) {
                    // Allow adjustment for 1900 Leap Year in MS Excel
                    $baseDate = ($excelTimestamp < 60) ? new DateTime('1899-12-31', $timeZone) : new DateTime('1899-12-30', $timeZone);
                } else {
                    $baseDate = new DateTime('1904-01-01', $timeZone);
                }
            }
        } else {
            $baseDate = new DateTime('1899-12-30', $timeZone);
        }

        $days = floor($excelTimestamp);
        $partDay = $excelTimestamp - $days;
        $hours = floor($partDay * 24);
        $partDay = $partDay * 24 - $hours;
        $minutes = floor($partDay * 60);
        $partDay = $partDay * 60 - $minutes;
        $seconds = round($partDay * 60);

        if ($days >= 0) {
            $days = '+' . $days;
        }
        $interval = $days . ' days';

        return $baseDate->modify($interval)
            ->setTime((int) $hours, (int) $minutes, (int) $seconds);
    }

    /**
     * Convert a MS serialized datetime value from Excel to a unix timestamp.
     * The use of Unix timestamps, and therefore this function, is discouraged.
     * They are not Y2038-safe on a 32-bit system, and have no timezone info.
     *
     * @param float|int $excelTimestamp MS Excel serialized date/time value
     * @param null|DateTimeZone|string $timeZone The timezone to assume for the Excel timestamp,
     *                                               if you don't want to treat it as a UTC value
     *                                               Use the default (UTC) unless you absolutely need a conversion
     *
     * @return int Unix timetamp for this date/time
     */
    public static function excelToTimestamp($excelTimestamp, $timeZone = null)
    {
        return (int) self::excelToDateTimeObject($excelTimestamp, $timeZone)
            ->format('U');
    }

    /**
     * Convert a date from PHP to an MS Excel serialized date/time value.
     *
     * @param mixed $dateValue PHP DateTime object or a string - Unix timestamp is also permitted, but discouraged;
     *    not Y2038-safe on a 32-bit system, and no timezone info
     *
     * @return false|float Excel date/time value
     *                                  or boolean FALSE on failure
     */
    public static function PHPToExcel($dateValue)
    {
        if ((is_object($dateValue)) && ($dateValue instanceof DateTimeInterface)) {
            return self::dateTimeToExcel($dateValue);
        } elseif (is_numeric($dateValue)) {
            return self::timestampToExcel($dateValue);
        } elseif (is_string($dateValue)) {
            return self::stringToExcel($dateValue);
        }

        return false;
    }

    /**
     * Convert a PHP DateTime object to an MS Excel serialized date/time value.
     *
     * @param DateTimeInterface $dateValue PHP DateTime object
     *
     * @return float MS Excel serialized date/time value
     */
    public static function dateTimeToExcel(DateTimeInterface $dateValue)
    {
        return self::formattedPHPToExcel(
            (int) $dateValue->format('Y'),
            (int) $dateValue->format('m'),
            (int) $dateValue->format('d'),
            (int) $dateValue->format('H'),
            (int) $dateValue->format('i'),
            (int) $dateValue->format('s')
        );
    }

    /**
     * Convert a Unix timestamp to an MS Excel serialized date/time value.
     * The use of Unix timestamps, and therefore this function, is discouraged.
     * They are not Y2038-safe on a 32-bit system, and have no timezone info.
     *
     * @param float|int|string $unixTimestamp Unix Timestamp
     *
     * @return false|float MS Excel serialized date/time value
     */
    public static function timestampToExcel($unixTimestamp)
    {
        if (!is_numeric($unixTimestamp)) {
            return false;
        }

        return self::dateTimeToExcel(new DateTime('@' . $unixTimestamp));
    }

    /**
     * formattedPHPToExcel.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hours
     * @param int $minutes
     * @param int $seconds
     *
     * @return float Excel date/time value
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
        $century = (int) substr((string) $year, 0, 2);
        $decade = (int) substr((string) $year, 2, 2);
        $excelDate = floor((146097 * $century) / 4) + floor((1461 * $decade) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119 - $myexcelBaseDate + $excel1900isLeapYear;

        $excelTime = (($hours * 3600) + ($minutes * 60) + $seconds) / 86400;

        return (float) $excelDate + $excelTime;
    }

    /**
     * Is a given cell a date/time?
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isDateTime(Cell $cell, $value = null, bool $dateWithoutTimeOkay = true)
    {
        $result = false;
        $worksheet = $cell->getWorksheetOrNull();
        $spreadsheet = ($worksheet === null) ? null : $worksheet->getParent();
        if ($worksheet !== null && $spreadsheet !== null) {
            $index = $spreadsheet->getActiveSheetIndex();
            $selected = $worksheet->getSelectedCells();

            try {
                $result = is_numeric($value ?? $cell->getCalculatedValue()) &&
                    self::isDateTimeFormat(
                        $worksheet->getStyle(
                            $cell->getCoordinate()
                        )->getNumberFormat(),
                        $dateWithoutTimeOkay
                    );
            } catch (Exception $e) {
                // Result is already false, so no need to actually do anything here
            }
            $worksheet->setSelectedCells($selected);
            $spreadsheet->setActiveSheetIndex($index);
        }

        return $result;
    }

    /**
     * Is a given NumberFormat code a date/time format code?
     *
     * @return bool
     */
    public static function isDateTimeFormat(NumberFormat $excelFormatCode, bool $dateWithoutTimeOkay = true)
    {
        return self::isDateTimeFormatCode((string) $excelFormatCode->getFormatCode(), $dateWithoutTimeOkay);
    }

    private const POSSIBLE_DATETIME_FORMAT_CHARACTERS = 'eymdHs';
    private const POSSIBLE_TIME_FORMAT_CHARACTERS = 'Hs'; // note - no 'm' due to ambiguity

    /**
     * Is a given number format code a date/time?
     *
     * @param string $excelFormatCode
     *
     * @return bool
     */
    public static function isDateTimeFormatCode($excelFormatCode, bool $dateWithoutTimeOkay = true)
    {
        if (strtolower($excelFormatCode) === strtolower(NumberFormat::FORMAT_GENERAL)) {
            //    "General" contains an epoch letter 'e', so we trap for it explicitly here (case-insensitive check)
            return false;
        }
        if (preg_match('/[0#]E[+-]0/i', $excelFormatCode)) {
            //    Scientific format
            return false;
        }

        // Switch on formatcode
        if (in_array($excelFormatCode, NumberFormat::DATE_TIME_OR_DATETIME_ARRAY, true)) {
            return $dateWithoutTimeOkay || in_array($excelFormatCode, NumberFormat::TIME_OR_DATETIME_ARRAY);
        }

        //    Typically number, currency or accounting (or occasionally fraction) formats
        if ((substr($excelFormatCode, 0, 1) == '_') || (substr($excelFormatCode, 0, 2) == '0 ')) {
            return false;
        }
        // Some "special formats" provided in German Excel versions were detected as date time value,
        // so filter them out here - "\C\H\-00000" (Switzerland) and "\D-00000" (Germany).
        if (\strpos($excelFormatCode, '-00000') !== false) {
            return false;
        }
        $possibleFormatCharacters = $dateWithoutTimeOkay ? self::POSSIBLE_DATETIME_FORMAT_CHARACTERS : self::POSSIBLE_TIME_FORMAT_CHARACTERS;
        // Try checking for any of the date formatting characters that don't appear within square braces
        if (preg_match('/(^|\])[^\[]*[' . $possibleFormatCharacters . ']/i', $excelFormatCode)) {
            //    We might also have a format mask containing quoted strings...
            //        we don't want to test for any of our characters within the quoted blocks
            if (strpos($excelFormatCode, '"') !== false) {
                $segMatcher = false;
                foreach (explode('"', $excelFormatCode) as $subVal) {
                    //    Only test in alternate array entries (the non-quoted blocks)
                    $segMatcher = $segMatcher === false;
                    if (
                        $segMatcher &&
                        (preg_match('/(^|\])[^\[]*[' . $possibleFormatCharacters . ']/i', $subVal))
                    ) {
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
     * Convert a date/time string to Excel time.
     *
     * @param string $dateValue Examples: '2009-12-31', '2009-12-31 15:59', '2009-12-31 15:59:10'
     *
     * @return false|float Excel date/time serial value
     */
    public static function stringToExcel($dateValue)
    {
        if (strlen($dateValue) < 2) {
            return false;
        }
        if (!preg_match('/^(\d{1,4}[ \.\/\-][A-Z]{3,9}([ \.\/\-]\d{1,4})?|[A-Z]{3,9}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?|\d{1,4}[ \.\/\-]\d{1,4}([ \.\/\-]\d{1,4})?)( \d{1,2}:\d{1,2}(:\d{1,2})?)?$/iu', $dateValue)) {
            return false;
        }

        $dateValueNew = DateTimeExcel\DateValue::fromString($dateValue);

        if (!is_float($dateValueNew)) {
            return false;
        }

        if (strpos($dateValue, ':') !== false) {
            $timeValue = DateTimeExcel\TimeValue::fromString($dateValue);
            if (!is_float($timeValue)) {
                return false;
            }
            $dateValueNew += $timeValue;
        }

        return $dateValueNew;
    }

    /**
     * Converts a month name (either a long or a short name) to a month number.
     *
     * @param string $monthName Month name or abbreviation
     *
     * @return int|string Month number (1 - 12), or the original string argument if it isn't a valid month name
     */
    public static function monthStringToNumber($monthName)
    {
        $monthIndex = 1;
        foreach (self::$monthNames as $shortMonthName => $longMonthName) {
            if (($monthName === $longMonthName) || ($monthName === $shortMonthName)) {
                return $monthIndex;
            }
            ++$monthIndex;
        }

        return $monthName;
    }

    /**
     * Strips an ordinal from a numeric value.
     *
     * @param string $day Day number with an ordinal
     *
     * @return int|string The integer value with any ordinal stripped, or the original string argument if it isn't a valid numeric
     */
    public static function dayStringToNumber($day)
    {
        $strippedDayValue = (str_replace(self::$numberSuffixes, '', $day));
        if (is_numeric($strippedDayValue)) {
            return (int) $strippedDayValue;
        }

        return $day;
    }

    public static function dateTimeFromTimestamp(string $date, ?DateTimeZone $timeZone = null): DateTime
    {
        $dtobj = DateTime::createFromFormat('U', $date) ?: new DateTime();
        $dtobj->setTimeZone($timeZone ?? self::getDefaultOrLocalTimezone());

        return $dtobj;
    }

    public static function formattedDateTimeFromTimestamp(string $date, string $format, ?DateTimeZone $timeZone = null): string
    {
        $dtobj = self::dateTimeFromTimestamp($date, $timeZone);

        return $dtobj->format($format);
    }
}
