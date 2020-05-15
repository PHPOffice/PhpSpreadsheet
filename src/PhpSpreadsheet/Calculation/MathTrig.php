<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Matrix\Exception as MatrixException;
use Matrix\Matrix;

class MathTrig
{
    //
    //    Private method to return an array of the factors of the input value
    //
    private static function factors($value)
    {
        $startVal = floor(sqrt($value));

        $factorArray = [];
        for ($i = $startVal; $i > 1; --$i) {
            if (($value % $i) == 0) {
                $factorArray = array_merge($factorArray, self::factors($value / $i));
                $factorArray = array_merge($factorArray, self::factors($i));
                if ($i <= sqrt($value)) {
                    break;
                }
            }
        }
        if (!empty($factorArray)) {
            rsort($factorArray);

            return $factorArray;
        }

        return [(int) $value];
    }

    private static function romanCut($num, $n)
    {
        return ($num - ($num % $n)) / $n;
    }

    /**
     * ARABIC.
     *
     * Converts a Roman numeral to an Arabic numeral.
     *
     * Excel Function:
     *        ARABIC(text)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param string $roman
     *
     * @return int|string the arabic numberal contrived from the roman numeral
     */
    public static function ARABIC($roman)
    {
        // An empty string should return 0
        $roman = substr(trim(strtoupper((string) Functions::flattenSingleValue($roman))), 0, 255);
        if ($roman === '') {
            return 0;
        }

        // Convert the roman numeral to an arabic number
        $lookup = [
            'M' => 1000, 'CM' => 900,
            'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90,
            'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9,
            'V' => 5, 'IV' => 4, 'I' => 1,
        ];

        $negativeNumber = $roman[0] === '-';
        if ($negativeNumber) {
            $roman = substr($roman, 1);
        }

        $arabic = 0;
        for ($i = 0; $i < strlen($roman); ++$i) {
            if (!isset($lookup[$roman[$i]])) {
                return Functions::VALUE(); // Invalid character detected
            }

            if ($i < (strlen($roman) - 1) && isset($lookup[substr($roman, $i, 2)])) {
                $arabic += $lookup[substr($roman, $i, 2)]; // Detected a match on the next 2 characters
                ++$i;
            } else {
                $arabic += $lookup[$roman[$i]]; // Detected a match on one character only
            }
        }

        if ($negativeNumber) {
            $arabic *= -1; // The number should be negative
        }

        return $arabic;
    }

    /**
     * ATAN2.
     *
     * This function calculates the arc tangent of the two variables x and y. It is similar to
     *        calculating the arc tangent of y ÷ x, except that the signs of both arguments are used
     *        to determine the quadrant of the result.
     * The arctangent is the angle from the x-axis to a line containing the origin (0, 0) and a
     *        point with coordinates (xCoordinate, yCoordinate). The angle is given in radians between
     *        -pi and pi, excluding -pi.
     *
     * Note that the Excel ATAN2() function accepts its arguments in the reverse order to the standard
     *        PHP atan2() function, so we need to reverse them here before calling the PHP atan() function.
     *
     * Excel Function:
     *        ATAN2(xCoordinate,yCoordinate)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $xCoordinate the x-coordinate of the point
     * @param float $yCoordinate the y-coordinate of the point
     *
     * @return float|string the inverse tangent of the specified x- and y-coordinates, or a string containing an error
     */
    public static function ATAN2($xCoordinate = null, $yCoordinate = null)
    {
        $xCoordinate = Functions::flattenSingleValue($xCoordinate);
        $yCoordinate = Functions::flattenSingleValue($yCoordinate);

        $xCoordinate = ($xCoordinate !== null) ? $xCoordinate : 0.0;
        $yCoordinate = ($yCoordinate !== null) ? $yCoordinate : 0.0;

        if (((is_numeric($xCoordinate)) || (is_bool($xCoordinate))) &&
            ((is_numeric($yCoordinate))) || (is_bool($yCoordinate))) {
            $xCoordinate = (float) $xCoordinate;
            $yCoordinate = (float) $yCoordinate;

            if (($xCoordinate == 0) && ($yCoordinate == 0)) {
                return Functions::DIV0();
            }

            return atan2($yCoordinate, $xCoordinate);
        }

        return Functions::VALUE();
    }

    /**
     * BASE.
     *
     * Converts a number into a text representation with the given radix (base).
     *
     * Excel Function:
     *        BASE(Number, Radix [Min_length])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number
     * @param float $radix
     * @param int $minLength
     *
     * @return string the text representation with the given radix (base)
     */
    public static function BASE($number, $radix, $minLength = null)
    {
        $number = Functions::flattenSingleValue($number);
        $radix = Functions::flattenSingleValue($radix);
        $minLength = Functions::flattenSingleValue($minLength);

        if (is_numeric($number) && is_numeric($radix) && ($minLength === null || is_numeric($minLength))) {
            // Truncate to an integer
            $number = (int) $number;
            $radix = (int) $radix;
            $minLength = (int) $minLength;

            if ($number < 0 || $number >= 2 ** 53 || $radix < 2 || $radix > 36) {
                return Functions::NAN(); // Numeric range constraints
            }

            $outcome = strtoupper((string) base_convert($number, 10, $radix));
            if ($minLength !== null) {
                $outcome = str_pad($outcome, $minLength, '0', STR_PAD_LEFT); // String padding
            }

            return $outcome;
        }

        return Functions::VALUE();
    }

    /**
     * CEILING.
     *
     * Returns number rounded up, away from zero, to the nearest multiple of significance.
     *        For example, if you want to avoid using pennies in your prices and your product is
     *        priced at $4.42, use the formula =CEILING(4.42,0.05) to round prices up to the
     *        nearest nickel.
     *
     * Excel Function:
     *        CEILING(number[,significance])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number the number you want to round
     * @param float $significance the multiple to which you want to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function CEILING($number, $significance = null)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);

        if (($significance === null) &&
            (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC)) {
            $significance = $number / abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if (($number == 0.0) || ($significance == 0.0)) {
                return 0.0;
            } elseif (self::SIGN($number) == self::SIGN($significance)) {
                return ceil($number / $significance) * $significance;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

    /**
     * COMBIN.
     *
     * Returns the number of combinations for a given number of items. Use COMBIN to
     *        determine the total possible number of groups for a given number of items.
     *
     * Excel Function:
     *        COMBIN(numObjs,numInSet)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param int $numObjs Number of different objects
     * @param int $numInSet Number of objects in each combination
     *
     * @return int|string Number of combinations, or a string containing an error
     */
    public static function COMBIN($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            if ($numObjs < $numInSet) {
                return Functions::NAN();
            } elseif ($numInSet < 0) {
                return Functions::NAN();
            }

            return round(self::FACT($numObjs) / self::FACT($numObjs - $numInSet)) / self::FACT($numInSet);
        }

        return Functions::VALUE();
    }

    /**
     * EVEN.
     *
     * Returns number rounded up to the nearest even integer.
     * You can use this function for processing items that come in twos. For example,
     *        a packing crate accepts rows of one or two items. The crate is full when
     *        the number of items, rounded up to the nearest two, matches the crate's
     *        capacity.
     *
     * Excel Function:
     *        EVEN(number)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number Number to round
     *
     * @return int|string Rounded Number, or a string containing an error
     */
    public static function EVEN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }

        if (is_numeric($number)) {
            $significance = 2 * self::SIGN($number);

            return (int) self::CEILING($number, $significance);
        }

        return Functions::VALUE();
    }

    /**
     * FACT.
     *
     * Returns the factorial of a number.
     * The factorial of a number is equal to 1*2*3*...* number.
     *
     * Excel Function:
     *        FACT(factVal)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $factVal Factorial Value
     *
     * @return int|string Factorial, or a string containing an error
     */
    public static function FACT($factVal)
    {
        $factVal = Functions::flattenSingleValue($factVal);

        if (is_numeric($factVal)) {
            if ($factVal < 0) {
                return Functions::NAN();
            }
            $factLoop = floor($factVal);
            if ((Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) &&
                ($factVal > $factLoop)) {
                return Functions::NAN();
            }

            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
            }

            return $factorial;
        }

        return Functions::VALUE();
    }

    /**
     * FACTDOUBLE.
     *
     * Returns the double factorial of a number.
     *
     * Excel Function:
     *        FACTDOUBLE(factVal)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $factVal Factorial Value
     *
     * @return int|string Double Factorial, or a string containing an error
     */
    public static function FACTDOUBLE($factVal)
    {
        $factLoop = Functions::flattenSingleValue($factVal);

        if (is_numeric($factLoop)) {
            $factLoop = floor($factLoop);
            if ($factVal < 0) {
                return Functions::NAN();
            }
            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
                --$factLoop;
            }

            return $factorial;
        }

        return Functions::VALUE();
    }

    /**
     * FLOOR.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR(number[,significance])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function FLOOR($number, $significance = null)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);

        if (($significance === null) &&
            (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC)) {
            $significance = $number / abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if ($significance == 0.0) {
                return Functions::DIV0();
            } elseif ($number == 0.0) {
                return 0.0;
            } elseif (self::SIGN($significance) == 1) {
                return floor($number / $significance) * $significance;
            } elseif (self::SIGN($number) == -1 && self::SIGN($significance) == -1) {
                return floor($number / $significance) * $significance;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

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
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return int|mixed|string Greatest Common Divisor, or a string containing an error
     */
    public static function GCD(...$args)
    {
        $args = Functions::flattenArray($args);
        // Loop through arguments
        foreach (Functions::flattenArray($args) as $value) {
            if (!is_numeric($value)) {
                return Functions::VALUE();
            } elseif ($value < 0) {
                return Functions::NAN();
            }
        }

        $gcd = (int) array_pop($args);
        do {
            $gcd = self::evaluateGCD($gcd, (int) array_pop($args));
        } while (!empty($args));

        return $gcd;
    }

    /**
     * INT.
     *
     * Casts a floating point value to an integer
     *
     * Excel Function:
     *        INT(number)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number Number to cast to an integer
     *
     * @return int|string Integer value, or a string containing an error
     */
    public static function INT($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 0;
        } elseif (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            return (int) floor($number);
        }

        return Functions::VALUE();
    }

    /**
     * LCM.
     *
     * Returns the lowest common multiplier of a series of numbers
     * The least common multiple is the smallest positive integer that is a multiple
     * of all integer arguments number1, number2, and so on. Use LCM to add fractions
     * with different denominators.
     *
     * Excel Function:
     *        LCM(number1[,number2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return int|string Lowest Common Multiplier, or a string containing an error
     */
    public static function LCM(...$args)
    {
        $returnValue = 1;
        $allPoweredFactors = [];
        // Loop through arguments
        foreach (Functions::flattenArray($args) as $value) {
            if (!is_numeric($value)) {
                return Functions::VALUE();
            }
            if ($value == 0) {
                return 0;
            } elseif ($value < 0) {
                return Functions::NAN();
            }
            $myFactors = self::factors(floor($value));
            $myCountedFactors = array_count_values($myFactors);
            $myPoweredFactors = [];
            foreach ($myCountedFactors as $myCountedFactor => $myCountedPower) {
                $myPoweredFactors[$myCountedFactor] = pow($myCountedFactor, $myCountedPower);
            }
            foreach ($myPoweredFactors as $myPoweredValue => $myPoweredFactor) {
                if (isset($allPoweredFactors[$myPoweredValue])) {
                    if ($allPoweredFactors[$myPoweredValue] < $myPoweredFactor) {
                        $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                    }
                } else {
                    $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                }
            }
        }
        foreach ($allPoweredFactors as $allPoweredFactor) {
            $returnValue *= (int) $allPoweredFactor;
        }

        return $returnValue;
    }

    /**
     * LOG_BASE.
     *
     * Returns the logarithm of a number to a specified base. The default base is 10.
     *
     * Excel Function:
     *        LOG(number[,base])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param float $number The positive real number for which you want the logarithm
     * @param float $base The base of the logarithm. If base is omitted, it is assumed to be 10.
     *
     * @return float|string The result, or a string containing an error
     */
    public static function logBase($number = null, $base = 10)
    {
        $number = Functions::flattenSingleValue($number);
        $base = ($base === null) ? 10 : (float) Functions::flattenSingleValue($base);

        if ((!is_numeric($base)) || (!is_numeric($number))) {
            return Functions::VALUE();
        }
        if (($base <= 0) || ($number <= 0)) {
            return Functions::NAN();
        }

        return log($number, $base);
    }

    /**
     * MDETERM.
     *
     * Returns the matrix determinant of an array.
     *
     * Excel Function:
     *        MDETERM(array)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param array $matrixValues A matrix of values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MDETERM($matrixValues)
    {
        $matrixData = [];
        if (!is_array($matrixValues)) {
            $matrixValues = [[$matrixValues]];
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = [$matrixRow];
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return Functions::VALUE();
                }
                $matrixData[$row][$column] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }

        $matrix = new Matrix($matrixData);
        if (!$matrix->isSquare()) {
            return Functions::VALUE();
        }

        try {
            return $matrix->determinant();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    /**
     * MINVERSE.
     *
     * Returns the inverse matrix for the matrix stored in an array.
     *
     * Excel Function:
     *        MINVERSE(array)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param array $matrixValues A matrix of values
     *
     * @return array|string The result, or a string containing an error
     */
    public static function MINVERSE($matrixValues)
    {
        $matrixData = [];
        if (!is_array($matrixValues)) {
            $matrixValues = [[$matrixValues]];
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = [$matrixRow];
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return Functions::VALUE();
                }
                $matrixData[$row][$column] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }

        $matrix = new Matrix($matrixData);
        if (!$matrix->isSquare()) {
            return Functions::VALUE();
        }

        if ($matrix->determinant() == 0.0) {
            return Functions::NAN();
        }

        try {
            return $matrix->inverse()->toArray();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    /**
     * MMULT.
     *
     * @param array $matrixData1 A matrix of values
     * @param array $matrixData2 A matrix of values
     *
     * @return array|string The result, or a string containing an error
     */
    public static function MMULT($matrixData1, $matrixData2)
    {
        $matrixAData = $matrixBData = [];
        if (!is_array($matrixData1)) {
            $matrixData1 = [[$matrixData1]];
        }
        if (!is_array($matrixData2)) {
            $matrixData2 = [[$matrixData2]];
        }

        try {
            $rowA = 0;
            foreach ($matrixData1 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = [$matrixRow];
                }
                $columnA = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return Functions::VALUE();
                    }
                    $matrixAData[$rowA][$columnA] = $matrixCell;
                    ++$columnA;
                }
                ++$rowA;
            }
            $matrixA = new Matrix($matrixAData);
            $rowB = 0;
            foreach ($matrixData2 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = [$matrixRow];
                }
                $columnB = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return Functions::VALUE();
                    }
                    $matrixBData[$rowB][$columnB] = $matrixCell;
                    ++$columnB;
                }
                ++$rowB;
            }
            $matrixB = new Matrix($matrixBData);

            if ($columnA != $rowB) {
                return Functions::VALUE();
            }

            return $matrixA->multiply($matrixB)->toArray();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    /**
     * MOD.
     *
     * @param int $a Dividend
     * @param int $b Divisor
     *
     * @return int|string Remainder, or a string containing an error
     */
    public static function MOD($a = 1, $b = 1)
    {
        $a = (float) Functions::flattenSingleValue($a);
        $b = (float) Functions::flattenSingleValue($b);

        if ($b == 0.0) {
            return Functions::DIV0();
        } elseif (($a < 0.0) && ($b > 0.0)) {
            return $b - fmod(abs($a), $b);
        } elseif (($a > 0.0) && ($b < 0.0)) {
            return $b + fmod($a, abs($b));
        }

        return fmod($a, $b);
    }

    /**
     * MROUND.
     *
     * Rounds a number to the nearest multiple of a specified value
     *
     * @param float $number Number to round
     * @param int $multiple Multiple to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function MROUND($number, $multiple)
    {
        $number = Functions::flattenSingleValue($number);
        $multiple = Functions::flattenSingleValue($multiple);

        if ((is_numeric($number)) && (is_numeric($multiple))) {
            if ($multiple == 0) {
                return 0;
            }
            if ((self::SIGN($number)) == (self::SIGN($multiple))) {
                $multiplier = 1 / $multiple;

                return round($number * $multiplier) / $multiplier;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

    /**
     * MULTINOMIAL.
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @param array of mixed Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MULTINOMIAL(...$args)
    {
        $summer = 0;
        $divisor = 1;
        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                if ($arg < 1) {
                    return Functions::NAN();
                }
                $summer += floor($arg);
                $divisor *= self::FACT($arg);
            } else {
                return Functions::VALUE();
            }
        }

        // Return
        if ($summer > 0) {
            $summer = self::FACT($summer);

            return $summer / $divisor;
        }

        return 0;
    }

    /**
     * ODD.
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @param float $number Number to round
     *
     * @return int|string Rounded Number, or a string containing an error
     */
    public static function ODD($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 1;
        } elseif (is_bool($number)) {
            return 1;
        } elseif (is_numeric($number)) {
            $significance = self::SIGN($number);
            if ($significance == 0) {
                return 1;
            }

            $result = self::CEILING($number, $significance);
            if ($result == self::EVEN($result)) {
                $result += $significance;
            }

            return (int) $result;
        }

        return Functions::VALUE();
    }

    /**
     * POWER.
     *
     * Computes x raised to the power y.
     *
     * @param float $x
     * @param float $y
     *
     * @return float|string The result, or a string containing an error
     */
    public static function POWER($x = 0, $y = 2)
    {
        $x = Functions::flattenSingleValue($x);
        $y = Functions::flattenSingleValue($y);

        // Validate parameters
        if ($x == 0.0 && $y == 0.0) {
            return Functions::NAN();
        } elseif ($x == 0.0 && $y < 0.0) {
            return Functions::DIV0();
        }

        // Return
        $result = pow($x, $y);

        return (!is_nan($result) && !is_infinite($result)) ? $result : Functions::NAN();
    }

    /**
     * PRODUCT.
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function PRODUCT(...$args)
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($returnValue === null) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
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
     *        QUOTIENT(value1[,value2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function QUOTIENT(...$args)
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($returnValue === null) {
                    $returnValue = ($arg == 0) ? 0 : $arg;
                } else {
                    if (($returnValue == 0) || ($arg == 0)) {
                        $returnValue = 0;
                    } else {
                        $returnValue /= $arg;
                    }
                }
            }
        }

        // Return
        return (int) $returnValue;
    }

    /**
     * RAND.
     *
     * @param int $min Minimal value
     * @param int $max Maximal value
     *
     * @return int Random number
     */
    public static function RAND($min = 0, $max = 0)
    {
        $min = Functions::flattenSingleValue($min);
        $max = Functions::flattenSingleValue($max);

        if ($min == 0 && $max == 0) {
            return (mt_rand(0, 10000000)) / 10000000;
        }

        return mt_rand($min, $max);
    }

    public static function ROMAN($aValue, $style = 0)
    {
        $aValue = Functions::flattenSingleValue($aValue);
        $style = ($style === null) ? 0 : (int) Functions::flattenSingleValue($style);
        if ((!is_numeric($aValue)) || ($aValue < 0) || ($aValue >= 4000)) {
            return Functions::VALUE();
        }
        $aValue = (int) $aValue;
        if ($aValue == 0) {
            return '';
        }

        $mill = ['', 'M', 'MM', 'MMM', 'MMMM', 'MMMMM'];
        $cent = ['', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM'];
        $tens = ['', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC'];
        $ones = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'];

        $roman = '';
        while ($aValue > 5999) {
            $roman .= 'M';
            $aValue -= 1000;
        }
        $m = self::romanCut($aValue, 1000);
        $aValue %= 1000;
        $c = self::romanCut($aValue, 100);
        $aValue %= 100;
        $t = self::romanCut($aValue, 10);
        $aValue %= 10;

        return $roman . $mill[$m] . $cent[$c] . $tens[$t] . $ones[$aValue];
    }

    /**
     * ROUNDUP.
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function ROUNDUP($number, $digits)
    {
        $number = Functions::flattenSingleValue($number);
        $digits = Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            $significance = pow(10, (int) $digits);
            if ($number < 0.0) {
                return floor($number * $significance) / $significance;
            }

            return ceil($number * $significance) / $significance;
        }

        return Functions::VALUE();
    }

    /**
     * ROUNDDOWN.
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function ROUNDDOWN($number, $digits)
    {
        $number = Functions::flattenSingleValue($number);
        $digits = Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            $significance = pow(10, (int) $digits);
            if ($number < 0.0) {
                return ceil($number * $significance) / $significance;
            }

            return floor($number * $significance) / $significance;
        }

        return Functions::VALUE();
    }

    /**
     * SERIESSUM.
     *
     * Returns the sum of a power series
     *
     * @param float $x Input value to the power series
     * @param float $n Initial power to which you want to raise $x
     * @param float $m Step by which to increase $n for each term in the series
     * @param array of mixed Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SERIESSUM(...$args)
    {
        $returnValue = 0;

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);

        $x = array_shift($aArgs);
        $n = array_shift($aArgs);
        $m = array_shift($aArgs);

        if ((is_numeric($x)) && (is_numeric($n)) && (is_numeric($m))) {
            // Calculate
            $i = 0;
            foreach ($aArgs as $arg) {
                // Is it a numeric value?
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $returnValue += $arg * pow($x, $n + ($m * $i++));
                } else {
                    return Functions::VALUE();
                }
            }

            return $returnValue;
        }

        return Functions::VALUE();
    }

    /**
     * SIGN.
     *
     * Determines the sign of a number. Returns 1 if the number is positive, zero (0)
     *        if the number is 0, and -1 if the number is negative.
     *
     * @param float $number Number to round
     *
     * @return int|string sign value, or a string containing an error
     */
    public static function SIGN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            if ($number == 0.0) {
                return 0;
            }

            return $number / abs($number);
        }

        return Functions::VALUE();
    }

    /**
     * SQRTPI.
     *
     * Returns the square root of (number * pi).
     *
     * @param float $number Number
     *
     * @return float|string Square Root of Number * Pi, or a string containing an error
     */
    public static function SQRTPI($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (is_numeric($number)) {
            if ($number < 0) {
                return Functions::NAN();
            }

            return sqrt($number * M_PI);
        }

        return Functions::VALUE();
    }

    protected static function filterHiddenArgs($cellReference, $args)
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, $column] = explode('.', $index);

                return $cellReference->getWorksheet()->getRowDimension($row)->getVisible() &&
                    $cellReference->getWorksheet()->getColumnDimension($column)->getVisible();
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected static function filterFormulaArgs($cellReference, $args)
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, $column] = explode('.', $index);
                if ($cellReference->getWorksheet()->cellExists($column . $row)) {
                    //take this cell out if it contains the SUBTOTAL or AGGREGATE functions in a formula
                    $isFormula = $cellReference->getWorksheet()->getCell($column . $row)->isFormula();
                    $cellFormula = !preg_match('/^=.*\b(SUBTOTAL|AGGREGATE)\s*\(/i', $cellReference->getWorksheet()->getCell($column . $row)->getValue());

                    return !$isFormula || $cellFormula;
                }

                return true;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * SUBTOTAL.
     *
     * Returns a subtotal in a list or database.
     *
     * @param int the number 1 to 11 that specifies which function to
     *                    use in calculating subtotals within a range
     *                    list
     *            Numbers 101 to 111 shadow the functions of 1 to 11
     *                    but ignore any values in the range that are
     *                    in hidden rows or columns
     * @param array of mixed Data Series
     *
     * @return float|string
     */
    public static function SUBTOTAL(...$args)
    {
        $cellReference = array_pop($args);
        $aArgs = Functions::flattenArrayIndexed($args);
        $subtotal = array_shift($aArgs);

        // Calculate
        if ((is_numeric($subtotal)) && (!is_string($subtotal))) {
            if ($subtotal > 100) {
                $aArgs = self::filterHiddenArgs($cellReference, $aArgs);
                $subtotal -= 100;
            }

            $aArgs = self::filterFormulaArgs($cellReference, $aArgs);
            switch ($subtotal) {
                case 1:
                    return Statistical::AVERAGE($aArgs);
                case 2:
                    return Statistical::COUNT($aArgs);
                case 3:
                    return Statistical::COUNTA($aArgs);
                case 4:
                    return Statistical::MAX($aArgs);
                case 5:
                    return Statistical::MIN($aArgs);
                case 6:
                    return self::PRODUCT($aArgs);
                case 7:
                    return Statistical::STDEV($aArgs);
                case 8:
                    return Statistical::STDEVP($aArgs);
                case 9:
                    return self::SUM($aArgs);
                case 10:
                    return Statistical::VARFunc($aArgs);
                case 11:
                    return Statistical::VARP($aArgs);
            }
        }

        return Functions::VALUE();
    }

    /**
     * SUM.
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function SUM(...$args)
    {
        $returnValue = 0;

        // Loop through the arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += $arg;
            }
        }

        return $returnValue;
    }

    /**
     * SUMIF.
     *
     * Counts the number of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIF(value1[,value2[, ...]],condition)
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed $aArgs Data values
     * @param string $condition the criteria that defines which cells will be summed
     * @param mixed $sumArgs
     *
     * @return float
     */
    public static function SUMIF($aArgs, $condition, $sumArgs = [])
    {
        $returnValue = 0;

        $aArgs = Functions::flattenArray($aArgs);
        $sumArgs = Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = Functions::ifCondition($condition);
        // Loop through arguments
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = str_replace('"', '""', $arg);
                $arg = Calculation::wrapResult(strtoupper($arg));
            }

            $testCondition = '=' . $arg . $condition;
            $sumValue = array_key_exists($key, $sumArgs) ? $sumArgs[$key] : 0;

            if (is_numeric($sumValue) &&
                Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                // Is it a value within our criteria and only numeric can be added to the result
                $returnValue += $sumValue;
            }
        }

        return $returnValue;
    }

    /**
     * SUMIFS.
     *
     *    Counts the number of cells that contain numbers within the list of arguments
     *
     *    Excel Function:
     *        SUMIFS(value1[,value2[, ...]],condition)
     *
     *    @category Mathematical and Trigonometric Functions
     *
     * @param mixed $args Data values
     * @param string $condition the criteria that defines which cells will be summed
     *
     * @return float
     */
    public static function SUMIFS(...$args)
    {
        $arrayList = $args;

        // Return value
        $returnValue = 0;

        $sumArgs = Functions::flattenArray(array_shift($arrayList));
        $aArgsArray = [];
        $conditions = [];

        while (count($arrayList) > 0) {
            $aArgsArray[] = Functions::flattenArray(array_shift($arrayList));
            $conditions[] = Functions::ifCondition(array_shift($arrayList));
        }

        // Loop through each sum and see if arguments and conditions are true
        foreach ($sumArgs as $index => $value) {
            $valid = true;

            foreach ($conditions as $cidx => $condition) {
                $arg = $aArgsArray[$cidx][$index];

                // Loop through arguments
                if (!is_numeric($arg)) {
                    $arg = Calculation::wrapResult(strtoupper($arg));
                }
                $testCondition = '=' . $arg . $condition;
                if (!Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                    // Is not a value within our criteria
                    $valid = false;

                    break; // if false found, don't need to check other conditions
                }
            }

            if ($valid) {
                $returnValue += $value;
            }
        }

        // Return
        return $returnValue;
    }

    /**
     * SUMPRODUCT.
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SUMPRODUCT(...$args)
    {
        $arrayList = $args;

        $wrkArray = Functions::flattenArray(array_shift($arrayList));
        $wrkCellCount = count($wrkArray);

        for ($i = 0; $i < $wrkCellCount; ++$i) {
            if ((!is_numeric($wrkArray[$i])) || (is_string($wrkArray[$i]))) {
                $wrkArray[$i] = 0;
            }
        }

        foreach ($arrayList as $matrixData) {
            $array2 = Functions::flattenArray($matrixData);
            $count = count($array2);
            if ($wrkCellCount != $count) {
                return Functions::VALUE();
            }

            foreach ($array2 as $i => $val) {
                if ((!is_numeric($val)) || (is_string($val))) {
                    $val = 0;
                }
                $wrkArray[$i] *= $val;
            }
        }

        return array_sum($wrkArray);
    }

    /**
     * SUMSQ.
     *
     * SUMSQ returns the sum of the squares of the arguments
     *
     * Excel Function:
     *        SUMSQ(value1[,value2[, ...]])
     *
     * @category Mathematical and Trigonometric Functions
     *
     * @param mixed ...$args Data values
     *
     * @return float
     */
    public static function SUMSQ(...$args)
    {
        $returnValue = 0;

        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ($arg * $arg);
            }
        }

        return $returnValue;
    }

    /**
     * SUMX2MY2.
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float
     */
    public static function SUMX2MY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] * $array1[$i]) - ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }

    /**
     * SUMX2PY2.
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float
     */
    public static function SUMX2PY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] * $array1[$i]) + ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }

    /**
     * SUMXMY2.
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float
     */
    public static function SUMXMY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))) {
                $result += ($array1[$i] - $array2[$i]) * ($array1[$i] - $array2[$i]);
            }
        }

        return $result;
    }

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
    public static function TRUNC($value = 0, $digits = 0)
    {
        $value = Functions::flattenSingleValue($value);
        $digits = Functions::flattenSingleValue($digits);

        // Validate parameters
        if ((!is_numeric($value)) || (!is_numeric($digits))) {
            return Functions::VALUE();
        }
        $digits = floor($digits);

        // Truncate
        $adjust = pow(10, $digits);

        if (($digits > 0) && (rtrim((int) ((abs($value) - abs((int) $value)) * $adjust), '0') < $adjust / 10)) {
            return $value;
        }

        return ((int) ($value * $adjust)) / $adjust;
    }

    /**
     * SEC.
     *
     * Returns the secant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The secant of the angle
     */
    public static function SEC($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = cos($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * SECH.
     *
     * Returns the hyperbolic secant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic secant of the angle
     */
    public static function SECH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = cosh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * CSC.
     *
     * Returns the cosecant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The cosecant of the angle
     */
    public static function CSC($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = sin($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * CSCH.
     *
     * Returns the hyperbolic cosecant of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cosecant of the angle
     */
    public static function CSCH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = sinh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * COT.
     *
     * Returns the cotangent of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The cotangent of the angle
     */
    public static function COT($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = tan($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * COTH.
     *
     * Returns the hyperbolic cotangent of an angle.
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cotangent of the angle
     */
    public static function COTH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = tanh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    /**
     * ACOT.
     *
     * Returns the arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The arccotangent of the number
     */
    public static function ACOT($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return (M_PI / 2) - atan($number);
    }

    /**
     * ACOTH.
     *
     * Returns the hyperbolic arccotangent of a number.
     *
     * @param float $number Number
     *
     * @return float|string The hyperbolic arccotangent of the number
     */
    public static function ACOTH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        $result = log(($number + 1) / ($number - 1)) / 2;

        return is_nan($result) ? Functions::NAN() : $result;
    }
}
