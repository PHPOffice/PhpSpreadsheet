<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Trunc
{
    /**
     * TRUNC.
     *
     * Truncates value to the number of fractional digits by number_digits.
     *
     * @param float $value
     * @param int $digits
     *
     * @return float|string Truncated value, or a string containing an error
     */
    public static function funcTrunc($value = 0, $digits = 0)
    {
        MathTrig::nullFalseTrueToNumber($value);
        $digits = Functions::flattenSingleValue($digits);

        // Validate parameters
        if ((!is_numeric($value)) || (!is_numeric($digits))) {
            return Functions::VALUE();
        }
        $digits = floor($digits);

        // Truncate
        $adjust = 10 ** $digits;

        if (($digits > 0) && (rtrim((int) ((abs($value) - abs((int) $value)) * $adjust), '0') < $adjust / 10)) {
            return $value;
        }

        return ((int) ($value * $adjust)) / $adjust;
    }
}
