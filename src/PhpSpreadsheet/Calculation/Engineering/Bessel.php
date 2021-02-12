<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Bessel
{
    private static function calculateBesselI(float $x, int $ord): float
    {
        if (abs($x) <= 30) {
            $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
            $ordK = 1;
            $fSqrX = ($x * $x) / 4;
            do {
                $fTerm *= $fSqrX;
                $fTerm /= ($ordK * ($ordK + $ord));
                $fResult += $fTerm;
            } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));

            return $fResult;
        }

        $f_2_PI = 2 * M_PI;

        $fXAbs = abs($x);
        $fResult = exp($fXAbs) / sqrt($f_2_PI * $fXAbs);
        if (($ord & 1) && ($x < 0)) {
            $fResult = -$fResult;
        }

        return $fResult;
    }

    /**
     * BESSELI.
     *
     *    Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
     *        for purely imaginary arguments
     *
     *    Excel Function:
     *        BESSELI(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELI returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function.
     *                                If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
     *                                If $ord < 0, BESSELI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELI($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            $fResult = self::calculateBesselI($x, $ord);

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    private static function calculateBesselJ(float $x, int $ord): float
    {
        if (abs($x) <= 30) {
            $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
            $ordK = 1;
            $fSqrX = ($x * $x) / -4;
            do {
                $fTerm *= $fSqrX;
                $fTerm /= ($ordK * ($ordK + $ord));
                $fResult += $fTerm;
            } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));

            return $fResult;
        }

        $f_PI_DIV_2 = M_PI / 2;
        $f_PI_DIV_4 = M_PI / 4;

        $fXAbs = abs($x);
        $fResult = sqrt(Functions::M_2DIVPI / $fXAbs) * cos($fXAbs - $ord * $f_PI_DIV_2 - $f_PI_DIV_4);
        if (($ord & 1) && ($x < 0)) {
            $fResult = -$fResult;
        }

        return $fResult;
    }

    /**
     * BESSELJ.
     *
     *    Returns the Bessel function
     *
     *    Excel Function:
     *        BESSELJ(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELJ returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELJ returns the #VALUE! error value.
     *                                If $ord < 0, BESSELJ returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELJ($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            $fResult = self::calculateBesselJ($x, $ord);

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    private static function besselK0(float $fNum): float
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);

            return -log($fNum2) * self::BESSELI($fNum, 0) +
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

            return log($fNum2) * self::BESSELI($fNum, 1) +
                (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
                          (-0.110404e-2 + $y * (-0.4686e-4))))))) / $fNum;
        }

        $y = 2 / $fNum;

        return exp(-$fNum) / sqrt($fNum) *
            (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
                               (0.325614e-2 + $y * (-0.68245e-3)))))));
    }

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
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

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
                    $fTox = 2 / $x;
                    $fBkm = self::besselK0($x);
                    $fBk = self::besselK1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fBkp = $fBkm + $n * $fTox * $fBk;
                        $fBkm = $fBk;
                        $fBk = $fBkp;
                    }
            }

            return (is_nan($fBk)) ? Functions::NAN() : $fBk;
        }

        return Functions::VALUE();
    }

    private static function besselY0(float $fNum): float
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = -2957821389.0 + $y * (7062834065.0 + $y * (-512359803.6 + $y * (10879881.29 + $y *
                                       (-86327.92757 + $y * 228.4622733))));
            $f2 = 40076544269.0 + $y * (745249964.8 + $y * (7189466.438 + $y *
                                       (47447.26470 + $y * (226.1030244 + $y))));

            return $f1 / $f2 + 0.636619772 * self::BESSELJ($fNum, 0) * log($fNum);
        }

        $z = 8.0 / $fNum;
        $y = ($z * $z);
        $xx = $fNum - 0.785398164;
        $f1 = 1 + $y * (-0.1098628627e-2 + $y * (0.2734510407e-4 + $y * (-0.2073370639e-5 + $y * 0.2093887211e-6)));
        $f2 = -0.1562499995e-1 + $y * (0.1430488765e-3 + $y * (-0.6911147651e-5 + $y * (0.7621095161e-6 + $y *
                                      (-0.934945152e-7))));

        return sqrt(0.636619772 / $fNum) * (sin($xx) * $f1 + $z * cos($xx) * $f2);
    }

    private static function besselY1(float $fNum): float
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = $fNum * (-0.4900604943e13 + $y * (0.1275274390e13 + $y * (-0.5153438139e11 + $y *
                            (0.7349264551e9 + $y * (-0.4237922726e7 + $y * 0.8511937935e4)))));
            $f2 = 0.2499580570e14 + $y * (0.4244419664e12 + $y * (0.3733650367e10 + $y * (0.2245904002e8 + $y *
                            (0.1020426050e6 + $y * (0.3549632885e3 + $y)))));

            return $f1 / $f2 + 0.636619772 * (self::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
        }

        return sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
    }

    /**
     * BESSELY.
     *
     * Returns the Bessel function, which is also called the Weber function or the Neumann function.
     *
     *    Excel Function:
     *        BESSELY(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELY($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBy = self::besselY0($x);

                    break;
                case 1:
                    $fBy = self::besselY1($x);

                    break;
                default:
                    $fTox = 2 / $x;
                    $fBym = self::besselY0($x);
                    $fBy = self::besselY1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fByp = $n * $fTox * $fBy - $fBym;
                        $fBym = $fBy;
                        $fBy = $fByp;
                    }
            }

            return (is_nan($fBy)) ? Functions::NAN() : $fBy;
        }

        return Functions::VALUE();
    }
}
