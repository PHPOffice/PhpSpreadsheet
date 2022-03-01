<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Tangent
{
    use ArrayEnabled;

    /**
     * TAN.
     *
     * Returns the result of builtin function tan after validating args.
     *
     * @param mixed $angle Should be numeric, or can be an array of numbers
     *
     * @return array|float|string tangent
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function tan($angle)
    {
        if (is_array($angle)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $angle);
        }

        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::verySmallDenominator(sin($angle), cos($angle));
    }

    /**
     * TANH.
     *
     * Returns the result of builtin function sinh after validating args.
     *
     * @param mixed $angle Should be numeric, or can be an array of numbers
     *
     * @return array|float|string hyperbolic tangent
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function tanh($angle)
    {
        if (is_array($angle)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $angle);
        }

        try {
            $angle = Helpers::validateNumericNullBool($angle);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return tanh($angle);
    }

    /**
     * ATAN.
     *
     * Returns the arctangent of a number.
     *
     * @param array|float $number Number, or can be an array of numbers
     *
     * @return array|float|string The arctangent of the number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function atan($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(atan($number));
    }

    /**
     * ATANH.
     *
     * Returns the inverse hyperbolic tangent of a number.
     *
     * @param array|float $number Number, or can be an array of numbers
     *
     * @return array|float|string The inverse hyperbolic tangent of the number
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function atanh($number)
    {
        if (is_array($number)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $number);
        }

        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::numberOrNan(atanh($number));
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
     * @param mixed $xCoordinate should be float, the x-coordinate of the point, or can be an array of numbers
     * @param mixed $yCoordinate should be float, the y-coordinate of the point, or can be an array of numbers
     *
     * @return array|float|string
     *         The inverse tangent of the specified x- and y-coordinates, or a string containing an error
     *         If an array of numbers is passed as one of the arguments, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function atan2($xCoordinate, $yCoordinate)
    {
        if (is_array($xCoordinate) || is_array($yCoordinate)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $xCoordinate, $yCoordinate);
        }

        try {
            $xCoordinate = Helpers::validateNumericNullBool($xCoordinate);
            $yCoordinate = Helpers::validateNumericNullBool($yCoordinate);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($xCoordinate == 0) && ($yCoordinate == 0)) {
            return ExcelError::DIV0();
        }

        return atan2($yCoordinate, $xCoordinate);
    }
}
