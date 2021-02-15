<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Bin2Hex
{
    /**
     * BINTOHEX.
     *
     * Return a binary value as hex.
     *
     * Excel Function:
     *        BIN2HEX(x[,places])
     *
     * @param string $number The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2HEX returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2HEX uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2HEX returns the #VALUE! error value.
     *                                If places is negative, BIN2HEX returns the #NUM! error value.
     * @param mixed $number
     * @param mixed $places
     */
    public static function funcBin2Hex($number, $places = null): string
    {
        $number = Functions::flattenSingleValue($number);
        $places = Functions::flattenSingleValue($places);

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
            return str_repeat('F', 8) . substr(strtoupper(dechex((int) bindec(substr($number, -9)))), -2);
        }
        $hexVal = (string) strtoupper(dechex((int) bindec($number)));

        return Helpers::nbrConversionFormat($hexVal, $places);
    }
}
