<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class Tangent
{
    /**
     * TAN.
     *
     * Returns the result of builtin function tan after validating args.
     *
     * @param mixed $angle Should be numeric
     *
     * @return float|string tangent
     */
    public static function tan($angle)
    {
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
     * @param mixed $angle Should be numeric
     *
     * @return float|string hyperbolic tangent
     */
    public static function tanh($angle)
    {
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
     * @param float $number Number
     *
     * @return float|string The arctangent of the number
     */
    public static function atan($number)
    {
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
     * @param float $number Number
     *
     * @return float|string The inverse hyperbolic tangent of the number
     */
    public static function atanh($number)
    {
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
     * @param mixed $xCoordinate should be float, the x-coordinate of the point
     * @param mixed $yCoordinate should be float, the y-coordinate of the point
     *
     * @return float|string the inverse tangent of the specified x- and y-coordinates, or a string containing an error
     */
    public static function atan2($xCoordinate, $yCoordinate)
    {
        try {
            $xCoordinate = Helpers::validateNumericNullBool($xCoordinate);
            $yCoordinate = Helpers::validateNumericNullBool($yCoordinate);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($xCoordinate == 0) && ($yCoordinate == 0)) {
            return Functions::DIV0();
        }

        return atan2($yCoordinate, $xCoordinate);
    }
}
