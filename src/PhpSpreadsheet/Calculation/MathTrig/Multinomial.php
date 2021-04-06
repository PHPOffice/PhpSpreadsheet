<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Multinomial
{
    /**
     * MULTINOMIAL.
     *
     * Returns the ratio of the factorial of a sum of values to the product of factorials.
     *
     * @param mixed[] $args An array of mixed values for the Data Series
     *
     * @return float|string The result, or a string containing an error
     */
    public static function funcMultinomial(...$args)
    {
        $summer = 0;
        $divisor = 1;

        try {
            // Loop through arguments
            foreach (Functions::flattenArray($args) as $argx) {
                $arg = Helpers::validateNumericNullSubstitution($argx, null);
                Helpers::validateNotNegative($arg);
                $arg = (int) $arg;
                $summer += $arg;
                $divisor *= Fact::funcFact($arg);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $summer = Fact::funcFact($summer);

        return $summer / $divisor;
    }
}
