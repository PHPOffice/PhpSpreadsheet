<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
        if ($significance === null) {
            self::floorCheck1Arg();
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
            $significance = Helpers::validateNumericNullSubstitution($significance, ($number < 0) ? -1 : 1);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::argumentsOk((float) $number, (float) $significance);
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
        if (Helpers::returnSign($number) == Helpers::returnSign($significance)) {
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
