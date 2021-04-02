<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Confidence
{
    use BaseValidations;

    /**
     * CONFIDENCE.
     *
     * Returns the confidence interval for a population mean
     *
     * @param mixed (float) $alpha
     * @param mixed (float) $stdDev Standard Deviation
     * @param mixed (float) $size
     *
     * @return float|string
     */
    public static function CONFIDENCE($alpha, $stdDev, $size)
    {
        $alpha = Functions::flattenSingleValue($alpha);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $size = Functions::flattenSingleValue($size);

        try {
            $alpha = self::validateFloat($alpha);
            $stdDev = self::validateFloat($stdDev);
            $size = self::validateInt($size);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($alpha <= 0) || ($alpha >= 1) || ($stdDev <= 0) || ($size < 1)) {
            return Functions::NAN();
        }

        return Statistical::NORMSINV(1 - $alpha / 2) * $stdDev / sqrt($size);
    }
}
