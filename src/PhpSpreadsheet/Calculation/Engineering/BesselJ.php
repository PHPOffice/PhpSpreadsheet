<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class BesselJ
{
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
            $ord = (int) floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            $fResult = self::calculate($x, $ord);

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    private static function calculate(float $x, int $ord): float
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
}
