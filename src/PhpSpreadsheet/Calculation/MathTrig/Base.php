<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Base
{
    /**
     * BASE.
     *
     * Converts a number into a text representation with the given radix (base).
     *
     * Excel Function:
     *        BASE(Number, Radix [Min_length])
     *
     * @param mixed $number expect float
     * @param mixed $radix expect float
     * @param mixed $minLength expect int or null
     *
     * @return string the text representation with the given radix (base)
     */
    public static function evaluate($number, $radix, $minLength = null)
    {
        try {
            $number = (int) Helpers::validateNumericNullBool($number);
            $radix = (int) Helpers::validateNumericNullBool($radix);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $minLength = Functions::flattenSingleValue($minLength);

        if ($minLength === null || is_numeric($minLength)) {
            if ($number < 0 || $number >= 2 ** 53 || $radix < 2 || $radix > 36) {
                return Functions::NAN(); // Numeric range constraints
            }

            $outcome = strtoupper((string) base_convert((string) $number, 10, $radix));
            if ($minLength !== null) {
                $outcome = str_pad($outcome, (int) $minLength, '0', STR_PAD_LEFT); // String padding
            }

            return $outcome;
        }

        return Functions::VALUE();
    }
}
