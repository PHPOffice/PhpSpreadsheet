<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class BesselK
{
    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELK($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBk = self::besselK0($x);

                    break;
                case 1:
                    $fBk = self::besselK1($x);

                    break;
                default:
                    $fBk = self::besselK2($x, $ord);
            }

            return (is_nan($fBk)) ? Functions::NAN() : $fBk;
        }

        return Functions::VALUE();
    }

    private static function besselK0(float $fNum): float
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);

            return -log($fNum2) * BesselI::BESSELI($fNum, 0) +
                (-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y *
                                    (0.10750e-3 + $y * 0.74e-5))))));
        }

        $y = 2 / $fNum;

        return exp(-$fNum) / sqrt($fNum) *
            (1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y *
                            (0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
    }

    private static function besselK1(float $fNum): float
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);

            return log($fNum2) * BesselI::BESSELI($fNum, 1) +
                (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
                                    (-0.110404e-2 + $y * (-0.4686e-4))))))) / $fNum;
        }

        $y = 2 / $fNum;

        return exp(-$fNum) / sqrt($fNum) *
            (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
                                (0.325614e-2 + $y * (-0.68245e-3)))))));
    }

    private static function besselK2(float $x, int $ord)
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
