<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;

class Combinations
{
    use ArrayEnabled;

    /**
     * COMBIN.
     *
     * Returns the number of combinations for a given number of items. Use COMBIN to
     *        determine the total possible number of groups for a given number of items.
     *
     * Excel Function:
     *        COMBIN(numObjs,numInSet)
     *
     * @param mixed $numObjs Number of different objects, or can be an array of numbers
     * @param mixed $numInSet Number of objects in each combination, or can be an array of numbers
     *
     * @return array|float|int|string Number of combinations, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function withoutRepetition($numObjs, $numInSet)
    {
        if (is_array($numObjs) || is_array($numInSet)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $numObjs, $numInSet);
        }

        try {
            $numObjs = Helpers::validateNumericNullSubstitution($numObjs, null);
            $numInSet = Helpers::validateNumericNullSubstitution($numInSet, null);
            Helpers::validateNotNegative($numInSet);
            Helpers::validateNotNegative($numObjs - $numInSet);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return round(Factorial::fact($numObjs) / Factorial::fact($numObjs - $numInSet)) / Factorial::fact($numInSet);
    }

    /**
     * COMBINA.
     *
     * Returns the number of combinations for a given number of items. Use COMBIN to
     *        determine the total possible number of groups for a given number of items.
     *
     * Excel Function:
     *        COMBINA(numObjs,numInSet)
     *
     * @param mixed $numObjs Number of different objects, or can be an array of numbers
     * @param mixed $numInSet Number of objects in each combination, or can be an array of numbers
     *
     * @return array|float|int|string Number of combinations, or a string containing an error
     *         If an array of numbers is passed as the argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function withRepetition($numObjs, $numInSet)
    {
        if (is_array($numObjs) || is_array($numInSet)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $numObjs, $numInSet);
        }

        try {
            $numObjs = Helpers::validateNumericNullSubstitution($numObjs, null);
            $numInSet = Helpers::validateNumericNullSubstitution($numInSet, null);
            Helpers::validateNotNegative($numInSet);
            Helpers::validateNotNegative($numObjs);
            $numObjs = (int) $numObjs;
            $numInSet = (int) $numInSet;
            // Microsoft documentation says following is true, but Excel
            //  does not enforce this restriction.
            //Helpers::validateNotNegative($numObjs - $numInSet);
            if ($numObjs === 0) {
                Helpers::validateNotNegative(-$numInSet);

                return 1;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return round(
            Factorial::fact($numObjs + $numInSet - 1) / Factorial::fact($numObjs - 1)
        ) / Factorial::fact($numInSet);
    }
}
