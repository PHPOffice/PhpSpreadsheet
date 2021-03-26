<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Product
{
    /**
     * PRODUCT.
     *
     * PRODUCT returns the product of all the values and cells referenced in the argument list.
     *
     * Excel Function:
     *        PRODUCT(value1[,value2[, ...]])
     *
     * @param mixed ...$args Data values
     *
     * @return float|string
     */
    public static function funcProduct(...$args)
    {
        // Return value
        $returnValue = null;

        // Loop through arguments
        foreach (Functions::flattenArray($args) as $arg) {
            // Is it a numeric value?
            if (is_numeric($arg)) {
                if ($returnValue === null) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
            } else {
                return Functions::VALUE();
            }
        }

        // Return
        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }
}
