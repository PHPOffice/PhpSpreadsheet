<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class TimeParts
{
    use ArrayEnabled;

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
     *                         Or can be an array of date/time values
     *
     * @return array|int|string Hour
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function hour($timeValue)
    {
        if (is_array($timeValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $timeValue);
        }

        try {
            Helpers::nullFalseTrueToNumber($timeValue);
            if (!is_numeric($timeValue)) {
                $timeValue = Helpers::getTimeValue($timeValue);
            }
            Helpers::validateNotNegative($timeValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $timeValue = fmod($timeValue, 1);
        $timeValue = SharedDateHelper::excelToDateTimeObject($timeValue);

        return (int) $timeValue->format('H');
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
     *                         Or can be an array of date/time values
     *
     * @return array|int|string Minute
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function minute($timeValue)
    {
        if (is_array($timeValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $timeValue);
        }

        try {
            Helpers::nullFalseTrueToNumber($timeValue);
            if (!is_numeric($timeValue)) {
                $timeValue = Helpers::getTimeValue($timeValue);
            }
            Helpers::validateNotNegative($timeValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $timeValue = fmod($timeValue, 1);
        $timeValue = SharedDateHelper::excelToDateTimeObject($timeValue);

        return (int) $timeValue->format('i');
    }

    /**
     * SECOND.
     *
     * Returns the seconds of a time value.
     * The minute is given as an integer, ranging from 0 to 59.
     *
     * Excel Function:
     *        SECOND(timeValue)
     *
     * @param mixed $timeValue Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard time string
     *                         Or can be an array of date/time values
     *
     * @return array|int|string Second
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function second($timeValue)
    {
        if (is_array($timeValue)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $timeValue);
        }

        try {
            Helpers::nullFalseTrueToNumber($timeValue);
            if (!is_numeric($timeValue)) {
                $timeValue = Helpers::getTimeValue($timeValue);
            }
            Helpers::validateNotNegative($timeValue);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $timeValue = fmod($timeValue, 1);
        $timeValue = SharedDateHelper::excelToDateTimeObject($timeValue);

        return (int) $timeValue->format('s');
    }
}
