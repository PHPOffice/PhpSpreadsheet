<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class WeekNum
{
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
    public static function funcWeekNum($dateValue, $method = Constants::STARTWEEK_SUNDAY)
    {
        $origDateValueNull = empty($dateValue);

        try {
            $method = self::validateMethod($method);
            if ($dateValue === null) { // boolean not allowed
                $dateValue = (Date::getExcelCalendar() === DATE::CALENDAR_MAC_1904 || $method === Constants::DOW_SUNDAY) ? 0 : 1;
            }
            $dateValue = self::validateDateValue($dateValue);
            if (!$dateValue && self::buggyWeekNum1900($method)) {
                // This seems to be an additional Excel bug.
                return 0;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = Date::excelToDateTimeObject($dateValue);
        if ($method == Constants::STARTWEEK_MONDAY_ISO) {
            Helpers::silly1900($PHPDateObject);

            return (int) $PHPDateObject->format('W');
        }
        if (self::buggyWeekNum1904($method, $origDateValueNull, $PHPDateObject)) {
            return 0;
        }
        Helpers::silly1900($PHPDateObject, '+ 5 years'); // 1905 calendar matches
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
     * Validate dateValue parameter.
     *
     * @param mixed $dateValue
     */
    private static function validateDateValue($dateValue): float
    {
        if (is_bool($dateValue)) {
            throw new Exception(Functions::VALUE());
        }

        return Helpers::getDateValue($dateValue);
    }

    /**
     * Validate method parameter.
     *
     * @param mixed $method
     */
    private static function validateMethod($method): int
    {
        if ($method === null) {
            $method = Constants::STARTWEEK_SUNDAY;
        }
        $method = Functions::flattenSingleValue($method);
        if (!is_numeric($method)) {
            throw new Exception(Functions::VALUE());
        }

        $method = (int) $method;
        if (!array_key_exists($method, Constants::METHODARR)) {
            throw new Exception(Functions::NAN());
        }
        $method = Constants::METHODARR[$method];

        return $method;
    }

    private static function buggyWeekNum1900(int $method): bool
    {
        return $method === Constants::DOW_SUNDAY && Date::getExcelCalendar() === Date::CALENDAR_WINDOWS_1900;
    }

    private static function buggyWeekNum1904(int $method, bool $origNull, DateTime $dateObject): bool
    {
        // This appears to be another Excel bug.

        return $method === Constants::DOW_SUNDAY && Date::getExcelCalendar() === Date::CALENDAR_MAC_1904 && !$origNull && $dateObject->format('Y-m-d') === '1904-01-01';
    }
}
