<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Helpers
{
    /**
     * daysPerYear.
     *
     * Returns the number of days in a specified year, as defined by the "basis" value
     *
     * @param int|string $year The year against which we're testing
     * @param int|string $basis The type of day count:
     *                              0 or omitted US (NASD)   360
     *                              1                        Actual (365 or 366 in a leap year)
     *                              2                        360
     *                              3                        365
     *                              4                        European 360
     *
     * @return int|string Result, or a string containing an error
     */
    public static function daysPerYear($year, $basis = 0)
    {
        if (!is_numeric($basis)) {
            return Functions::NAN();
        }

        switch ($basis) {
            case FinancialConstants::BASIS_DAYS_PER_YEAR_NASD:
            case FinancialConstants::BASIS_DAYS_PER_YEAR_360:
            case FinancialConstants::BASIS_DAYS_PER_YEAR_360_EUROPEAN:
                return 360;
            case FinancialConstants::BASIS_DAYS_PER_YEAR_365:
                return 365;
            case FinancialConstants::BASIS_DAYS_PER_YEAR_ACTUAL:
                return (DateTimeExcel\Helpers::isLeapYear($year)) ? 366 : 365;
        }

        return Functions::NAN();
    }

    /**
     * isLastDayOfMonth.
     *
     * Returns a boolean TRUE/FALSE indicating if this date is the last date of the month
     *
     * @param DateTimeInterface $date The date for testing
     */
    public static function isLastDayOfMonth(DateTimeInterface $date): bool
    {
        return $date->format('d') === $date->format('t');
    }
}
