<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Oct2Hex
{
    /**
     * OCTTOHEX.
     *
     * Return an octal value as hex.
     *
     * Excel Function:
     *        OCT2HEX(x[,places])
     *
     * @param string $number The octal number you want to convert. Number may not contain
     *                                    more than 10 octal characters (30 bits). The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits are
     *                                    magnitude bits. Negative numbers are represented using
     *                                    two's-complement notation.
     *                                    If number is negative, OCT2HEX ignores places and returns a
     *                                    10-character hexadecimal number.
     *                                    If number is not a valid octal number, OCT2HEX returns the
     *                                    #NUM! error value.
     *                                    If OCT2HEX requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, OCT2HEX
     *                                    uses the minimum number of characters necessary. Places is useful
     *                                    for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2HEX returns the #VALUE! error value.
     *                                    If places is negative, OCT2HEX returns the #NUM! error value.
     * @param mixed $number
     * @param mixed $places
     */
    public static function funcOct2Hex($number, $places = null): string
    {
        $number = Functions::flattenSingleValue($number);
        $places = Functions::flattenSingleValue($places);

        Helpers::nullOrOdsBoolToNumber($number);
        if (is_bool($number)) {
            return Functions::VALUE();
        }
        Helpers::gnumericFloatToInt($number);

        $number = (string) $number;
        if (preg_match_all('/[01234567]/', $number, $out) != strlen($number)) {
            return Functions::NAN();
        }
        $hexVal = strtoupper(dechex((int) Oct2Dec::funcOct2Dec((int) $number)));

        return Helpers::nbrConversionFormat($hexVal, $places);
    }
}
