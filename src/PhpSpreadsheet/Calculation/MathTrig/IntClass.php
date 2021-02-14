<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class IntClass
{
    /**
     * INT.
     *
     * Casts a floating point value to an integer
     *
     * Excel Function:
     *        INT(number)
     *
     * @param float $number Number to cast to an integer
     *
     * @return int|string Integer value, or a string containing an error
     */
    public static function funcInt($number)
    {
        MathTrig::nullFalseTrueToNumber($number);
        if (is_numeric($number)) {
            return (int) floor($number);
        }

        return Functions::VALUE();
    }
}
