<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Quotient
{
    /**
     * QUOTIENT.
     *
     * QUOTIENT function returns the integer portion of a division. Numerator is the divided number
     *        and denominator is the divisor.
     *
     * Excel Function:
     *        QUOTIENT(value1,value2)
     *
     * @param mixed $numerator Expect float|int
     * @param mixed $denominator Expect float|int
     *
     * @return int|string
     */
    public static function funcQuotient($numerator, $denominator)
    {
        try {
            $numerator = Helpers::validateNumericNullSubstitution($numerator, 0);
            $denominator = Helpers::validateNumericNullSubstitution($denominator, 0);
            Helpers::validateNotZero($denominator);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (int) ($numerator / $denominator);
    }
}
