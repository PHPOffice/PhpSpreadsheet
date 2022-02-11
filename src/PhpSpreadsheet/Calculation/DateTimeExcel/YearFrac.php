<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDateHelper;

class YearFrac
{
    use ArrayEnabled;

    /**
     * YEARFRAC.
     *
     * Calculates the fraction of the year represented by the number of whole days between two dates
     * (the start_date and the end_date).
     * Use the YEARFRAC worksheet function to identify the proportion of a whole year's benefits or
     * obligations to assign to a specific term.
     *
     * Excel Function:
     *        YEARFRAC(startDate,endDate[,method])
     * See https://lists.oasis-open.org/archives/office-formula/200806/msg00039.html
     *     for description of algorithm used in Excel
     *
     * @param mixed $startDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *                         Or can be an array of values
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     *                         Or can be an array of methods
     * @param array|int $method Method used for the calculation
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *                         Or can be an array of methods
     *
     * @return array|float|string fraction of the year, or a string containing an error
     *         If an array of values is passed for the $startDate or $endDays,arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function fraction($startDate, $endDate, $method = 0)
    {
        if (is_array($startDate) || is_array($endDate) || is_array($method)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $startDate, $endDate, $method);
        }

        try {
            $method = (int) Helpers::validateNumericNull($method);
            $sDate = Helpers::getDateValue($startDate);
            $eDate = Helpers::getDateValue($endDate);
            $sDate = self::excelBug($sDate, $startDate, $endDate, $method);
            $eDate = self::excelBug($eDate, $endDate, $startDate, $method);
            $startDate = min($sDate, $eDate);
            $endDate = max($sDate, $eDate);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        switch ($method) {
            case 0:
                return Functions::scalar(Days360::between($startDate, $endDate)) / 360;
            case 1:
                return self::method1($startDate, $endDate);
            case 2:
                return Functions::scalar(Difference::interval($startDate, $endDate)) / 360;
            case 3:
                return Functions::scalar(Difference::interval($startDate, $endDate)) / 365;
            case 4:
                return Functions::scalar(Days360::between($startDate, $endDate, true)) / 360;
        }

        return Functions::NAN();
    }

    /**
     * Excel 1900 calendar treats date argument of null as 1900-01-00. Really.
     *
     * @param mixed $startDate
     * @param mixed $endDate
     */
    private static function excelBug(float $sDate, $startDate, $endDate, int $method): float
    {
        if (Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE && SharedDateHelper::getExcelCalendar() !== SharedDateHelper::CALENDAR_MAC_1904) {
            if ($endDate === null && $startDate !== null) {
                if (DateParts::month($sDate) == 12 && DateParts::day($sDate) === 31 && $method === 0) {
                    $sDate += 2;
                } else {
                    ++$sDate;
                }
            }
        }

        return $sDate;
    }

    private static function method1(float $startDate, float $endDate): float
    {
        $days = Functions::scalar(Difference::interval($startDate, $endDate));
        $startYear = (int) DateParts::year($startDate);
        $endYear = (int) DateParts::year($endDate);
        $years = $endYear - $startYear + 1;
        $startMonth = (int) DateParts::month($startDate);
        $startDay = (int) DateParts::day($startDate);
        $endMonth = (int) DateParts::month($endDate);
        $endDay = (int) DateParts::day($endDate);
        $startMonthDay = 100 * $startMonth + $startDay;
        $endMonthDay = 100 * $endMonth + $endDay;
        if ($years == 1) {
            $tmpCalcAnnualBasis = 365 + (int) Helpers::isLeapYear($endYear);
        } elseif ($years == 2 && $startMonthDay >= $endMonthDay) {
            if (Helpers::isLeapYear($startYear)) {
                $tmpCalcAnnualBasis = 365 + (int) ($startMonthDay <= 229);
            } elseif (Helpers::isLeapYear($endYear)) {
                $tmpCalcAnnualBasis = 365 + (int) ($endMonthDay >= 229);
            } else {
                $tmpCalcAnnualBasis = 365;
            }
        } else {
            $tmpCalcAnnualBasis = 0;
            for ($year = $startYear; $year <= $endYear; ++$year) {
                $tmpCalcAnnualBasis += 365 + (int) Helpers::isLeapYear($year);
            }
            $tmpCalcAnnualBasis /= $years;
        }

        return $days / $tmpCalcAnnualBasis;
    }
}
