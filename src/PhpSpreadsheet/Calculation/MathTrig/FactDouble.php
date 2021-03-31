<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class FactDouble
{
    /**
     * FACTDOUBLE.
     *
     * Returns the double factorial of a number.
     *
     * Excel Function:
     *        FACTDOUBLE(factVal)
     *
     * @param float $factVal Factorial Value
     *
     * @return float|int|string Double Factorial, or a string containing an error
     */
    public static function evaluate($factVal)
    {
        try {
            $factVal = Helpers::validateNumericNullSubstitution($factVal, 0);
            Helpers::validateNotNegative($factVal);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $factLoop = floor($factVal);
        $factorial = 1;
        while ($factLoop > 1) {
            $factorial *= $factLoop;
            $factLoop -= 2;
        }

        return $factorial;
    }
}
