<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class BesselY
{
    /**
     * BESSELY.
     *
     * Returns the Bessel function, which is also called the Weber function or the Neumann function.
     *
     *    Excel Function:
     *        BESSELY(x,ord)
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELY returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELY returns the #VALUE! error value.
     *                                If $ord < 0, BESSELY returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELY($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0 : Functions::flattenSingleValue($ord);

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
                    $fBy = self::besselY2($x, $ord);
            }

            return (is_nan($fBy)) ? Functions::NAN() : $fBy;
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

            return $f1 / $f2 + 0.636619772 * BesselJ::BESSELJ($fNum, 0) * log($fNum);
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

            return $f1 / $f2 + 0.636619772 * (BesselJ::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
        }

        return sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
    }

    private static function besselY2(float $x, int $ord)
    {
        $fTox = 2 / $x;
        $fBym = self::besselY0($x);
        $fBy = self::besselY1($x);
        for ($n = 1; $n < $ord; ++$n) {
            $fByp = $n * $fTox * $fBy - $fBym;
            $fBym = $fBy;
            $fBy = $fByp;
        }

        return $fBy;
    }
}
