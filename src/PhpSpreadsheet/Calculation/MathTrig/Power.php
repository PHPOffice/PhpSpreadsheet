<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Power
{
    /**
     * POWER.
     *
     * Computes x raised to the power y.
     *
     * @param float|int $x
     * @param float|int $y
     *
     * @return float|int|string The result, or a string containing an error
     */
    public static function evaluate($x, $y)
    {
        try {
            $x = Helpers::validateNumericNullBool($x);
            $y = Helpers::validateNumericNullBool($y);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if (!$x && !$y) {
            return Functions::NAN();
        }
        if (!$x && $y < 0.0) {
            return Functions::DIV0();
        }

        // Return
        $result = $x ** $y;

        return Helpers::numberOrNan($result);
    }
}
