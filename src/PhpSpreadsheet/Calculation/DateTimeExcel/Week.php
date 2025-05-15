<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class Week
{
    use ArrayEnabled;

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
     *                         Or can be an array of date values
     * @param array<mixed>|int $method Week begins on Sunday or Monday
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
     *                         Or can be an array of methods
     *
     * @return array<mixed>|int|string Week Number
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function number(mixed $dateValue, array|int|string|null $method = Constants::STARTWEEK_SUNDAY): array|int|string
    {
        if (is_array($dateValue) || is_array($method)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $dateValue, $method);
        }

        $origDateValueNull = empty($dateValue);

        try {
            $method = self::validateMethod($method);
            if ($dateValue === null) { // boolean not allowed
                $dateValue = (SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_MAC_1904 || $method === Constants::DOW_SUNDAY) ? 0 : 1;
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
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);
        if ($method == Constants::STARTWEEK_MONDAY_ISO) {
            Helpers::silly1900($PHPDateObject);

            return (int) $PHPDateObject->format('W');
        }
        if (self::buggyWeekNum1904($method, $origDateValueNull, $PHPDateObject)) {
            return 0;
        }
        Helpers::silly1900($PHPDateObject, '+ 5 years'); // 1905 calendar matches
        $dayOfYear = (int) $PHPDateObject->format('z');
        $PHPDateObject->modify('-' . $dayOfYear . ' days');
        $firstDayOfFirstWeek = (int) $PHPDateObject->format('w');
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
     *                         Or can be an array of date values
     *
     * @return array<mixed>|int|string Week Number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function isoWeekNumber(mixed $dateValue): array|int|string
    {
        if (is_array($dateValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $dateValue);
        }

        if (self::apparentBug($dateValue)) {
            return 52;
        }

        try {
            $dateValue = Helpers::getDateValue($dateValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);
        Helpers::silly1900($PHPDateObject);

        return (int) $PHPDateObject->format('W');
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
     * @param null|array<mixed>|bool|float|int|string $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *                         Or can be an array of date values
     * @param mixed $style A number that determines the type of return value
     *                                        1 or omitted    Numbers 1 (Sunday) through 7 (Saturday).
     *                                        2                Numbers 1 (Monday) through 7 (Sunday).
     *                                        3                Numbers 0 (Monday) through 6 (Sunday).
     *                         Or can be an array of styles
     *
     * @return array<mixed>|int|string Day of the week value
     *         If an array of values is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function day(null|array|float|int|string|bool $dateValue, mixed $style = 1): array|string|int
    {
        if (is_array($dateValue) || is_array($style)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $dateValue, $style);
        }

        try {
            $dateValue = Helpers::getDateValue($dateValue);
            $style = self::validateStyle($style);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $PHPDateObject = SharedDateHelper::excelToDateTimeObject($dateValue);
        Helpers::silly1900($PHPDateObject);
        $DoW = (int) $PHPDateObject->format('w');

        switch ($style) {
            case 1:
                ++$DoW;

                break;
            case 2:
                $DoW = self::dow0Becomes7($DoW);

                break;
            case 3:
                $DoW = self::dow0Becomes7($DoW) - 1;

                break;
        }

        return $DoW;
    }

    /**
     * @param mixed $style expect int
     */
    private static function validateStyle(mixed $style): int
    {
        if (!is_numeric($style)) {
            throw new Exception(ExcelError::VALUE());
        }
        $style = (int) $style;
        if (($style < 1) || ($style > 3)) {
            throw new Exception(ExcelError::NAN());
        }

        return $style;
    }

    private static function dow0Becomes7(int $DoW): int
    {
        return ($DoW === 0) ? 7 : $DoW;
    }

    /**
     * @param mixed $dateValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     */
    private static function apparentBug(mixed $dateValue): bool
    {
        if (SharedDateHelper::getExcelCalendar() !== SharedDateHelper::CALENDAR_MAC_1904) {
            if (is_bool($dateValue)) {
                return true;
            }
            if (is_numeric($dateValue) && !((int) $dateValue)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate dateValue parameter.
     */
    private static function validateDateValue(mixed $dateValue): float
    {
        if (is_bool($dateValue)) {
            throw new Exception(ExcelError::VALUE());
        }

        return Helpers::getDateValue($dateValue);
    }

    /**
     * Validate method parameter.
     */
    private static function validateMethod(mixed $method): int
    {
        if ($method === null) {
            $method = Constants::STARTWEEK_SUNDAY;
        }

        if (!is_numeric($method)) {
            throw new Exception(ExcelError::VALUE());
        }

        $method = (int) $method;
        if (!array_key_exists($method, Constants::METHODARR)) {
            throw new Exception(ExcelError::NAN());
        }
        $method = Constants::METHODARR[$method];

        return $method;
    }

    private static function buggyWeekNum1900(int $method): bool
    {
        return $method === Constants::DOW_SUNDAY && SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_WINDOWS_1900;
    }

    private static function buggyWeekNum1904(int $method, bool $origNull, DateTime $dateObject): bool
    {
        // This appears to be another Excel bug.

        return $method === Constants::DOW_SUNDAY && SharedDateHelper::getExcelCalendar() === SharedDateHelper::CALENDAR_MAC_1904
            && !$origNull && $dateObject->format('Y-m-d') === '1904-01-01';
    }
}
