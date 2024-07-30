<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Trunc
{
    use ArrayEnabled;

    /**
     * TRUNC.
     *
     * Truncates value to the number of fractional digits by number_digits.
     *
     * @param array|float $value Or can be an array of values
     * @param array|int $digits Or can be an array of values
     *
     * @return array|float|string Truncated value, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function evaluate(array|float|string|null $value = 0, array|int|string $digits = 0): array|float|string
    {
        if (is_array($value) || is_array($digits)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $digits);
        }

        try {
            $value = Helpers::validateNumericNullBool($value);
            $digits = Helpers::validateNumericNullSubstitution($digits, null);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($value == 0) {
            return $value;
        }

        if ($value >= 0) {
            $minusSign = '';
        } else {
            $minusSign = '-';
            $value = -$value;
        }
        $digits = (int) floor($digits);
        if ($digits < 0) {
            $power = (int) (10 ** -$digits);
            $result = intdiv((int) floor($value), $power) * $power;

            return ($minusSign === '') ? $result : -$result;
        }
        $decimals = PHP_FLOAT_DIG - strlen((string) (int) $value);
        $resultString = sprintf('%.' . $decimals . 'F', $value);
        $regExp = '/([.]\\d{' . $digits . '})\\d+$/';
        $result = $minusSign . (preg_replace($regExp, '$1', $resultString) ?? $resultString);

        return (float) $result;
    }
}
