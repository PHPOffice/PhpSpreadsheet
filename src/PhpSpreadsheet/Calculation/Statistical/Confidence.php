<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Confidence
{
    /**
     * CONFIDENCE.
     *
     * Returns the confidence interval for a population mean
     *
     * @param float $alpha
     * @param float $stdDev Standard Deviation
     * @param float $size
     *
     * @return float|string
     */
    public static function CONFIDENCE($alpha, $stdDev, $size)
    {
        $alpha = Functions::flattenSingleValue($alpha);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $size = Functions::flattenSingleValue($size);

        if ((is_numeric($alpha)) && (is_numeric($stdDev)) && (is_numeric($size))) {
            $size = floor($size);
            if (($alpha <= 0) || ($alpha >= 1)) {
                return Functions::NAN();
            }
            if (($stdDev <= 0) || ($size < 1)) {
                return Functions::NAN();
            }

            return Statistical::NORMSINV(1 - $alpha / 2) * $stdDev / sqrt($size);
        }

        return Functions::VALUE();
    }
}
