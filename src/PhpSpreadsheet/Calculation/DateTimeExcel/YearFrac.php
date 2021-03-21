<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class YearFrac
{
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
     * @param mixed $endDate Excel date serial value (float), PHP date timestamp (integer),
     *                                    PHP DateTime object, or a standard date string
     * @param int $method Method used for the calculation
     *                                        0 or omitted    US (NASD) 30/360
     *                                        1                Actual/actual
     *                                        2                Actual/360
     *                                        3                Actual/365
     *                                        4                European 30/360
     *
     * @return float|string fraction of the year, or a string containing an error
     */
    public static function funcYearFrac($startDate, $endDate, $method = 0)
    {
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
                return Days360::funcDays360($startDate, $endDate) / 360;
            case 1:
                return self::method1($startDate, $endDate);
            case 2:
                return DateDif::funcDateDif($startDate, $endDate) / 360;
            case 3:
                return DateDif::funcDateDif($startDate, $endDate) / 365;
            case 4:
                return Days360::funcDays360($startDate, $endDate, true) / 360;
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
        if (Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE && Date::getExcelCalendar() !== Date::CALENDAR_MAC_1904) {
            if ($endDate === null && $startDate !== null) {
                if (Month::funcMonth($sDate) == 12 && Day::funcDay($sDate) === 31 && $method === 0) {
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
        $days = DateDif::funcDateDif($startDate, $endDate);
        $startYear = Year::funcYear($startDate);
        $endYear = Year::funcYear($endDate);
        $years = $endYear - $startYear + 1;
        $startMonth = Month::funcMonth($startDate);
        $startDay = Day::funcDay($startDate);
        $endMonth = Month::funcMonth($endDate);
        $endDay = Day::funcDay($endDate);
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
