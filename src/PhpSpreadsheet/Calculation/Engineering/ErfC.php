<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ErfC
{
    /**
     * ERFC.
     *
     *    Returns the complementary ERF function integrated between x and infinity
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the lower bound argument,
     *        the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *        improved, so that it can now calculate the function for both positive and negative x values.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts nagative arguments.
     *
     *    Excel Function:
     *        ERFC(x)
     *
     * @param float $value The lower bound for integrating ERFC
     *
     * @return float|string
     */
    public static function ERFC($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_numeric($value)) {
            return self::erfcValue($value);
        }

        return Functions::VALUE();
    }

    //
    //    Private method to calculate the erfc value
    //
    private static $oneSqrtPi = 0.564189583547756287;

    private static function erfcValue($value)
    {
        if (abs($value) < 2.2) {
            return 1 - Erf::erfValue($value);
        }
        if ($value < 0) {
            return 2 - self::ERFC(-$value);
        }
        $a = $n = 1;
        $b = $c = $value;
        $d = ($value * $value) + 0.5;
        $q1 = $q2 = $b / $d;
        do {
            $t = $a * $n + $b * $value;
            $a = $b;
            $b = $t;
            $t = $c * $n + $d * $value;
            $c = $d;
            $d = $t;
            $n += 0.5;
            $q1 = $q2;
            $q2 = $b / $d;
        } while ((abs($q1 - $q2) / $q2) > Functions::PRECISION);

        return self::$oneSqrtPi * exp(-$value * $value) * $q2;
    }
}
