<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Tanh
{
    /**
     * TANH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function funcTanh($angle)
    {
        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return tanh($angle);
    }
}
