<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Hex2Bin
{
    /**
     * HEXTOBIN.
     *
     * Return a hex value as binary.
     *
     * Excel Function:
     *        HEX2BIN(x[,places])
     *
     * @param string $number the hexadecimal number you want to convert.
     *                  Number cannot contain more than 10 characters.
     *                  The most significant bit of number is the sign bit (40th bit from the right).
     *                  The remaining 9 bits are magnitude bits.
     *                  Negative numbers are represented using two's-complement notation.
     *                  If number is negative, HEX2BIN ignores places and returns a 10-character binary number.
     *                  If number is negative, it cannot be less than FFFFFFFE00,
     *                      and if number is positive, it cannot be greater than 1FF.
     *                  If number is not a valid hexadecimal number, HEX2BIN returns the #NUM! error value.
     *                  If HEX2BIN requires more than places characters, it returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    HEX2BIN uses the minimum number of characters necessary. Places
     *                                    is useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2BIN returns the #VALUE! error value.
     *                                    If places is negative, HEX2BIN returns the #NUM! error value.
     * @param mixed $number
     * @param mixed $places
     */
    public static function funcHex2Bin($number, $places = null): string
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

        return Dec2Bin::funcDec2Bin(Hex2Dec::funcHex2Dec($number), $places);
    }
}
