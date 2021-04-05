<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Mod
{
    /**
     * MOD.
     *
     * @param mixed $a Dividend
     * @param mixed $b Divisor
     *
     * @return float|int|string Remainder, or a string containing an error
     */
    public static function evaluate($a, $b)
    {
        try {
            $a = Helpers::validateNumericNullBool($a);
            $b = Helpers::validateNumericNullBool($b);
            Helpers::validateNotZero($b);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($a < 0.0) && ($b > 0.0)) {
            return $b - fmod(abs($a), $b);
        }
        if (($a > 0.0) && ($b < 0.0)) {
            return $b + fmod($a, abs($b));
        }

        return fmod($a, $b);
    }
}
