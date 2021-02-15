<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Dec2Oct
{
    /**
     * DECTOOCT.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @param string $number The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2OCT returns a 10-character (30-bit)
     *                                octal number in which the most significant bit is the sign bit.
     *                                The remaining 29 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -536,870,912 or if number > 536,870,911, DEC2OCT
     *                                returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If DEC2OCT requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2OCT uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2OCT returns the #NUM! error value.
     * @param mixed $number
     * @param mixed $places
     */
    public static function funcDec2Oct($number, $places = null): string
    {
        $number = Functions::flattenSingleValue($number);
        $places = Functions::flattenSingleValue($places);

        Helpers::nullOrOdsBoolToNumber($number);
        if (is_bool($number)) {
            return Functions::VALUE();
        }
        //Helpers::gnumericFloatToInt($number);

        $number = (string) $number;
        if (strlen($number) > preg_match_all('/[-0123456789.]/', $number, $out)) {
            return Functions::VALUE();
        }
        $number = (int) floor((float) $number);
        $r = decoct($number);
        $r = substr($r, -10);

        return Helpers::nbrConversionFormat($r, $places);
    }
}
