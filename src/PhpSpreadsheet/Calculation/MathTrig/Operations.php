<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Operations
{
    /**
     * MOD.
     *
     * @param mixed $dividend Dividend
     * @param mixed $divisor Divisor
     *
     * @return float|int|string Remainder, or a string containing an error
     */
    public static function mod($dividend, $divisor)
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

    /**
     * POWER.
     *
     * Computes x raised to the power y.
     *
     * @param float|int $x
     * @param float|int $y
     *
     * @return float|int|string The result, or a string containing an error
     */
    public static function power($x, $y)
    {
        try {
            $x = Helpers::validateNumericNullBool($x);
            $y = Helpers::validateNumericNullBool($y);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Validate parameters
        if (!$x && !$y) {
            return Functions::NAN();
        }
        if (!$x && $y < 0.0) {
            return Functions::DIV0();
        }

        // Return
        $result = $x ** $y;

        return Helpers::numberOrNan($result);
    }

    /**
     * PRODUCT.
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function product(...$args)
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                if ($returnValue === null) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
            } else {
                return Functions::VALUE();
            }
        }

        // Return
        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

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
    public static function quotient($numerator, $denominator)
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
