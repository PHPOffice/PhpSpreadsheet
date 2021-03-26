<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Helpers
{
    public const DAYS_PER_YEAR_NASD = 0;
    public const DAYS_PER_YEAR_ACTUAL = 1;
    public const DAYS_PER_YEAR_360 = 2;
    public const DAYS_PER_YEAR_365 = 3;
    public const DAYS_PER_YEAR_360_EUROPEAN = 4;

    /**
     * daysPerYear.
     *
     * Returns the number of days in a specified year, as defined by the "basis" value
     *
     * @param int|string $year The year against which we're testing
     * @param int|string $basis The type of day count:
     *                                    0 or omitted US (NASD)    360
     *                                    1                        Actual (365 or 366 in a leap year)
     *                                    2                        360
     *                                    3                        365
     *                                    4                        European 360
     *
     * @return int|string Result, or a string containing an error
     */
    public static function daysPerYear($year, $basis = 0)
    {
        if (!is_numeric($basis)) {
            return Functions::NAN();
        }

        switch ($basis) {
            case self::DAYS_PER_YEAR_NASD:
            case self::DAYS_PER_YEAR_360:
            case self::DAYS_PER_YEAR_360_EUROPEAN:
                return 360;
            case self::DAYS_PER_YEAR_365:
                return 365;
            case self::DAYS_PER_YEAR_ACTUAL:
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
     *
     * @return bool
     */
    public static function isLastDayOfMonth(DateTimeInterface $date)
    {
        return $date->format('d') === $date->format('t');
    }
}
