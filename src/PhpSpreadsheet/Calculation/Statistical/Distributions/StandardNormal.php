<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions;

class StandardNormal
{
    /**
     * NORMSDIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed (float) $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function cumulative($value)
    {
        return Normal::distribution($value, 0, 1, true);
    }

    /**
     * NORM.S.DIST.
     *
     * Returns the standard normal cumulative distribution function. The distribution has
     * a mean of 0 (zero) and a standard deviation of one. Use this function in place of a
     * table of standard normal curve areas.
     *
     * @param mixed (float) $value
     * @param mixed (bool) $cumulative
     *
     * @return float|string The result, or a string containing an error
     */
    public static function distribution($value, $cumulative)
    {
        return Normal::distribution($value, 0, 1, $cumulative);
    }

    /**
     * NORMSINV.
     *
     * Returns the inverse of the standard normal cumulative distribution
     *
     * @param mixed (float) $value
     *
     * @return float|string The result, or a string containing an error
     */
    public static function inverse($value)
    {
        return Normal::inverse($value, 0, 1);
    }
}
