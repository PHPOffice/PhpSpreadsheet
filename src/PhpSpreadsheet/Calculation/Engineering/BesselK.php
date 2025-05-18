<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class BesselK
{
    use ArrayEnabled;

    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @param mixed $x A float value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     *                      Or can be an array of values
     * @param mixed $ord The integer order of the Bessel function.
     *                       If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                       If $ord < 0, BESSELKI returns the #NUM! error value.
     *                      Or can be an array of values
     *
     * @return array<mixed>|float|string Result, or a string containing an error
     *         If an array of numbers is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function BESSELK(mixed $x, mixed $ord): array|string|float
    {
        if (is_array($x) || is_array($ord)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $x, $ord);
        }

        try {
            $x = EngineeringValidations::validateFloat($x);
            $ord = EngineeringValidations::validateInt($ord);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (($ord < 0) || ($x <= 0.0)) {
            return ExcelError::NAN();
        }

        $fBk = self::calculate($x, $ord);

        return (is_nan($fBk)) ? ExcelError::NAN() : $fBk;
    }

    private static function calculate(float $x, int $ord): float
    {
        return match ($ord) {
            0 => self::besselK0($x),
            1 => self::besselK1($x),
            default => self::besselK2($x, $ord),
        };
    }

    /**
     * Mollify Phpstan.
     *
     * @codeCoverageIgnore
     */
    private static function callBesselI(float $x, int $ord): float
    {
        $rslt = BesselI::BESSELI($x, $ord);
        if (!is_float($rslt)) {
            throw new Exception('Unexpected array or string');
        }

        return $rslt;
    }

    private static function besselK0(float $x): float
    {
        if ($x <= 2) {
            $fNum2 = $x * 0.5;
            $y = ($fNum2 * $fNum2);

            return -log($fNum2) * self::callBesselI($x, 0)
                + (-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y
                                    * (0.10750e-3 + $y * 0.74e-5))))));
        }

        $y = 2 / $x;

        return exp(-$x) / sqrt($x)
            * (1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y
                            * (0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
    }

    private static function besselK1(float $x): float
    {
        if ($x <= 2) {
            $fNum2 = $x * 0.5;
            $y = ($fNum2 * $fNum2);

            return log($fNum2) * self::callBesselI($x, 1)
                + (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y
                                    * (-0.110404e-2 + $y * (-0.4686e-4))))))) / $x;
        }

        $y = 2 / $x;

        return exp(-$x) / sqrt($x)
            * (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y
                                * (0.325614e-2 + $y * (-0.68245e-3)))))));
    }

    private static function besselK2(float $x, int $ord): float
    {
        $fTox = 2 / $x;
        $fBkm = self::besselK0($x);
        $fBk = self::besselK1($x);
        for ($n = 1; $n < $ord; ++$n) {
            $fBkp = $fBkm + $n * $fTox * $fBk;
            $fBkm = $fBk;
            $fBk = $fBkp;
        }

        return $fBk;
    }
}
