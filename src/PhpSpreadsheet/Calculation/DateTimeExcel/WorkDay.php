<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class WorkDay
{
    use ArrayEnabled;

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
     * @param array<mixed>|mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     *                         Or can be an array of date values
     * @param array<mixed>|int $endDays The number of nonweekend and nonholiday days before or after
     *                                        startDate. A positive value for days yields a future date; a
     *                                        negative value yields a past date.
     *                         Or can be an array of int values
     * @param null|mixed $dateArgs An array of dates (such as holidays) to exclude from the calculation
     *
     * @return array<mixed>|DateTime|float|int|string Excel date/time serial value, PHP date/time serial value or PHP date/time object,
     *                        depending on the value of the ReturnDateType flag
     *         If an array of values is passed for the $startDate or $endDays,arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function date(mixed $startDate, array|int|string $endDays, mixed ...$dateArgs): array|float|int|DateTime|string
    {
        if (is_array($startDate) || is_array($endDays)) {
            return self::evaluateArrayArgumentsSubset(
                [self::class, __FUNCTION__],
                2,
                $startDate,
                $endDays,
                ...$dateArgs
            );
        }

        //    Retrieve the mandatory start date and days that are referenced in the function definition
        try {
            $startDate = Helpers::getDateValue($startDate);
            $endDays = Helpers::validateNumericNull($endDays);
            $holidayArray = array_map([Helpers::class, 'getDateValue'], Functions::flattenArray($dateArgs));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $startDate = (float) floor($startDate);
        $endDays = (int) floor($endDays);
        //    If endDays is 0, we always return startDate
        if ($endDays == 0) {
            return $startDate;
        }
        if ($endDays < 0) {
            return self::decrementing($startDate, $endDays, $holidayArray);
        }

        return self::incrementing($startDate, $endDays, $holidayArray);
    }

    /**
     * Use incrementing logic to determine Workday.
     *
     * @param array<mixed> $holidayArray
     */
    private static function incrementing(float $startDate, int $endDays, array $holidayArray): float|int|DateTime
    {
        //    Adjust the start date if it falls over a weekend
        $startDoW = self::getWeekDay($startDate, 3);
        if ($startDoW >= 5) {
            $startDate += 7 - $startDoW;
            --$endDays;
        }

        //    Add endDays
        $endDate = (float) $startDate + ((int) ($endDays / 5) * 7);
        $endDays = $endDays % 5;
        while ($endDays > 0) {
            ++$endDate;
            //    Adjust the calculated end date if it falls over a weekend
            $endDow = self::getWeekDay($endDate, 3);
            if ($endDow >= 5) {
                $endDate += 7 - $endDow;
            }
            --$endDays;
        }

        //    Test any extra holiday parameters
        if (!empty($holidayArray)) {
            $endDate = self::incrementingArray($startDate, $endDate, $holidayArray);
        }

        return Helpers::returnIn3FormatsFloat($endDate);
    }

    /** @param array<mixed> $holidayArray */
    private static function incrementingArray(float $startDate, float $endDate, array $holidayArray): float
    {
        $holidayCountedArray = $holidayDates = [];
        foreach ($holidayArray as $holidayDate) {
            /** @var float $holidayDate */
            if (self::getWeekDay($holidayDate, 3) < 5) {
                $holidayDates[] = $holidayDate;
            }
        }
        sort($holidayDates, SORT_NUMERIC);
        foreach ($holidayDates as $holidayDate) {
            if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                if (!in_array($holidayDate, $holidayCountedArray)) {
                    ++$endDate;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
            //    Adjust the calculated end date if it falls over a weekend
            $endDoW = self::getWeekDay($endDate, 3);
            if ($endDoW >= 5) {
                $endDate += 7 - $endDoW;
            }
        }

        return $endDate;
    }

    /**
     * Use decrementing logic to determine Workday.
     *
     * @param array<mixed> $holidayArray
     */
    private static function decrementing(float $startDate, int $endDays, array $holidayArray): float|int|DateTime
    {
        //    Adjust the start date if it falls over a weekend
        $startDoW = self::getWeekDay($startDate, 3);
        if ($startDoW >= 5) {
            $startDate += -$startDoW + 4;
            ++$endDays;
        }

        //    Add endDays
        $endDate = (float) $startDate + ((int) ($endDays / 5) * 7);
        $endDays = $endDays % 5;
        while ($endDays < 0) {
            --$endDate;
            //    Adjust the calculated end date if it falls over a weekend
            $endDow = self::getWeekDay($endDate, 3);
            if ($endDow >= 5) {
                $endDate += 4 - $endDow;
            }
            ++$endDays;
        }

        //    Test any extra holiday parameters
        if (!empty($holidayArray)) {
            $endDate = self::decrementingArray($startDate, $endDate, $holidayArray);
        }

        return Helpers::returnIn3FormatsFloat($endDate);
    }

    /** @param array<mixed> $holidayArray */
    private static function decrementingArray(float $startDate, float $endDate, array $holidayArray): float
    {
        $holidayCountedArray = $holidayDates = [];
        foreach ($holidayArray as $holidayDate) {
            /** @var float $holidayDate */
            if (self::getWeekDay($holidayDate, 3) < 5) {
                $holidayDates[] = $holidayDate;
            }
        }
        rsort($holidayDates, SORT_NUMERIC);
        foreach ($holidayDates as $holidayDate) {
            if (($holidayDate <= $startDate) && ($holidayDate >= $endDate)) {
                if (!in_array($holidayDate, $holidayCountedArray)) {
                    --$endDate;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
            //    Adjust the calculated end date if it falls over a weekend
            $endDoW = self::getWeekDay($endDate, 3);
            /** int $endDoW */
            if ($endDoW >= 5) {
                $endDate += -$endDoW + 4;
            }
        }

        return $endDate;
    }

    private static function getWeekDay(float $date, int $wd): int
    {
        $result = Functions::scalar(Week::day($date, $wd));

        return is_int($result) ? $result : -1;
    }
}
