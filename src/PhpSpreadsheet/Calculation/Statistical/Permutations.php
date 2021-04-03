<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

class Permutations
{
    use BaseValidations;

    /**
     * PERMUT.
     *
     * Returns the number of permutations for a given number of objects that can be
     *        selected from number objects. A permutation is any set or subset of objects or
     *        events where internal order is significant. Permutations are different from
     *        combinations, for which the internal order is not significant. Use this function
     *        for lottery-style probability calculations.
     *
     * @param mixed $numObjs Integer number of different objects
     * @param mixed $numInSet Integer number of objects in each permutation
     *
     * @return int|string Number of permutations, or a string containing an error
     */
    public static function PERMUT($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        try {
            $numObjs = self::validateInt($numObjs);
            $numInSet = self::validateInt($numInSet);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($numObjs < $numInSet) {
            return Functions::NAN();
        }

        return (int) round(MathTrig\Fact::funcFact($numObjs) / MathTrig\Fact::funcFact($numObjs - $numInSet));
    }

    /**
     * PERMUTATIONA.
     *
     * Returns the number of permutations for a given number of objects (with repetitions)
     *     that can be selected from the total objects.
     *
     * @param mixed $numObjs Integer number of different objects
     * @param mixed $numInSet Integer number of objects in each permutation
     *
     * @return int|string Number of permutations, or a string containing an error
     */
    public static function PERMUTATIONA($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        try {
            $numObjs = self::validateInt($numObjs);
            $numInSet = self::validateInt($numInSet);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if ($numObjs < 0 || $numInSet < 0) {
            return Functions::NAN();
        }

        return (int) ($numObjs ** $numInSet);
    }
}
