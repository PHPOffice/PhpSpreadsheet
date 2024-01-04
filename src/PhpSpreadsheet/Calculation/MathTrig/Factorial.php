<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Factorial
{
    use ArrayEnabled;

    /**
     * FACT.
     *
     * Returns the factorial of a number.
     * The factorial of a number is equal to 1*2*3*...* number.
     *
     * Excel Function:
     *        FACT(factVal)
     *
     * @param array|float $factVal Factorial Value, or can be an array of numbers
     *
     * @return array|float|int|string Factorial, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function fact($factVal): array|string|float|int
    {
        if (is_array($factVal)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $factVal);
        }

        try {
            $factVal = Helpers::validateNumericNullBool($factVal);
            Helpers::validateNotNegative($factVal);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $factLoop = floor($factVal);
        if ($factVal > $factLoop) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                return Statistical\Distributions\Gamma::gammaValue($factVal + 1);
            }
        }

        $factorial = 1;
        while ($factLoop > 1) {
            $factorial *= $factLoop--;
        }

        return $factorial;
    }

    /**
     * FACTDOUBLE.
     *
     * Returns the double factorial of a number.
     *
     * Excel Function:
     *        FACTDOUBLE(factVal)
     *
     * @param array|float $factVal Factorial Value, or can be an array of numbers
     *
     * @return array|float|int|string Double Factorial, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function factDouble($factVal): array|string|float|int
    {
        if (is_array($factVal)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $factVal);
        }

        try {
            $factVal = Helpers::validateNumericNullSubstitution($factVal, 0);
            Helpers::validateNotNegative($factVal);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $factLoop = floor($factVal);
        $factorial = 1;
        while ($factLoop > 1) {
            $factorial *= $factLoop;
            $factLoop -= 2;
        }

        return $factorial;
    }

    /**
     * MULTINOMIAL.
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @param mixed[] $args An array of mixed values for the Data Series
     *
     * @return float|int|string The result, or a string containing an error
     */
    public static function multinomial(...$args): string|int|float
    {
        $summer = 0;
        $divisor = 1;

        try {
            // Loop through arguments
            foreach (Functions::flattenArray($args) as $argx) {
                $arg = Helpers::validateNumericNullSubstitution($argx, null);
                Helpers::validateNotNegative($arg);
                $arg = (int) $arg;
                $summer += $arg;
                $divisor *= self::fact($arg);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $summer = self::fact($summer);

        return is_numeric($summer) ? ($summer / $divisor) : ExcelError::VALUE();
    }
}
