<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use Exception;

class Even
{
    /**
     * EVEN.
     *
     * Returns number rounded up to the nearest even integer.
     * You can use this function for processing items that come in twos. For example,
     *        a packing crate accepts rows of one or two items. The crate is full when
     *        the number of items, rounded up to the nearest two, matches the crate's
     *        capacity.
     *
     * Excel Function:
     *        EVEN(number)
     *
     * @param float $number Number to round
     *
     * @return float|string Rounded Number, or a string containing an error
     */
    public static function funcEven($number)
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Helpers::getEven($number);
    }
}
