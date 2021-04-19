<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Mod
{
    /**
     * MOD.
     *
     * @param mixed $dividend Dividend
     * @param mixed $divisor Divisor
     *
     * @return float|int|string Remainder, or a string containing an error
     */
    public static function evaluate($dividend, $divisor)
    {
        try {
            $dividend = Helpers::validateNumericNullBool($dividend);
            $divisor = Helpers::validateNumericNullBool($divisor);
            Helpers::validateNotZero($divisor);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($dividend < 0.0) && ($divisor > 0.0)) {
            return $divisor - fmod(abs($dividend), $divisor);
        }
        if (($dividend > 0.0) && ($divisor < 0.0)) {
            return $divisor + fmod($dividend, abs($divisor));
        }

        return fmod($dividend, $divisor);
    }
}
