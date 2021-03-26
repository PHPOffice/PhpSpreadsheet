<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Exception;

class MathTrig
{
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
     * @Deprecated 2.0.0 Use the funcAtan2 method in the MathTrig\Atan2 class instead
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
        return MathTrig\Atan2::funcAtan2($xCoordinate, $yCoordinate);
    }

    /**
     * BASE.
     *
     * Converts a number into a text representation with the given radix (base).
     *
     * @Deprecated 2.0.0 Use the funcBase method in the MathTrig\Base class instead
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
        return MathTrig\Base::funcBase($number, $radix, $minLength);
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

            return round(MathTrig\Fact::funcFact($numObjs) / MathTrig\Fact::funcFact($numObjs - $numInSet)) / MathTrig\Fact::funcFact($numInSet);
        }

        return Functions::VALUE();
    }

    /**
     * EVEN.
     *
     * @Deprecated 2.0.0 Use the funcEven method in the MathTrig\Even class instead
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
        return MathTrig\Even::funcEven($number);
    }

    /**
     * Helper function for Even.
     *
     * @Deprecated 2.0.0 Use the getEven method in the MathTrig\Helpers class instead
     */
    public static function getEven(float $number): int
    {
        return (int) MathTrig\Helpers::getEven($number);
    }

    /**
     * FACT.
     *
     * Returns the factorial of a number.
     * The factorial of a number is equal to 1*2*3*...* number.
     *
     * @Deprecated 2.0.0 Use the funcFact method in the MathTrig\Fact class instead
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
        return MathTrig\Fact::funcFact($factVal);
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
     * @Deprecated 2.0.0 Use the funcLcm method in the MathTrig\Lcm class instead
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
        return MathTrig\Lcm::funcLcm(...$args);
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
     * @Deprecated 2.0.0 Use the funcMDeterm method in the MathTrig\MatrixFuncs class instead
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
        return MathTrig\MatrixFunctions::funcMDeterm($matrixValues);
    }

    /**
     * MINVERSE.
     *
     * Returns the inverse matrix for the matrix stored in an array.
     *
     * @Deprecated 2.0.0 Use the funcMInverse method in the MathTrig\MatrixFuncs class instead
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
        return MathTrig\MatrixFunctions::funcMInverse($matrixValues);
    }

    /**
     * MMULT.
     *
     * @Deprecated 2.0.0 Use the funcMMult method in the MathTrig\MatrixFuncs class instead
     *
     * @param array $matrixData1 A matrix of values
     * @param array $matrixData2 A matrix of values
     *
     * @return array|string The result, or a string containing an error
     */
    public static function MMULT($matrixData1, $matrixData2)
    {
        return MathTrig\MatrixFunctions::funcMMult($matrixData1, $matrixData2);
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
        return MathTrig\Multinomial::funcMultinomial(...$args);
    }

    /**
     * ODD.
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @Deprecated 2.0.0 Use the funcOdd method in the MathTrig\Odd class instead
     *
     * @param float $number Number to round
     *
     * @return int|string Rounded Number, or a string containing an error
     */
    public static function ODD($number)
    {
        return MathTrig\Odd::funcOdd($number);
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
     * @Deprecated 2.0.0 Use the funcProduct method in the MathTrig\Product class instead
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function PRODUCT(...$args)
    {
        return MathTrig\Product::funcProduct(...$args);
    }

    /**
     * QUOTIENT.
     *
     * QUOTIENT function returns the integer portion of a division. Numerator is the divided number
     *        and denominator is the divisor.
     *
     * @Deprecated 2.0.0 Use the funcQuotient method in the MathTrig\Quotient class instead
     *
     * Excel Function:
     *        QUOTIENT(value1[,value2[, ...]])
     *
     * @param mixed $numerator
     * @param mixed $denominator
     *
     * @return int|string
     */
    public static function QUOTIENT($numerator, $denominator)
    {
        return MathTrig\Quotient::funcQuotient($numerator, $denominator);
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
     * @Deprecated 2.0.0 Use the funcSeriesSum method in the MathTrig\SeriesSum class instead
     *
     * @param mixed $x Input value
     * @param mixed $n Initial power
     * @param mixed $m Step
     * @param mixed[] $args An array of coefficients for the Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SERIESSUM($x, $n, $m, ...$args)
    {
        return MathTrig\SeriesSum::funcSeriesSum($x, $n, $m, ...$args);
    }

    /**
     * SIGN.
     *
     * Determines the sign of a number. Returns 1 if the number is positive, zero (0)
     *        if the number is 0, and -1 if the number is negative.
     *
     * @Deprecated 2.0.0 Use the funcSign method in the MathTrig\Sign class instead
     *
     * @param float $number Number to round
     *
     * @return int|string sign value, or a string containing an error
     */
    public static function SIGN($number)
    {
        return MathTrig\Sign::funcSign($number);
    }

    /**
     * returnSign = returns 0/-1/+1.
     *
     * @Deprecated 2.0.0 Use the returnSign method in the MathTrig\Helpers class instead
     */
    public static function returnSign(float $number): int
    {
        return MathTrig\Helpers::returnSign($number);
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

    /**
     * SUBTOTAL.
     *
     * Returns a subtotal in a list or database.
     *
     * @Deprecated 2.0.0 Use the funcSubtotal method in the MathTrig\Subtotal class instead
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
        return MathTrig\Subtotal::funcSubtotal($functionType, ...$args);
    }

    /**
     * SUM.
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * @Deprecated 2.0.0 Use the funcSumNoStrings method in the MathTrig\Sum class instead
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function SUM(...$args)
    {
        return MathTrig\Sum::funcSum(...$args);
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
     * @return float|string
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
     * @return float|string
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
     * @Deprecated 2.0.0 Use the funcSumProduct method in the MathTrig\SumProduct class instead
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SUMPRODUCT(...$args)
    {
        return MathTrig\SumProduct::funcSumProduct(...$args);
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
     * @Deprecated 2.0.0 Use the funcSec method in the MathTrig\Sec class instead
     *
     * @param float $angle Number
     *
     * @return float|string The secant of the angle
     */
    public static function SEC($angle)
    {
        return MathTrig\Sec::funcSec($angle);
    }

    /**
     * SECH.
     *
     * Returns the hyperbolic secant of an angle.
     *
     * @Deprecated 2.0.0 Use the funcSech method in the MathTrig\Sech class instead
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic secant of the angle
     */
    public static function SECH($angle)
    {
        return MathTrig\Sech::funcSech($angle);
    }

    /**
     * CSC.
     *
     * Returns the cosecant of an angle.
     *
     * @Deprecated 2.0.0 Use the funcCsc method in the MathTrig\Csc class instead
     *
     * @param float $angle Number
     *
     * @return float|string The cosecant of the angle
     */
    public static function CSC($angle)
    {
        return MathTrig\Csc::funcCsc($angle);
    }

    /**
     * CSCH.
     *
     * Returns the hyperbolic cosecant of an angle.
     *
     * @Deprecated 2.0.0 Use the funcCsch method in the MathTrig\Csch class instead
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cosecant of the angle
     */
    public static function CSCH($angle)
    {
        return MathTrig\Csch::funcCsch($angle);
    }

    /**
     * COT.
     *
     * Returns the cotangent of an angle.
     *
     * @Deprecated 2.0.0 Use the funcCot method in the MathTrig\Cot class instead
     *
     * @param float $angle Number
     *
     * @return float|string The cotangent of the angle
     */
    public static function COT($angle)
    {
        return MathTrig\Cot::funcCot($angle);
    }

    /**
     * COTH.
     *
     * Returns the hyperbolic cotangent of an angle.
     *
     * @Deprecated 2.0.0 Use the funcCoth method in the MathTrig\Coth class instead
     *
     * @param float $angle Number
     *
     * @return float|string The hyperbolic cotangent of the angle
     */
    public static function COTH($angle)
    {
        return MathTrig\Coth::funcCoth($angle);
    }

    /**
     * ACOT.
     *
     * Returns the arccotangent of a number.
     *
     * @Deprecated 2.0.0 Use the funcAcot method in the MathTrig\Acot class instead
     *
     * @param float $number Number
     *
     * @return float|string The arccotangent of the number
     */
    public static function ACOT($number)
    {
        return MathTrig\Acot::funcAcot($number);
    }

    /**
     * Return NAN or value depending on argument.
     *
     * @Deprecated 2.0.0 Use the numberOrNan method in the MathTrig\Helpers class instead
     *
     * @param float $result Number
     *
     * @return float|string
     */
    public static function numberOrNan($result)
    {
        return MathTrig\Helpers::numberOrNan($result);
    }

    /**
     * ACOTH.
     *
     * Returns the hyperbolic arccotangent of a number.
     *
     * @Deprecated 2.0.0 Use the funcAcoth method in the MathTrig\Acoth class instead
     *
     * @param float $number Number
     *
     * @return float|string The hyperbolic arccotangent of the number
     */
    public static function ACOTH($number)
    {
        return MathTrig\Acoth::funcAcoth($number);
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
     * @Deprecated 2.0.0 Use the funcAcos method in the MathTrig\Acos class instead
     *
     * Returns the result of builtin function acos after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinACOS($number)
    {
        return MathTrig\Acos::funcAcos($number);
    }

    /**
     * ACOSH.
     *
     * Returns the result of builtin function acosh after validating args.
     *
     * @Deprecated 2.0.0 Use the funcAcosh method in the MathTrig\Acosh class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinACOSH($number)
    {
        return MathTrig\Acosh::funcAcosh($number);
    }

    /**
     * ASIN.
     *
     * Returns the result of builtin function asin after validating args.
     *
     * @Deprecated 2.0.0 Use the funcAsin method in the MathTrig\Asin class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinASIN($number)
    {
        return MathTrig\Asin::funcAsin($number);
    }

    /**
     * ASINH.
     *
     * Returns the result of builtin function asinh after validating args.
     *
     * @Deprecated 2.0.0 Use the funcAsinh method in the MathTrig\Asinh class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinASINH($number)
    {
        return MathTrig\Asinh::funcAsinh($number);
    }

    /**
     * ATAN.
     *
     * Returns the result of builtin function atan after validating args.
     *
     * @Deprecated 2.0.0 Use the funcAtan method in the MathTrig\Atan class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinATAN($number)
    {
        return MathTrig\Atan::funcAtan($number);
    }

    /**
     * ATANH.
     *
     * Returns the result of builtin function atanh after validating args.
     *
     * @Deprecated 2.0.0 Use the funcAtanh method in the MathTrig\Atanh class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinATANH($number)
    {
        return MathTrig\Atanh::funcAtanh($number);
    }

    /**
     * COS.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @Deprecated 2.0.0 Use the funcCos method in the MathTrig\Cos class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinCOS($number)
    {
        return MathTrig\Cos::funcCos($number);
    }

    /**
     * COSH.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @Deprecated 2.0.0 Use the funcCosh method in the MathTrig\Cosh class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinCOSH($number)
    {
        return MathTrig\Cosh::funcCosh($number);
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
     * @Deprecated 2.0.0 Use the funcSin method in the MathTrig\Sin class instead
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinSIN($number)
    {
        return MathTrig\Sin::funcSin($number);
    }

    /**
     * SINH.
     *
     * @Deprecated 2.0.0 Use the funcSinh method in the MathTrig\Sinh class instead
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinSINH($number)
    {
        return MathTrig\Sinh::funcSinh($number);
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
     * @Deprecated 2.0.0 Use the funcTan method in the MathTrig\Tan class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinTAN($number)
    {
        return MathTrig\Tan::funcTan($number);
    }

    /**
     * TANH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @Deprecated 2.0.0 Use the funcTanh method in the MathTrig\Tanh class instead
     *
     * @param mixed $number Should be numeric
     *
     * @return float|string Rounded number
     */
    public static function builtinTANH($number)
    {
        return MathTrig\Tanh::funcTanh($number);
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @Deprecated 2.0.0 Use the validateNumericNullBool method in the MathTrig\Helpers class instead
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
