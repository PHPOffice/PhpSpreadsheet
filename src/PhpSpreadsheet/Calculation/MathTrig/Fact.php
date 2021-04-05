<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Fact
{
    /**
     * FACT.
     *
     * Returns the factorial of a number.
     * The factorial of a number is equal to 1*2*3*...* number.
     *
     * Excel Function:
     *        FACT(factVal)
     *
     * @param float $factVal Factorial Value
     *
     * @return int|string Factorial, or a string containing an error
     */
    public static function funcFact($factVal)
    {
        try {
            $factVal = Helpers::validateNumericNullBool($factVal);
            Helpers::validateNotNegative($factVal);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $factLoop = floor($factVal);
        if ($factVal > $factLoop) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return Statistical\Distributions\Gamma::gammaValue($factVal + 1);
            }
        }

        $factorial = 1;
        while ($factLoop > 1) {
            $factorial *= $factLoop--;
        }

        return $factorial;
    }
}
