<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class BesselI
{
    /**
     * BESSELI.
     *
     *    Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
     *        for purely imaginary arguments
     *
     *    Excel Function:
     *        BESSELI(x,ord)
     *
     * NOTE: The MS Excel implementation of the BESSELI function is still not accurate.
     *       This code provides a more accurate calculation
     *
     * @param mixed $x A float value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELI returns the #VALUE! error value.
     * @param mixed $ord The integer order of the Bessel function.
     *                                If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
     *                                If $ord < 0, BESSELI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELI($x, $ord)
    {
        $x = Functions::flattenSingleValue($x);
        $ord = Functions::flattenSingleValue($ord);

        try {
            $x = EngineeringValidations::validateFloat($x);
            $ord = EngineeringValidations::validateInt($ord);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($ord < 0) {
            return Functions::NAN();
        }

        $fResult = self::calculate($x, $ord);

        return (is_nan($fResult)) ? Functions::NAN() : $fResult;
    }

    private static function calculate(float $x, int $ord): float
    {
        // special cases
        switch ($ord) {
            case 0:
                return self::besselI0($x);
            case 1:
                return self::besselI1($x);
        }

        return self::besselI2($x, $ord);
    }

    private static function besselI0(float $x): float
    {
        $ax = abs($x);

        if ($ax < 3.75) {
            $y = $x / 3.75;
            $y = $y * $y;

            return 1.0 + $y * (3.5156229 + $y * (3.0899424 + $y * (1.2067492
                                + $y * (0.2659732 + $y * (0.360768e-1 + $y * 0.45813e-2)))));
        }

        $y = 3.75 / $ax;

        return (exp($ax) / sqrt($ax)) * (0.39894228 + $y * (0.1328592e-1 + $y * (0.225319e-2 + $y * (-0.157565e-2
                            + $y * (0.916281e-2 + $y * (-0.2057706e-1 + $y * (0.2635537e-1 +
                                        $y * (-0.1647633e-1 + $y * 0.392377e-2))))))));
    }

    private static function besselI1(float $x): float
    {
        $ax = abs($x);

        if ($ax < 3.75) {
            $y = $x / 3.75;
            $y = $y * $y;
            $ans = $ax * (0.5 + $y * (0.87890594 + $y * (0.51498869 + $y * (0.15084934 + $y * (0.2658733e-1 +
                                    $y * (0.301532e-2 + $y * 0.32411e-3))))));

            return ($x < 0.0) ? -$ans : $ans;
        }

        $y = 3.75 / $ax;
        $ans = 0.2282967e-1 + $y * (-0.2895312e-1 + $y * (0.1787654e-1 - $y * 0.420059e-2));
        $ans = 0.39894228 + $y * (-0.3988024e-1 + $y * (-0.362018e-2 + $y * (0.163801e-2 +
                        $y * (-0.1031555e-1 + $y * $ans))));
        $ans *= exp($ax) / sqrt($ax);

        return ($x < 0.0) ? -$ans : $ans;
    }

    private static function besselI2(float $x, int $ord): float
    {
        if ($x === 0.0) {
            return 0.0;
        }

        $tox = 2.0 / abs($x);
        $bip = 0;
        $ans = 0.0;
        $bi = 1.0;

        for ($j = 2 * ($ord + (int) sqrt(40.0 * $ord)); $j > 0; --$j) {
            $bim = $bip + $j * $tox * $bi;
            $bip = $bi;
            $bi = $bim;

            if (abs($bi) > 1.0e+12) {
                $ans *= 1.0e-12;
                $bi *= 1.0e-12;
                $bip *= 1.0e-12;
            }

            if ($j === $ord) {
                $ans = $bip;
            }
        }

        $ans *= self::besselI0($x) / $bi;

        return ($x < 0.0 && (($ord % 2) === 1)) ? -$ans : $ans;
    }
}
