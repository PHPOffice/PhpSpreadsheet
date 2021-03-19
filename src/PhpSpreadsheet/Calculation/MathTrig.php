<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Exception;
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

    private static function strSplit(string $roman): array
    {
        $rslt = str_split($roman);

        return is_array($rslt) ? $rslt : [];
    }

    /**
     * ARABIC.
     *
     * Converts a Roman numeral to an Arabic numeral.
     *
     * Excel Function:
     *        ARABIC(text)
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
        $negativeNumber = $roman[0] === '-';
        if ($negativeNumber) {
            $roman = substr($roman, 1);
        }

        try {
            $arabic = self::calculateArabic(self::strSplit($roman));
        } catch (Exception $e) {
            return Functions::VALUE(); // Invalid character detected
        }

        if ($negativeNumber) {
            $arabic *= -1; // The number should be negative
        }

        return $arabic;
    }

    /**
     * Recursively calculate the arabic value of a roman numeral.
     *
     * @param int $sum
     * @param int $subtract
     *
     * @return int
     */
    protected static function calculateArabic(array $roman, &$sum = 0, $subtract = 0)
    {
        $lookup = [
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        ];

        $numeral = array_shift($roman);
        if (!isset($lookup[$numeral])) {
            throw new Exception('Invalid character detected');
        }

        $arabic = $lookup[$numeral];
        if (count($roman) > 0 && isset($lookup[$roman[0]]) && $arabic < $lookup[$roman[0]]) {
            $subtract += $arabic;
        } else {
            $sum += ($arabic - $subtract);
            $subtract = 0;
        }

        if (count($roman) > 0) {
            self::calculateArabic($roman, $sum, $subtract);
        }

        return $sum;
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
     * @param float $xCoordinate the x-coordinate of the point
     * @param float $yCoordinate the y-coordinate of the point
     *
     * @return float|string the inverse tangent of the specified x- and y-coordinates, or a string containing an error
     */
    public static function ATAN2($xCoordinate = null, $yCoordinate = null)
    {
        $xCoordinate = Functions::flattenSingleValue($xCoordinate);
        $yCoordinate = Functions::flattenSingleValue($yCoordinate);

        $xCoordinate = $xCoordinate ?? 0.0;
        $yCoordinate = $yCoordinate ?? 0.0;

        if (
            ((is_numeric($xCoordinate)) || (is_bool($xCoordinate))) &&
            ((is_numeric($yCoordinate))) || (is_bool($yCoordinate))
        ) {
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
     * @Deprecated 1.17.0
     *
     * @see Use the funcCeiling() method in the MathTrig\Ceiling class instead
     *
     * @param float $number the number you want to round
     * @param float $significance the multiple to which you want to round
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function CEILING($number, $significance = null)
    {
        return MathTrig\Ceiling::funcCeiling($number, $significance);
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
            return self::getEven((float) $number);
        }

        return Functions::VALUE();
    }

    public static function getEven(float $number): int
    {
        $significance = 2 * self::returnSign($number);

        return (int) MathTrig\Ceiling::funcCeiling($number, $significance);
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
            if (
                (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) &&
                ($factVal > $factLoop)
            ) {
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
     * @Deprecated 1.17.0
     *
     * @see Use the funcFloor() method in the MathTrig\Floor class instead
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function FLOOR($number, $significance = null)
    {
        return MathTrig\Floor::funcFloor($number, $significance);
    }

    /**
     * FLOOR.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.MATH(number[,significance[,mode]])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcFloorMath() method in the MathTrig\FloorMath class instead
     *
     * @param float $number Number to round
     * @param float $significance Significance
     * @param int $mode direction to round negative numbers
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function FLOORMATH($number, $significance = null, $mode = 0)
    {
        return MathTrig\FloorMath::funcFloorMath($number, $significance, $mode);
    }

    /**
     * FLOOR.PRECISE.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.PRECISE(number[,significance])
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcFloorPrecise() method in the MathTrig\FloorPrecise class instead
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function FLOORPRECISE($number, $significance = 1)
    {
        return MathTrig\FloorPrecise::funcFloorPrecise($number, $significance);
    }

    private static function evaluateGCD($a, $b)
    {
        return $b ? self::evaluateGCD($b, $a % $b) : $a;
    }

    /**
     * INT.
     *
     * Casts a floating point value to an integer
     *
     * Excel Function:
     *        INT(number)
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcInt() method in the MathTrig\IntClass class instead
     *
     * @param float $number Number to cast to an integer
     *
     * @return int|string Integer value, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function INT($number)
    {
        return MathTrig\IntClass::funcInt($number);
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
                $myPoweredFactors[$myCountedFactor] = $myCountedFactor ** $myCountedPower;
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
     * @Deprecated 1.17.0
     *
     * @see Use the funcMround() method in the MathTrig\Mround class instead
     *
     * @param float $number Number to round
     * @param int $multiple Multiple to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function MROUND($number, $multiple)
    {
        return MathTrig\Mround::funcMround($number, $multiple);
    }

    /**
     * MULTINOMIAL.
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @param mixed[] $args An array of mixed values for the Data Series
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
            $significance = self::returnSign($number);
            if ($significance == 0) {
                return 1;
            }

            $result = MathTrig\Ceiling::funcCeiling($number, $significance);
            if (is_string($result)) {
                return $result;
            }
            if ($result == self::getEven((float) $result)) {
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
        $result = $x ** $y;

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

    /**
     * ROMAN.
     *
     * Converts a number to Roman numeral
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcRoman() method in the MathTrig\Roman class instead
     *
     * @param mixed $aValue Number to convert
     * @param mixed $style Number indicating one of five possible forms
     *
     * @return string Roman numeral, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function ROMAN($aValue, $style = 0)
    {
        return MathTrig\Roman::funcRoman($aValue, $style);
    }

    /**
     * ROUNDUP.
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcRoundUp() method in the MathTrig\RoundUp class instead
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function ROUNDUP($number, $digits)
    {
        return MathTrig\RoundUp::funcRoundUp($number, $digits);
    }

    /**
     * ROUNDDOWN.
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @Deprecated 1.17.0
     *
     * @see Use the funcRoundDown() method in the MathTrig\RoundDown class instead
     *
     * @param float $number Number to round
     * @param int $digits Number of digits to which you want to round $number
     *
     * @return float|string Rounded Number, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function ROUNDDOWN($number, $digits)
    {
        return MathTrig\RoundDown::funcRoundDown($number, $digits);
    }

    /**
     * SERIESSUM.
     *
     * Returns the sum of a power series
     *
     * @param mixed[] $args An array of mixed values for the Data Series
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
                    $returnValue += $arg * $x ** ($n + ($m * $i++));
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
            return self::returnSign($number);
        }

        return Functions::VALUE();
    }

    public static function returnSign(float $number): int
    {
        return $number ? (($number > 0) ? 1 : -1) : 0;
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
     * @param int $functionType
     *            A number 1 to 11 that specifies which function to
     *                    use in calculating subtotals within a range
     *                    list
     *            Numbers 101 to 111 shadow the functions of 1 to 11
     *                    but ignore any values in the range that are
     *                    in hidden rows or columns
     * @param mixed[] $args A mixed data series of values
     *
     * @return float|string
     */
    public static function SUBTOTAL($functionType, ...$args)
    {
        $cellReference = array_pop($args);
        $aArgs = Functions::flattenArrayIndexed($args);
        $subtotal = Functions::flattenSingleValue($functionType);

        // Calculate
        if ((is_numeric($subtotal)) && (!is_string($subtotal))) {
            if ($subtotal > 100) {
                $aArgs = self::filterHiddenArgs($cellReference, $aArgs);
                $subtotal -= 100;
            }

            $aArgs = self::filterFormulaArgs($cellReference, $aArgs);
            switch ($subtotal) {
                case 1:
                    return Statistical\Averages::AVERAGE($aArgs);
                case 2:
                    return Statistical\Counts::COUNT($aArgs);
                case 3:
                    return Statistical\Counts::COUNTA($aArgs);
                case 4:
                    return Statistical\Maximum::MAX($aArgs);
                case 5:
                    return Statistical\Minimum::MIN($aArgs);
                case 6:
                    return self::PRODUCT($aArgs);
                case 7:
                    return Statistical\StandardDeviations::STDEV($aArgs);
                case 8:
                    return Statistical\StandardDeviations::STDEVP($aArgs);
                case 9:
                    return self::SUM($aArgs);
                case 10:
                    return Statistical\Variances::VAR($aArgs);
                case 11:
                    return Statistical\Variances::VARP($aArgs);
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
            } elseif (Functions::isError($arg)) {
                return $arg;
            }
        }

        return $returnValue;
    }

    /**
     * SUMIF.
     *
     * Totals the values of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIF(range, criteria, [sum_range])
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::SUMIF()
     *      Use the SUMIF() method in the Statistical\Conditional class instead
     *
     * @param mixed $range Data values
     * @param string $criteria the criteria that defines which cells will be summed
     * @param mixed $sumRange
     *
     * @return float
     */
    public static function SUMIF($range, $criteria, $sumRange = [])
    {
        return Statistical\Conditional::SUMIF($range, $criteria, $sumRange);
    }

    /**
     * SUMIFS.
     *
     *    Totals the values of cells that contain numbers within the list of arguments
     *
     *    Excel Function:
     *        SUMIFS(sum_range, criteria_range1, criteria1, [criteria_range2, criteria2], ...)
     *
     * @Deprecated 1.17.0
     *
     * @see Statistical\Conditional::SUMIFS()
     *      Use the SUMIFS() method in the Statistical\Conditional class instead
     *
     * @param mixed $args Data values
     *
     * @return float
     */
    public static function SUMIFS(...$args)
    {
        return Statistical\Conditional::SUMIFS(...$args);
    }

    /**
     * SUMPRODUCT.
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
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
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
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
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
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
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
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
     * @Deprecated 1.17.0
     *
     * @see Use the funcTrunc() method in the MathTrig\Trunc class instead
     *
     * @param float $value
     * @param int $digits
     *
     * @return float|string Truncated value, or a string containing an error
     *
     * @codeCoverageIgnore
     */
    public static function TRUNC($value = 0, $digits = 0)
    {
        return MathTrig\Trunc::funcTrunc($value, $digits);
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

        return self::verySmallDivisor($result) ? Functions::DIV0() : (1 / $result);
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

        return self::verySmallDivisor($result) ? Functions::DIV0() : (1 / $result);
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

        $result = sin($angle);

        return self::verySmallDivisor($result) ? Functions::DIV0() : (cos($angle) / $result);
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
     * Return NAN or value depending on argument.
     *
     * @param float $result Number
     *
     * @return float|string
     */
    public static function numberOrNan($result)
    {
        return is_nan($result) ? Functions::NAN() : $result;
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

        return self::numberOrNan($result);
    }

    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @Deprecated 1.17.0
     *
     * @see Use the builtinRound() method in the MathTrig\Round class instead
     *
     * @param mixed $number Should be numeric
     * @param mixed $precision Should be int
     *
     * @return float|string Rounded number
     *
     * @codeCoverageIgnore
     */
    public static function builtinROUND($number, $precision)
    {
        return MathTrig\Round::builtinRound($number, $precision);
    }

    /**
     * ABS.
     *
     * Returns the result of builtin function abs after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|int|string Rounded number
     */
    public static function builtinABS($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return abs($number);
    }

    /**
     * ACOS.
     *
     * Returns the result of builtin function acos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinACOS($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::numberOrNan(acos($number));
    }

    /**
     * ACOSH.
     *
     * Returns the result of builtin function acosh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinACOSH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::numberOrNan(acosh($number));
    }

    /**
     * ASIN.
     *
     * Returns the result of builtin function asin after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinASIN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::numberOrNan(asin($number));
    }

    /**
     * ASINH.
     *
     * Returns the result of builtin function asinh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinASINH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return asinh($number);
    }

    /**
     * ASIN.
     *
     * Returns the result of builtin function atan after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinATAN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::numberOrNan(atan($number));
    }

    /**
     * ATANH.
     *
     * Returns the result of builtin function atanh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinATANH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return atanh($number);
    }

    /**
     * COS.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinCOS($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return cos($number);
    }

    /**
     * COSH.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinCOSH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return cosh($number);
    }

    /**
     * DEGREES.
     *
     * Returns the result of builtin function rad2deg after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinDEGREES($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return rad2deg($number);
    }

    /**
     * EXP.
     *
     * Returns the result of builtin function exp after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinEXP($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return exp($number);
    }

    /**
     * LN.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinLN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return log($number);
    }

    /**
     * LOG10.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinLOG10($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return log10($number);
    }

    /**
     * RADIANS.
     *
     * Returns the result of builtin function deg2rad after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinRADIANS($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return deg2rad($number);
    }

    /**
     * SIN.
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinSIN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return sin($number);
    }

    /**
     * SINH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinSINH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return sinh($number);
    }

    /**
     * SQRT.
     *
     * Returns the result of builtin function sqrt after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinSQRT($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::numberOrNan(sqrt($number));
    }

    /**
     * TAN.
     *
     * Returns the result of builtin function tan after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinTAN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return self::verySmallDivisor(cos($number)) ? Functions::DIV0() : tan($number);
    }

    /**
     * TANH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinTANH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return tanh($number);
    }

    private static function verySmallDivisor(float $number): bool
    {
        return abs($number) < 1.0E-12;
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @param mixed $number
     */
    public static function nullFalseTrueToNumber(&$number): void
    {
        $number = Functions::flattenSingleValue($number);
        if ($number === null) {
            $number = 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }
    }
}
