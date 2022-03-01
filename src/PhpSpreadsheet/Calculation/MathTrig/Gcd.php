<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Gcd
{
    /**
     * Recursively determine GCD.
     *
     * Returns the greatest common divisor of a series of numbers.
     * The greatest common divisor is the largest integer that divides both
     *        number1 and number2 without a remainder.
     *
     * Excel Function:
     *        GCD(number1[,number2[, ...]])
     *
     * @param float|int $a
     * @param float|int $b
     *
     * @return float|int
     */
    private static function evaluateGCD($a, $b)
    {
        return $b ? self::evaluateGCD($b, $a % $b) : $a;
    }

    /**
     * GCD.
     *
     * Returns the greatest common divisor of a series of numbers.
     * The greatest common divisor is the largest integer that divides both
     *        number1 and number2 without a remainder.
     *
     * Excel Function:
     *        GCD(number1[,number2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|int|string Greatest Common Divisor, or a string containing an error
     */
    public static function evaluate(...$args)
    {
        try {
            $arrayArgs = [];
            foreach (Functions::flattenArray($args) as $value1) {
                if ($value1 !== null) {
                    $value = Helpers::validateNumericNullSubstitution($value1, 1);
                    Helpers::validateNotNegative($value);
                    $arrayArgs[] = (int) $value;
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (count($arrayArgs) <= 0) {
            return ExcelError::VALUE();
        }
        $gcd = (int) array_pop($arrayArgs);
        do {
            $gcd = self::evaluateGCD($gcd, (int) array_pop($arrayArgs));
        } while (!empty($arrayArgs));

        return $gcd;
    }
}
