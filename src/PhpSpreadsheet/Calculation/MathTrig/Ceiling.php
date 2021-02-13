<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Ceiling
{
    /**
     * CEILING.
     *
     * Returns number rounded up, away from zero, to the nearest multiple of significance.
     *        For example, if you want to avoid using pennies in your prices and your product is
     *        priced at $4.42, use the formula =CEILING(4.42,0.05) to round prices up to the
     *        nearest nickel.
     *
     * Excel Function:
     *        CEILING(number[,significance])
     *
     * @param float $number the number you want the ceiling
     * @param float $significance the multiple to which you want to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcCeiling($number, $significance = null)
    {
        MathTrig::nullFalseTrueToNumber($number);
        $significance = Functions::flattenSingleValue($significance);

        if ($significance === null) {
            self::floorCheck1Arg();
            $significance = ((float) $number < 0) ? -1 : 1;
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            return self::argumentsOk((float) $number, (float) $significance);
        }

        return Functions::VALUE();
    }

    /**
     * Avoid Scrutinizer problems concerning complexity.
     *
     * @return float|string
     */
    private static function argumentsOk(float $number, float $significance)
    {
        if (empty($number * $significance)) {
            return 0.0;
        }
        if (MathTrig::returnSign($number) == MathTrig::returnSign($significance)) {
            return ceil($number / $significance) * $significance;
        }

        return Functions::NAN();
    }

    private static function floorCheck1Arg(): void
    {
        $compatibility = Functions::getCompatibilityMode();
        if ($compatibility === Functions::COMPATIBILITY_EXCEL) {
            throw new Exception('Excel requires 2 arguments for CEILING');
        }
    }
}
