<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Random
{
    /**
     * RAND.
     *
     * @return float Random number
     */
    public static function rand()
    {
        return (mt_rand(0, 10000000)) / 10000000;
    }

    /**
     * RANDBETWEEN.
     *
     * @param mixed $min Minimal value
     * @param mixed $max Maximal value
     *
     * @return float|int|string Random number
     */
    public static function randBetween($min, $max)
    {
        try {
            $min = (int) Helpers::validateNumericNullBool($min);
            $max = (int) Helpers::validateNumericNullBool($max);
            Helpers::validateNotNegative($max - $min);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return mt_rand($min, $max);
    }
}
