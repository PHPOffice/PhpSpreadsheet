<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Counts;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;

class Mean
{
    /**
     * GEOMEAN.
     *
     * Returns the geometric mean of an array or range of positive data. For example, you
     *        can use GEOMEAN to calculate average growth rate given compound interest with
     *        variable rates.
     *
     * Excel Function:
     *        GEOMEAN(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function geometric(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        $aMean = MathTrig\Operations::product($aArgs);
        if (is_numeric($aMean) && ($aMean > 0)) {
            $aCount = Counts::COUNT($aArgs);
            if (Minimum::min($aArgs) > 0) {
                return $aMean ** (1 / $aCount);
            }
        }

        return Functions::NAN();
    }

    /**
     * HARMEAN.
     *
     * Returns the harmonic mean of a data set. The harmonic mean is the reciprocal of the
     *        arithmetic mean of reciprocals.
     *
     * Excel Function:
     *        HARMEAN(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function harmonic(...$args)
    {
        // Loop through arguments
        $aArgs = Functions::flattenArray($args);
        if (Minimum::min($aArgs) < 0) {
            return Functions::NAN();
        }

        $returnValue = 0;
        $aCount = 0;
        foreach ($aArgs as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($arg <= 0) {
                    return Functions::NAN();
                }
                $returnValue += (1 / $arg);
                ++$aCount;
            }
        }

        // Return
        if ($aCount > 0) {
            return 1 / ($returnValue / $aCount);
        }

        return Functions::NA();
    }

    /**
     * TRIMMEAN.
     *
     * Returns the mean of the interior of a data set. TRIMMEAN calculates the mean
     *        taken by excluding a percentage of data points from the top and bottom tails
     *        of a data set.
     *
     * Excel Function:
     *        TRIMEAN(value1[,value2[, ...]], $discard)
     *
     * @param mixed $args Data values
     *
     * @return float|string
     */
    public static function trim(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        // Calculate
        $percent = array_pop($aArgs);

        if ((is_numeric($percent)) && (!is_string($percent))) {
            if (($percent < 0) || ($percent > 1)) {
                return Functions::NAN();
            }

            $mArgs = [];
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }

            $discard = floor(Counts::COUNT($mArgs) * $percent / 2);
            sort($mArgs);

            for ($i = 0; $i < $discard; ++$i) {
                array_pop($mArgs);
                array_shift($mArgs);
            }

            return Averages::average($mArgs);
        }

        return Functions::VALUE();
    }
}
