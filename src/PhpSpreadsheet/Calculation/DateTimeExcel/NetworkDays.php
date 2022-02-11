<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class NetworkDays
{
    use ArrayEnabled;

    /**
     * NETWORKDAYS.
     *
     * Returns the number of whole working days between start_date and end_date. Working days
     * exclude weekends and any dates identified in holidays.
     * Use NETWORKDAYS to calculate employee benefits that accrue based on the number of days
     * worked during a specific term.
     *
     * Excel Function:
     *        NETWORKDAYS(startDate,endDate[,holidays[,holiday[,...]]])
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     *                         Or can be an array of date values
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                            PHP DateTime object, or a standard date string
     *                         Or can be an array of date values
     * @param mixed $dateArgs An array of dates (such as holidays) to exclude from the calculation
     *
     * @return array|int|string Interval between the dates
     *         If an array of values is passed for the $startDate or $endDate arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function count($startDate, $endDate, ...$dateArgs)
    {
        if (is_array($startDate) || is_array($endDate)) {
            return self::evaluateArrayArgumentsSubset(
                [self::class, __FUNCTION__],
                2,
                $startDate,
                $endDate,
                ...$dateArgs
            );
        }

        try {
            //    Retrieve the mandatory start and end date that are referenced in the function definition
            $sDate = Helpers::getDateValue($startDate);
            $eDate = Helpers::getDateValue($endDate);
            $startDate = min($sDate, $eDate);
            $endDate = max($sDate, $eDate);
            //    Get the optional days
            $dateArgs = Functions::flattenArray($dateArgs);
            //    Test any extra holiday parameters
            $holidayArray = [];
            foreach ($dateArgs as $holidayDate) {
                $holidayArray[] = Helpers::getDateValue($holidayDate);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Execute function
        $startDow = self::calcStartDow($startDate);
        $endDow = self::calcEndDow($endDate);
        $wholeWeekDays = (int) floor(($endDate - $startDate) / 7) * 5;
        $partWeekDays = self::calcPartWeekDays($startDow, $endDow);

        //    Test any extra holiday parameters
        $holidayCountedArray = [];
        foreach ($holidayArray as $holidayDate) {
            if (($holidayDate >= $startDate) && ($holidayDate <= $endDate)) {
                if ((Week::day($holidayDate, 2) < 6) && (!in_array($holidayDate, $holidayCountedArray))) {
                    --$partWeekDays;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
        }

        return self::applySign($wholeWeekDays + $partWeekDays, $sDate, $eDate);
    }

    private static function calcStartDow(float $startDate): int
    {
        $startDow = 6 - (int) Week::day($startDate, 2);
        if ($startDow < 0) {
            $startDow = 5;
        }

        return $startDow;
    }

    private static function calcEndDow(float $endDate): int
    {
        $endDow = (int) Week::day($endDate, 2);
        if ($endDow >= 6) {
            $endDow = 0;
        }

        return $endDow;
    }

    private static function calcPartWeekDays(int $startDow, int $endDow): int
    {
        $partWeekDays = $endDow + $startDow;
        if ($partWeekDays > 5) {
            $partWeekDays -= 5;
        }

        return $partWeekDays;
    }

    private static function applySign(int $result, float $sDate, float $eDate): int
    {
        return ($sDate > $eDate) ? -$result : $result;
    }
}
