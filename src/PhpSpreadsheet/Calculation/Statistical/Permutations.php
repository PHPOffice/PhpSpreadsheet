<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Permutations
{
    /**
     * PERMUT.
     *
     * Returns the number of permutations for a given number of objects that can be
     *        selected from number objects. A permutation is any set or subset of objects or
     *        events where internal order is significant. Permutations are different from
     *        combinations, for which the internal order is not significant. Use this function
     *        for lottery-style probability calculations.
     *
     * @param int $numObjs Number of different objects
     * @param int $numInSet Number of objects in each permutation
     *
     * @return int|string Number of permutations, or a string containing an error
     */
    public static function PERMUT($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            $numInSet = floor($numInSet);
            if ($numObjs < $numInSet) {
                return Functions::NAN();
            }

            return round(MathTrig::FACT($numObjs) / MathTrig::FACT($numObjs - $numInSet));
        }

        return Functions::VALUE();
    }

    /**
     * PERMUTATIONA.
     *
     * Returns the number of permutations for a given number of objects (with repetitions)
     *     that can be selected from the total objects.
     *
     * @param int $numObjs Number of different objects
     * @param int $numInSet Number of objects in each permutation
     *
     * @return int|string Number of permutations, or a string containing an error
     */
    public static function PERMUTATIONA($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            $numObjs = floor($numObjs);
            $numInSet = floor($numInSet);
            if ($numObjs < 0 || $numInSet < 0) {
                return Functions::NAN();
            }

            return $numObjs ** $numInSet;
        }

        return Functions::VALUE();
    }
}
