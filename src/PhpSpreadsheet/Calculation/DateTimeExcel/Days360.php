<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class Days360
{
    /**
     * DAYS360.
     *
     * Returns the number of days between two dates based on a 360-day year (twelve 30-day months),
     * which is used in some accounting calculations. Use this function to help compute payments if
     * your accounting system is based on twelve 30-day months.
     *
     * Excel Function:
     *        DAYS360(startDate,endDate[,method])
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                        PHP DateTime object, or a standard date string
     * @param mixed $method US or European Method as a bool
     *                                        FALSE or omitted: U.S. (NASD) method. If the starting date is
     *                                        the last day of a month, it becomes equal to the 30th of the
     *                                        same month. If the ending date is the last day of a month and
     *                                        the starting date is earlier than the 30th of a month, the
     *                                        ending date becomes equal to the 1st of the next month;
     *                                        otherwise the ending date becomes equal to the 30th of the
     *                                        same month.
     *                                        TRUE: European method. Starting dates and ending dates that
     *                                        occur on the 31st of a month become equal to the 30th of the
     *                                        same month.
     *
     * @return int|string Number of days between start date and end date
     */
    public static function between($startDate = 0, $endDate = 0, $method = false)
    {
        try {
            $startDate = Helpers::getDateValue($startDate);
            $endDate = Helpers::getDateValue($endDate);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (!is_bool($method)) {
            return Functions::VALUE();
        }

        // Execute function
        $PHPStartDateObject = SharedDateHelper::excelToDateTimeObject($startDate);
        $startDay = $PHPStartDateObject->format('j');
        $startMonth = $PHPStartDateObject->format('n');
        $startYear = $PHPStartDateObject->format('Y');

        $PHPEndDateObject = SharedDateHelper::excelToDateTimeObject($endDate);
        $endDay = $PHPEndDateObject->format('j');
        $endMonth = $PHPEndDateObject->format('n');
        $endYear = $PHPEndDateObject->format('Y');

        return self::dateDiff360((int) $startDay, (int) $startMonth, (int) $startYear, (int) $endDay, (int) $endMonth, (int) $endYear, !$method);
    }

    /**
     * Return the number of days between two dates based on a 360 day calendar.
     */
    private static function dateDiff360(int $startDay, int $startMonth, int $startYear, int $endDay, int $endMonth, int $endYear, bool $methodUS): int
    {
        $startDay = self::getStartDay($startDay, $startMonth, $startYear, $methodUS);
        $endDay = self::getEndDay($endDay, $endMonth, $endYear, $startDay, $methodUS);

        return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
    }

    private static function getStartDay(int $startDay, int $startMonth, int $startYear, bool $methodUS): int
    {
        if ($startDay == 31) {
            --$startDay;
        } elseif ($methodUS && ($startMonth == 2 && ($startDay == 29 || ($startDay == 28 && !Helpers::isLeapYear($startYear))))) {
            $startDay = 30;
        }

        return $startDay;
    }

    private static function getEndDay(int $endDay, int &$endMonth, int &$endYear, int $startDay, bool $methodUS): int
    {
        if ($endDay == 31) {
            if ($methodUS && $startDay != 30) {
                $endDay = 1;
                if ($endMonth == 12) {
                    ++$endYear;
                    $endMonth = 1;
                } else {
                    ++$endMonth;
                }
            } else {
                $endDay = 30;
            }
        }

        return $endDay;
    }
}
