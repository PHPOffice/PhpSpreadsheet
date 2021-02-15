<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Bin2Dec
{
    /**
     * BINTODEC.
     *
     * Return a binary value as decimal.
     *
     * Excel Function:
     *        BIN2DEC(x)
     *
     * @param string $number The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2DEC returns the #NUM! error value.
     * @param mixed $number
     *
     * @return float|int|string
     */
    public static function funcBin2Dec($number)
    {
        $number = Functions::flattenSingleValue($number);
        Helpers::nullOrOdsBoolToNumber($number);
        if (is_bool($number)) {
            return Functions::VALUE();
        }
        Helpers::gnumericFloatToInt($number);
        $number = (string) $number;
        if (strlen($number) > preg_match_all('/[01]/', $number, $out)) {
            return Functions::NAN();
        }
        if (strlen($number) > 10) {
            return Functions::NAN();
        }
        if (strlen($number) == 10) {
            //    Two's Complement
            $number = substr($number, -9);

            return '-' . (512 - bindec($number));
        }

        return bindec($number);
    }
}
