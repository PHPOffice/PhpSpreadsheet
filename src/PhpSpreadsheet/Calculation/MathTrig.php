<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

/**
 * @deprecated 1.18.0
 */
class MathTrig
{
    /**
     * ARABIC.
     *
     * Converts a Roman numeral to an Arabic numeral.
     *
     * Excel Function:
     *        ARABIC(text)
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Arabic class instead
     * @see MathTrig\Arabic::evaluate()
     *
     * @param array|string $roman
     *
     * @return array|int|string the arabic numberal contrived from the roman numeral
     */
    public static function ARABIC($roman)
    {
        return MathTrig\Arabic::evaluate($roman);
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
     * @deprecated 1.18.0
     *      Use the atan2 method in the MathTrig\Trig\Tangent class instead
     * @see MathTrig\Trig\Tangent::atan2()
     *
     * @param array|float $xCoordinate the x-coordinate of the point
     * @param array|float $yCoordinate the y-coordinate of the point
     *
     * @return array|float|string the inverse tangent of the specified x- and y-coordinates, or a string containing an error
     */
    public static function ATAN2($xCoordinate = null, $yCoordinate = null): string|float|array
    {
        return MathTrig\Trig\Tangent::atan2($xCoordinate, $yCoordinate);
    }

    /**
     * BASE.
     *
     * Converts a number into a text representation with the given radix (base).
     *
     * Excel Function:
     *        BASE(Number, Radix [Min_length])
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Base class instead
     * @see MathTrig\Base::evaluate()
     *
     * @param float $number
     * @param float $radix
     * @param int $minLength
     *
     * @return array|string the text representation with the given radix (base)
     */
    public static function BASE($number, $radix, $minLength = null): string|array
    {
        return MathTrig\Base::evaluate($number, $radix, $minLength);
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
     * @deprecated 1.17.0
     *      Use the ceiling() method in the MathTrig\Ceiling class instead
     * @see MathTrig\Ceiling::ceiling()
     *
     * @param float $number the number you want to round
     * @param float $significance the multiple to which you want to round
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function CEILING($number, $significance = null)
    {
        return MathTrig\Ceiling::ceiling($number, $significance);
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
     * @deprecated 1.18.0
     *      Use the withoutRepetition() method in the MathTrig\Combinations class instead
     * @see MathTrig\Combinations::withoutRepetition()
     *
     * @param array|int $numObjs Number of different objects
     * @param array|int $numInSet Number of objects in each combination
     *
     * @return array|float|int|string Number of combinations, or a string containing an error
     */
    public static function COMBIN($numObjs, $numInSet)
    {
        return MathTrig\Combinations::withoutRepetition($numObjs, $numInSet);
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
     * @deprecated 1.18.0
     *      Use the even() method in the MathTrig\Round class instead
     * @see MathTrig\Round::even()
     *
     * @param array|float $number Number to round
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function EVEN($number): string|float|array
    {
        return MathTrig\Round::even($number);
    }

    /**
     * Helper function for Even.
     *
     * @deprecated 1.18.0
     *      Use the evaluate() method in the MathTrig\Helpers class instead
     * @see MathTrig\Helpers::getEven()
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
     * Excel Function:
     *        FACT(factVal)
     *
     * @deprecated 1.18.0
     *      Use the fact() method in the MathTrig\Factorial class instead
     * @see MathTrig\Factorial::fact()
     *
     * @param array|float $factVal Factorial Value
     *
     * @return array|float|int|string Factorial, or a string containing an error
     */
    public static function FACT($factVal)
    {
        return MathTrig\Factorial::fact($factVal);
    }

    /**
     * FACTDOUBLE.
     *
     * Returns the double factorial of a number.
     *
     * Excel Function:
     *        FACTDOUBLE(factVal)
     *
     * @deprecated 1.18.0
     *      Use the factDouble() method in the MathTrig\Factorial class instead
     * @see MathTrig\Factorial::factDouble()
     *
     * @param array|float $factVal Factorial Value
     *
     * @return array|float|int|string Double Factorial, or a string containing an error
     */
    public static function FACTDOUBLE($factVal)
    {
        return MathTrig\Factorial::factDouble($factVal);
    }

    /**
     * FLOOR.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR(number[,significance])
     *
     * @deprecated 1.17.0
     *      Use the floor() method in the MathTrig\Floor class instead
     * @see MathTrig\Floor::floor()
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function FLOOR($number, $significance = null)
    {
        return MathTrig\Floor::floor($number, $significance);
    }

    /**
     * FLOOR.MATH.
     *
     * Round a number down to the nearest integer or to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.MATH(number[,significance[,mode]])
     *
     * @deprecated 1.17.0
     *      Use the math() method in the MathTrig\Floor class instead
     * @see MathTrig\Floor::math()
     *
     * @param float $number Number to round
     * @param float $significance Significance
     * @param int $mode direction to round negative numbers
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function FLOORMATH($number, $significance = null, $mode = 0)
    {
        return MathTrig\Floor::math($number, $significance, $mode);
    }

    /**
     * FLOOR.PRECISE.
     *
     * Rounds number down, toward zero, to the nearest multiple of significance.
     *
     * Excel Function:
     *        FLOOR.PRECISE(number[,significance])
     *
     * @deprecated 1.17.0
     *      Use the precise() method in the MathTrig\Floor class instead
     * @see MathTrig\Floor::precise()
     *
     * @param float $number Number to round
     * @param float $significance Significance
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function FLOORPRECISE($number, $significance = 1)
    {
        return MathTrig\Floor::precise($number, $significance);
    }

    /**
     * INT.
     *
     * Casts a floating point value to an integer
     *
     * Excel Function:
     *        INT(number)
     *
     * @deprecated 1.17.0
     *      Use the evaluate() method in the MathTrig\IntClass class instead
     * @see MathTrig\IntClass::evaluate()
     *
     * @param array|float $number Number to cast to an integer
     *
     * @return array|int|string Integer value, or a string containing an error
     */
    public static function INT($number)
    {
        return MathTrig\IntClass::evaluate($number);
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
     * @deprecated 1.18.0
     *      Use the evaluate() method in the MathTrig\Gcd class instead
     * @see MathTrig\Gcd::evaluate()
     *
     * @param mixed ...$args Data values
     *
     * @return int|mixed|string Greatest Common Divisor, or a string containing an error
     */
    public static function GCD(mixed ...$args)
    {
        return MathTrig\Gcd::evaluate(...$args);
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
     * @deprecated 1.18.0
     *      Use the evaluate() method in the MathTrig\Lcm class instead
     * @see MathTrig\Lcm::evaluate()
     *
     * @param mixed ...$args Data values
     *
     * @return int|string Lowest Common Multiplier, or a string containing an error
     */
    public static function LCM(mixed ...$args)
    {
        return MathTrig\Lcm::evaluate(...$args);
    }

    /**
     * LOG_BASE.
     *
     * Returns the logarithm of a number to a specified base. The default base is 10.
     *
     * Excel Function:
     *        LOG(number[,base])
     *
     * @deprecated 1.18.0
     *      Use the withBase() method in the MathTrig\Logarithms class instead
     * @see MathTrig\Logarithms::withBase()
     *
     * @param float $number The positive real number for which you want the logarithm
     * @param float $base The base of the logarithm. If base is omitted, it is assumed to be 10.
     *
     * @return array|float|string The result, or a string containing an error
     */
    public static function logBase($number, $base = 10): string|float|array
    {
        return MathTrig\Logarithms::withBase($number, $base);
    }

    /**
     * MDETERM.
     *
     * Returns the matrix determinant of an array.
     *
     * Excel Function:
     *        MDETERM(array)
     *
     * @deprecated 1.18.0
     *      Use the determinant() method in the MathTrig\MatrixFunctions class instead
     * @see MathTrig\MatrixFunctions::determinant()
     *
     * @param array $matrixValues A matrix of values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MDETERM($matrixValues)
    {
        return MathTrig\MatrixFunctions::determinant($matrixValues);
    }

    /**
     * MINVERSE.
     *
     * Returns the inverse matrix for the matrix stored in an array.
     *
     * Excel Function:
     *        MINVERSE(array)
     *
     * @deprecated 1.18.0
     *      Use the inverse() method in the MathTrig\MatrixFunctions class instead
     * @see MathTrig\MatrixFunctions::inverse()
     *
     * @param array $matrixValues A matrix of values
     *
     * @return array|string The result, or a string containing an error
     */
    public static function MINVERSE($matrixValues): string|array
    {
        return MathTrig\MatrixFunctions::inverse($matrixValues);
    }

    /**
     * MMULT.
     *
     * @deprecated 1.18.0
     *      Use the multiply() method in the MathTrig\MatrixFunctions class instead
     * @see MathTrig\MatrixFunctions::multiply()
     *
     * @param array $matrixData1 A matrix of values
     * @param array $matrixData2 A matrix of values
     *
     * @return array|string The result, or a string containing an error
     */
    public static function MMULT($matrixData1, $matrixData2): string|array
    {
        return MathTrig\MatrixFunctions::multiply($matrixData1, $matrixData2);
    }

    /**
     * MOD.
     *
     * @deprecated 1.18.0
     *      Use the mod() method in the MathTrig\Operations class instead
     * @see MathTrig\Operations::mod()
     *
     * @param int $a Dividend
     * @param int $b Divisor
     *
     * @return array|float|int|string Remainder, or a string containing an error
     */
    public static function MOD($a = 1, $b = 1)
    {
        return MathTrig\Operations::mod($a, $b);
    }

    /**
     * MROUND.
     *
     * Rounds a number to the nearest multiple of a specified value
     *
     * @deprecated 1.17.0
     *      Use the multiple() method in the MathTrig\Mround class instead
     * @see MathTrig\Round::multiple()
     *
     * @param float $number Number to round
     * @param array|int $multiple Multiple to which you want to round $number
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function MROUND($number, $multiple): string|int|float|array
    {
        return MathTrig\Round::multiple($number, $multiple);
    }

    /**
     * MULTINOMIAL.
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @deprecated 1.18.0
     *      Use the multinomial method in the MathTrig\Factorial class instead
     * @see MathTrig\Factorial::multinomial()
     *
     * @param mixed[] $args An array of mixed values for the Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function MULTINOMIAL(...$args)
    {
        return MathTrig\Factorial::multinomial(...$args);
    }

    /**
     * ODD.
     *
     * Returns number rounded up to the nearest odd integer.
     *
     * @deprecated 1.18.0
     *      Use the odd method in the MathTrig\Round class instead
     * @see MathTrig\Round::odd()
     *
     * @param array|float $number Number to round
     *
     * @return array|float|int|string Rounded Number, or a string containing an error
     */
    public static function ODD($number): string|int|float|array
    {
        return MathTrig\Round::odd($number);
    }

    /**
     * POWER.
     *
     * Computes x raised to the power y.
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Power class instead
     * @see MathTrig\Operations::power()
     *
     * @param float $x
     * @param float $y
     *
     * @return array|float|int|string The result, or a string containing an error
     */
    public static function POWER($x = 0, $y = 2)
    {
        return MathTrig\Operations::power($x, $y);
    }

    /**
     * PRODUCT.
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * @deprecated 1.18.0
     *      Use the product method in the MathTrig\Operations class instead
     * @see MathTrig\Operations::product()
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function PRODUCT(mixed ...$args)
    {
        return MathTrig\Operations::product(...$args);
    }

    /**
     * QUOTIENT.
     *
     * QUOTIENT function returns the integer portion of a division. Numerator is the divided number
     *        and denominator is the divisor.
     *
     * @deprecated 1.18.0
     *      Use the quotient method in the MathTrig\Operations class instead
     * @see MathTrig\Operations::quotient()
     *
     * Excel Function:
     *        QUOTIENT(value1[,value2[, ...]])
     *
     * @return array|int|string
     */
    public static function QUOTIENT(mixed $numerator, mixed $denominator)
    {
        return MathTrig\Operations::quotient($numerator, $denominator);
    }

    /**
     * RAND/RANDBETWEEN.
     *
     * @deprecated 1.18.0
     *      Use the randBetween or randBetween method in the MathTrig\Random class instead
     * @see MathTrig\Random::randBetween()
     *
     * @param int $min Minimal value
     * @param int $max Maximal value
     *
     * @return array|int|string Random number
     */
    public static function RAND($min = 0, $max = 0): string|int|array
    {
        return MathTrig\Random::randBetween($min, $max);
    }

    /**
     * ROMAN.
     *
     * Converts a number to Roman numeral
     *
     * @deprecated 1.17.0
     *      Use the evaluate() method in the MathTrig\Roman class instead
     * @see MathTrig\Roman::evaluate()
     *
     * @param mixed $aValue Number to convert
     * @param mixed $style Number indicating one of five possible forms
     *
     * @return array|string Roman numeral, or a string containing an error
     */
    public static function ROMAN(mixed $aValue, mixed $style = 0): string|array
    {
        return MathTrig\Roman::evaluate($aValue, $style);
    }

    /**
     * ROUNDUP.
     *
     * Rounds a number up to a specified number of decimal places
     *
     * @deprecated 1.17.0
     *      Use the up() method in the MathTrig\Round class instead
     * @see MathTrig\Round::up()
     *
     * @param array|float $number Number to round
     * @param array|int $digits Number of digits to which you want to round $number
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function ROUNDUP($number, $digits): string|float|array
    {
        return MathTrig\Round::up($number, $digits);
    }

    /**
     * ROUNDDOWN.
     *
     * Rounds a number down to a specified number of decimal places
     *
     * @deprecated 1.17.0
     *      Use the down() method in the MathTrig\Round class instead
     * @see MathTrig\Round::down()
     *
     * @param array|float $number Number to round
     * @param array|int $digits Number of digits to which you want to round $number
     *
     * @return array|float|string Rounded Number, or a string containing an error
     */
    public static function ROUNDDOWN($number, $digits): string|float|array
    {
        return MathTrig\Round::down($number, $digits);
    }

    /**
     * SERIESSUM.
     *
     * Returns the sum of a power series
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\SeriesSum class instead
     * @see MathTrig\SeriesSum::evaluate()
     *
     * @param mixed $x Input value
     * @param mixed $n Initial power
     * @param mixed $m Step
     * @param mixed[] $args An array of coefficients for the Data Series
     *
     * @return array|float|string The result, or a string containing an error
     */
    public static function SERIESSUM(mixed $x, mixed $n, mixed $m, ...$args): string|float|int|array
    {
        return MathTrig\SeriesSum::evaluate($x, $n, $m, ...$args);
    }

    /**
     * SIGN.
     *
     * Determines the sign of a number. Returns 1 if the number is positive, zero (0)
     *        if the number is 0, and -1 if the number is negative.
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Sign class instead
     * @see MathTrig\Sign::evaluate()
     *
     * @param array|float $number Number to round
     *
     * @return array|int|string sign value, or a string containing an error
     */
    public static function SIGN($number): string|int|array
    {
        return MathTrig\Sign::evaluate($number);
    }

    /**
     * returnSign = returns 0/-1/+1.
     *
     * @deprecated 1.18.0
     *      Use the returnSign method in the MathTrig\Helpers class instead
     * @see MathTrig\Helpers::returnSign()
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
     * @deprecated 1.18.0
     *      Use the pi method in the MathTrig\Sqrt class instead
     * @see MathTrig\Sqrt::sqrt()
     *
     * @param array|float $number Number
     *
     * @return array|float|string Square Root of Number * Pi, or a string containing an error
     */
    public static function SQRTPI($number): string|float|array
    {
        return MathTrig\Sqrt::pi($number);
    }

    /**
     * SUBTOTAL.
     *
     * Returns a subtotal in a list or database.
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Subtotal class instead
     * @see MathTrig\Subtotal::evaluate()
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
        return MathTrig\Subtotal::evaluate($functionType, ...$args);
    }

    /**
     * SUM.
     *
     * SUM computes the sum of all the values and cells referenced in the argument list.
     *
     * @deprecated 1.18.0
     *      Use the sumErroringStrings method in the MathTrig\Sum class instead
     * @see MathTrig\Sum::sumErroringStrings()
     *
     * Excel Function:
     *        SUM(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function SUM(mixed ...$args)
    {
        return MathTrig\Sum::sumIgnoringStrings(...$args);
    }

    /**
     * SUMIF.
     *
     * Totals the values of cells that contain numbers within the list of arguments
     *
     * Excel Function:
     *        SUMIF(range, criteria, [sum_range])
     *
     * @deprecated 1.17.0
     *      Use the SUMIF() method in the Statistical\Conditional class instead
     * @see Statistical\Conditional::SUMIF()
     *
     * @param array $range Data values
     * @param string $criteria the criteria that defines which cells will be summed
     *
     * @return null|float|string
     */
    public static function SUMIF(array $range, $criteria, array $sumRange = [])
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
     * @deprecated 1.17.0
     *      Use the SUMIFS() method in the Statistical\Conditional class instead
     * @see Statistical\Conditional::SUMIFS()
     *
     * @param mixed $args Data values
     *
     * @return null|float|string
     */
    public static function SUMIFS(mixed ...$args)
    {
        return Statistical\Conditional::SUMIFS(...$args);
    }

    /**
     * SUMPRODUCT.
     *
     * Excel Function:
     *        SUMPRODUCT(value1[,value2[, ...]])
     *
     * @deprecated 1.18.0
     *      Use the product method in the MathTrig\Sum class instead
     * @see MathTrig\Sum::product()
     *
     * @param mixed ...$args Data values
     *
     * @return float|string The result, or a string containing an error
     */
    public static function SUMPRODUCT(mixed ...$args): string|int|float
    {
        return MathTrig\Sum::product(...$args);
    }

    /**
     * SUMSQ.
     *
     * SUMSQ returns the sum of the squares of the arguments
     *
     * @deprecated 1.18.0
     *      Use the sumSquare method in the MathTrig\SumSquares class instead
     * @see MathTrig\SumSquares::sumSquare()
     *
     * Excel Function:
     *        SUMSQ(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function SUMSQ(mixed ...$args)
    {
        return MathTrig\SumSquares::sumSquare(...$args);
    }

    /**
     * SUMX2MY2.
     *
     * @deprecated 1.18.0
     *     Use the sumXSquaredMinusYSquared method in the MathTrig\SumSquares class instead
     * @see MathTrig\SumSquares::sumXSquaredMinusYSquared()
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float|string
     */
    public static function SUMX2MY2($matrixData1, $matrixData2)
    {
        return MathTrig\SumSquares::sumXSquaredMinusYSquared($matrixData1, $matrixData2);
    }

    /**
     * SUMX2PY2.
     *
     * @deprecated 1.18.0
     *     Use the sumXSquaredPlusYSquared method in the MathTrig\SumSquares class instead
     * @see MathTrig\SumSquares::sumXSquaredPlusYSquared()
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float|string
     */
    public static function SUMX2PY2($matrixData1, $matrixData2)
    {
        return MathTrig\SumSquares::sumXSquaredPlusYSquared($matrixData1, $matrixData2);
    }

    /**
     * SUMXMY2.
     *
     * @deprecated 1.18.0
     *      Use the sumXMinusYSquared method in the MathTrig\SumSquares class instead
     * @see MathTrig\SumSquares::sumXMinusYSquared()
     *
     * @param mixed[] $matrixData1 Matrix #1
     * @param mixed[] $matrixData2 Matrix #2
     *
     * @return float|string
     */
    public static function SUMXMY2($matrixData1, $matrixData2)
    {
        return MathTrig\SumSquares::sumXMinusYSquared($matrixData1, $matrixData2);
    }

    /**
     * TRUNC.
     *
     * Truncates value to the number of fractional digits by number_digits.
     *
     * @deprecated 1.17.0
     *      Use the evaluate() method in the MathTrig\Trunc class instead
     * @see MathTrig\Trunc::evaluate()
     *
     * @param float $value
     * @param int $digits
     *
     * @return array|float|string Truncated value, or a string containing an error
     */
    public static function TRUNC($value = 0, $digits = 0)
    {
        return MathTrig\Trunc::evaluate($value, $digits);
    }

    /**
     * SEC.
     *
     * Returns the secant of an angle.
     *
     * @deprecated 1.18.0
     *      Use the sec method in the MathTrig\Trig\Secant class instead
     * @see MathTrig\Trig\Secant::sec()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The secant of the angle
     */
    public static function SEC($angle)
    {
        return MathTrig\Trig\Secant::sec($angle);
    }

    /**
     * SECH.
     *
     * Returns the hyperbolic secant of an angle.
     *
     * @deprecated 1.18.0
     *      Use the sech method in the MathTrig\Trig\Secant class instead
     * @see MathTrig\Trig\Secant::sech()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The hyperbolic secant of the angle
     */
    public static function SECH($angle)
    {
        return MathTrig\Trig\Secant::sech($angle);
    }

    /**
     * CSC.
     *
     * Returns the cosecant of an angle.
     *
     * @deprecated 1.18.0
     *      Use the csc method in the MathTrig\Trig\Cosecant class instead
     * @see MathTrig\Trig\Cosecant::csc()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The cosecant of the angle
     */
    public static function CSC($angle)
    {
        return MathTrig\Trig\Cosecant::csc($angle);
    }

    /**
     * CSCH.
     *
     * Returns the hyperbolic cosecant of an angle.
     *
     * @deprecated 1.18.0
     *      Use the csch method in the MathTrig\Trig\Cosecant class instead
     * @see MathTrig\Trig\Cosecant::csch()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The hyperbolic cosecant of the angle
     */
    public static function CSCH($angle)
    {
        return MathTrig\Trig\Cosecant::csch($angle);
    }

    /**
     * COT.
     *
     * Returns the cotangent of an angle.
     *
     * @deprecated 1.18.0
     *      Use the cot method in the MathTrig\Trig\Cotangent class instead
     * @see MathTrig\Trig\Cotangent::cot()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The cotangent of the angle
     */
    public static function COT($angle)
    {
        return MathTrig\Trig\Cotangent::cot($angle);
    }

    /**
     * COTH.
     *
     * Returns the hyperbolic cotangent of an angle.
     *
     * @deprecated 1.18.0
     *      Use the coth method in the MathTrig\Trig\Cotangent class instead
     * @see MathTrig\Trig\Cotangent::coth()
     *
     * @param array|float $angle Number
     *
     * @return array|float|string The hyperbolic cotangent of the angle
     */
    public static function COTH($angle)
    {
        return MathTrig\Trig\Cotangent::coth($angle);
    }

    /**
     * ACOT.
     *
     * Returns the arccotangent of a number.
     *
     * @deprecated 1.18.0
     *      Use the acot method in the MathTrig\Trig\Cotangent class instead
     * @see MathTrig\Trig\Cotangent::acot()
     *
     * @param array|float $number Number
     *
     * @return array|float|string The arccotangent of the number
     */
    public static function ACOT($number)
    {
        return MathTrig\Trig\Cotangent::acot($number);
    }

    /**
     * Return NAN or value depending on argument.
     *
     * @deprecated 1.18.0
     *      Use the numberOrNan method in the MathTrig\Helpers class instead
     * @see MathTrig\Helpers::numberOrNan()
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
     * @deprecated 1.18.0
     *      Use the acoth method in the MathTrig\Trig\Cotangent class instead
     * @see MathTrig\Trig\Cotangent::acoth()
     *
     * @param array|float $number Number
     *
     * @return array|float|string The hyperbolic arccotangent of the number
     */
    public static function ACOTH($number)
    {
        return MathTrig\Trig\Cotangent::acoth($number);
    }

    /**
     * ROUND.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @deprecated 1.17.0
     *      Use the round() method in the MathTrig\Round class instead
     * @see MathTrig\Round::round()
     *
     * @param array|mixed $number Should be numeric
     * @param array|mixed $precision Should be int
     *
     * @return array|float|string Rounded number
     */
    public static function builtinROUND($number, $precision): string|float|array
    {
        return MathTrig\Round::round($number, $precision);
    }

    /**
     * ABS.
     *
     * Returns the result of builtin function abs after validating args.
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Absolute class instead
     * @see MathTrig\Absolute::evaluate()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|int|string Rounded number
     */
    public static function builtinABS($number): string|int|float|array
    {
        return MathTrig\Absolute::evaluate($number);
    }

    /**
     * ACOS.
     *
     * @deprecated 1.18.0
     *      Use the acos method in the MathTrig\Trig\Cosine class instead
     * @see MathTrig\Trig\Cosine::acos()
     *
     * Returns the result of builtin function acos after validating args.
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinACOS($number)
    {
        return MathTrig\Trig\Cosine::acos($number);
    }

    /**
     * ACOSH.
     *
     * Returns the result of builtin function acosh after validating args.
     *
     * @deprecated 1.18.0
     *      Use the acosh method in the MathTrig\Trig\Cosine class instead
     * @see MathTrig\Trig\Cosine::acosh()
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinACOSH($number)
    {
        return MathTrig\Trig\Cosine::acosh($number);
    }

    /**
     * ASIN.
     *
     * Returns the result of builtin function asin after validating args.
     *
     * @deprecated 1.18.0
     *      Use the asin method in the MathTrig\Trig\Sine class instead
     * @see MathTrig\Trig\Sine::asin()
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinASIN($number)
    {
        return MathTrig\Trig\Sine::asin($number);
    }

    /**
     * ASINH.
     *
     * Returns the result of builtin function asinh after validating args.
     *
     * @deprecated 1.18.0
     *      Use the asinh method in the MathTrig\Trig\Sine class instead
     * @see MathTrig\Trig\Sine::asinh()
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinASINH($number)
    {
        return MathTrig\Trig\Sine::asinh($number);
    }

    /**
     * ATAN.
     *
     * Returns the result of builtin function atan after validating args.
     *
     * @deprecated 1.18.0
     *      Use the atan method in the MathTrig\Trig\Tangent class instead
     * @see MathTrig\Trig\Tangent::atan()
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinATAN($number)
    {
        return MathTrig\Trig\Tangent::atan($number);
    }

    /**
     * ATANH.
     *
     * Returns the result of builtin function atanh after validating args.
     *
     * @deprecated 1.18.0
     *      Use the atanh method in the MathTrig\Trig\Tangent class instead
     * @see MathTrig\Trig\Tangent::atanh()
     *
     * @param array|float $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinATANH($number)
    {
        return MathTrig\Trig\Tangent::atanh($number);
    }

    /**
     * COS.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @deprecated 1.18.0
     *      Use the cos method in the MathTrig\Trig\Cosine class instead
     * @see MathTrig\Trig\Cosine::cos()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinCOS($number): string|float|array
    {
        return MathTrig\Trig\Cosine::cos($number);
    }

    /**
     * COSH.
     *
     * Returns the result of builtin function cos after validating args.
     *
     * @deprecated 1.18.0
     *      Use the cosh method in the MathTrig\Trig\Cosine class instead
     * @see MathTrig\Trig\Cosine::cosh()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinCOSH($number): string|float|array
    {
        return MathTrig\Trig\Cosine::cosh($number);
    }

    /**
     * DEGREES.
     *
     * Returns the result of builtin function rad2deg after validating args.
     *
     * @deprecated 1.18.0
     *      Use the toDegrees method in the MathTrig\Angle class instead
     * @see MathTrig\Angle::toDegrees()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinDEGREES($number): string|float|array
    {
        return MathTrig\Angle::toDegrees($number);
    }

    /**
     * EXP.
     *
     * Returns the result of builtin function exp after validating args.
     *
     * @deprecated 1.18.0
     *      Use the evaluate method in the MathTrig\Exp class instead
     * @see MathTrig\Exp::evaluate()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinEXP($number): string|float|array
    {
        return MathTrig\Exp::evaluate($number);
    }

    /**
     * LN.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @deprecated 1.18.0
     *      Use the natural method in the MathTrig\Logarithms class instead
     * @see MathTrig\Logarithms::natural()
     *
     * @param mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinLN(mixed $number): string|float|array
    {
        return MathTrig\Logarithms::natural($number);
    }

    /**
     * LOG10.
     *
     * Returns the result of builtin function log after validating args.
     *
     * @deprecated 1.18.0
     *      Use the natural method in the MathTrig\Logarithms class instead
     * @see MathTrig\Logarithms::base10()
     *
     * @param mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinLOG10(mixed $number): string|float|array
    {
        return MathTrig\Logarithms::base10($number);
    }

    /**
     * RADIANS.
     *
     * Returns the result of builtin function deg2rad after validating args.
     *
     * @deprecated 1.18.0
     *      Use the toRadians method in the MathTrig\Angle class instead
     * @see MathTrig\Angle::toRadians()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinRADIANS($number): string|float|array
    {
        return MathTrig\Angle::toRadians($number);
    }

    /**
     * SIN.
     *
     * Returns the result of builtin function sin after validating args.
     *
     * @deprecated 1.18.0
     *      Use the sin method in the MathTrig\Trig\Sine class instead
     * @see MathTrig\Trig\Sine::evaluate()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string sine
     */
    public static function builtinSIN($number): string|float|array
    {
        return MathTrig\Trig\Sine::sin($number);
    }

    /**
     * SINH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @deprecated 1.18.0
     *      Use the sinh method in the MathTrig\Trig\Sine class instead
     * @see MathTrig\Trig\Sine::sinh()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinSINH($number): string|float|array
    {
        return MathTrig\Trig\Sine::sinh($number);
    }

    /**
     * SQRT.
     *
     * Returns the result of builtin function sqrt after validating args.
     *
     * @deprecated 1.18.0
     *      Use the sqrt method in the MathTrig\Sqrt class instead
     * @see MathTrig\Sqrt::sqrt()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinSQRT($number)
    {
        return MathTrig\Sqrt::sqrt($number);
    }

    /**
     * TAN.
     *
     * Returns the result of builtin function tan after validating args.
     *
     * @deprecated 1.18.0
     *      Use the tan method in the MathTrig\Trig\Tangent class instead
     * @see MathTrig\Trig\Tangent::tan()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinTAN($number)
    {
        return MathTrig\Trig\Tangent::tan($number);
    }

    /**
     * TANH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @deprecated 1.18.0
     *      Use the tanh method in the MathTrig\Trig\Tangent class instead
     * @see MathTrig\Trig\Tangent::tanh()
     *
     * @param array|mixed $number Should be numeric
     *
     * @return array|float|string Rounded number
     */
    public static function builtinTANH($number): string|float|array
    {
        return MathTrig\Trig\Tangent::tanh($number);
    }

    /**
     * Many functions accept null/false/true argument treated as 0/0/1.
     *
     * @deprecated 1.18.0
     *      Use the validateNumericNullBool method in the MathTrig\Helpers class instead
     * @see MathTrig\Helpers::validateNumericNullBool()
     */
    public static function nullFalseTrueToNumber(mixed &$number): void
    {
        $number = Functions::flattenSingleValue($number);
        if ($number === null) {
            $number = 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }
    }
}
