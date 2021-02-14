<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Round
{
    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param mixed $number Should be numeric
     * @param mixed $precision Should be int
     *
     * @return float|string Rounded number
     */
    public static function builtinROUND($number, $precision)
    {
        MathTrig::nullFalseTrueToNumber($number);

        if (!is_numeric($number) || !is_numeric($precision)) {
            return Functions::VALUE();
        }

        return round($number, $precision);
    }
}
