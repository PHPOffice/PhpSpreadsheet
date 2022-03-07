<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class Time
{
    use ArrayEnabled;

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
     * @param array|int $hour A number from 0 (zero) to 32767 representing the hour.
     *                                    Any value greater than 23 will be divided by 24 and the remainder
     *                                    will be treated as the hour value. For example, TIME(27,0,0) =
     *                                    TIME(3,0,0) = .125 or 3:00 AM.
     * @param array|int $minute A number from 0 to 32767 representing the minute.
     *                                    Any value greater than 59 will be converted to hours and minutes.
     *                                    For example, TIME(0,750,0) = TIME(12,30,0) = .520833 or 12:30 PM.
     * @param array|int $second A number from 0 to 32767 representing the second.
     *                                    Any value greater than 59 will be converted to hours, minutes,
     *                                    and seconds. For example, TIME(0,0,2000) = TIME(0,33,22) = .023148
     *                                    or 12:33:20 AM
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     *
     * @return array|mixed Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function fromHMS($hour, $minute, $second)
    {
        if (is_array($hour) || is_array($minute) || is_array($second)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $hour, $minute, $second);
        }

        try {
            $hour = self::toIntWithNullBool($hour);
            $minute = self::toIntWithNullBool($minute);
            $second = self::toIntWithNullBool($second);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        self::adjustSecond($second, $minute);
        self::adjustMinute($minute, $hour);

        if ($hour > 23) {
            $hour = $hour % 24;
        } elseif ($hour < 0) {
            return ExcelError::NAN();
        }

        // Execute function
        $retType = Functions::getReturnDateType();
        if ($retType === Functions::RETURNDATE_EXCEL) {
            $calendar = SharedDateHelper::getExcelCalendar();
            $date = (int) ($calendar !== SharedDateHelper::CALENDAR_WINDOWS_1900);

            return (float) SharedDateHelper::formattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
        }
        if ($retType === Functions::RETURNDATE_UNIX_TIMESTAMP) {
            return (int) SharedDateHelper::excelToTimestamp(SharedDateHelper::formattedPHPToExcel(1970, 1, 1, $hour, $minute, $second)); // -2147468400; //    -2147472000 + 3600
        }
        // RETURNDATE_PHP_DATETIME_OBJECT
        // Hour has already been normalized (0-23) above
        $phpDateObject = new DateTime('1900-01-01 ' . $hour . ':' . $minute . ':' . $second);

        return $phpDateObject;
    }

    private static function adjustSecond(int &$second, int &$minute): void
    {
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
    }

    private static function adjustMinute(int &$minute, int &$hour): void
    {
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
    }

    /**
     * @param mixed $value expect int
     */
    private static function toIntWithNullBool($value): int
    {
        $value = $value ?? 0;
        if (is_bool($value)) {
            $value = (int) $value;
        }
        if (!is_numeric($value)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (int) $value;
    }
}
