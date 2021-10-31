<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class WorkDay
{
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
    public static function date($startDate, $endDays, ...$dateArgs)
    {
        //    Retrieve the mandatory start date and days that are referenced in the function definition
        try {
            $startDate = Helpers::getDateValue($startDate);
            $endDays = Helpers::validateNumericNull($endDays);
            $dateArgs = Functions::flattenArray($dateArgs);
            $holidayArray = [];
            foreach ($dateArgs as $holidayDate) {
                $holidayArray[] = Helpers::getDateValue($holidayDate);
            }
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
     * @return mixed
     */
    private static function incrementing(float $startDate, int $endDays, array $holidayArray)
    {
        //    Adjust the start date if it falls over a weekend

        $startDoW = self::getWeekDay($startDate, 3);
        if (self::getWeekDay($startDate, 3) >= 5) {
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

    private static function incrementingArray(float $startDate, float $endDate, array $holidayArray): float
    {
        $holidayCountedArray = $holidayDates = [];
        foreach ($holidayArray as $holidayDate) {
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
     * @return mixed
     */
    private static function decrementing(float $startDate, int $endDays, array $holidayArray)
    {
        //    Adjust the start date if it falls over a weekend

        $startDoW = self::getWeekDay($startDate, 3);
        if (self::getWeekDay($startDate, 3) >= 5) {
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

    private static function decrementingArray(float $startDate, float $endDate, array $holidayArray): float
    {
        $holidayCountedArray = $holidayDates = [];
        foreach ($holidayArray as $holidayDate) {
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
            if ($endDoW >= 5) {
                $endDate += -$endDoW + 4;
            }
        }

        return $endDate;
    }

    private static function getWeekDay(float $date, int $wd): int
    {
        $result = Week::day($date, $wd);

        return is_string($result) ? -1 : $result;
    }
}
