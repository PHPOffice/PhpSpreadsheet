<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

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
    public static function evaluate($value = 0, $digits = 0)
    {
        try {
            $value = Helpers::validateNumericNullBool($value);
            $digits = Helpers::validateNumericNullSubstitution($digits, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $digits = floor($digits);

        // Truncate
        $adjust = 10 ** $digits;

        if (($digits > 0) && (rtrim((string) (int) ((abs($value) - abs((int) $value)) * $adjust), '0') < $adjust / 10)) {
            return $value;
        }

        return ((int) ($value * $adjust)) / $adjust;
    }
}
