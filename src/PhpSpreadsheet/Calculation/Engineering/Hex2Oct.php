<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Hex2Oct
{
    /**
     * HEXTOOCT.
     *
     * Return a hex value as octal.
     *
     * Excel Function:
     *        HEX2OCT(x[,places])
     *
     * @param string $number The hexadecimal number you want to convert. Number cannot
     *                                    contain more than 10 characters. The most significant bit of
     *                                    number is the sign bit. The remaining 39 bits are magnitude
     *                                    bits. Negative numbers are represented using two's-complement
     *                                    notation.
     *                                    If number is negative, HEX2OCT ignores places and returns a
     *                                    10-character octal number.
     *                                    If number is negative, it cannot be less than FFE0000000, and
     *                                    if number is positive, it cannot be greater than 1FFFFFFF.
     *                                    If number is not a valid hexadecimal number, HEX2OCT returns
     *                                    the #NUM! error value.
     *                                    If HEX2OCT requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, HEX2OCT
     *                                    uses the minimum number of characters necessary. Places is
     *                                    useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2OCT returns the #VALUE! error
     *                                    value.
     *                                    If places is negative, HEX2OCT returns the #NUM! error value.
     * @param mixed $number
     * @param mixed $places
     */
    public static function funcHex2Oct($number, $places = null): string
    {
        $number = Functions::flattenSingleValue($number);
        $places = Functions::flattenSingleValue($places);

        Helpers::nullOrOdsBoolToNumber($number);
        if (is_bool($number)) {
            return Functions::VALUE();
        }
        Helpers::gnumericFloatToInt($number);

        $number = (string) $number;
        if (strlen($number) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($number), $out)) {
            return Functions::NAN();
        }

        $decimal = Hex2Dec::funcHex2Dec($number);
        if ($decimal < -536870912 || $decimal > 536870911) {
            return Functions::NAN();
        }

        return Dec2Oct::funcDec2Oct($decimal, $places);
    }
}
