<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Hex2Dec
{
    /**
     * HEXTODEC.
     *
     * Return a hex value as decimal.
     *
     * Excel Function:
     *        HEX2DEC(x)
     *
     * @param string $number The hexadecimal number you want to convert. This number cannot
     *                                contain more than 10 characters (40 bits). The most significant
     *                                bit of number is the sign bit. The remaining 39 bits are magnitude
     *                                bits. Negative numbers are represented using two's-complement
     *                                notation.
     *                                If number is not a valid hexadecimal number, HEX2DEC returns the
     *                                #NUM! error value.
     * @param mixed $number
     *
     * @return float|int|string
     */
    public static function funcHex2Dec($number)
    {
        $number = Functions::flattenSingleValue($number);

        Helpers::nullOrOdsBoolToNumber($number);
        if (is_bool($number)) {
            return Functions::VALUE();
        }
        Helpers::gnumericFloatToInt($number);

        $number = (string) $number;
        if (strlen($number) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($number), $out)) {
            return Functions::NAN();
        }

        if (strlen($number) > 10) {
            return Functions::NAN();
        }

        $binX = '';
        foreach (str_split($number) as $char) {
            $binX .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 40 && $binX[0] == '1') {
            for ($i = 0; $i < 40; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }
}
