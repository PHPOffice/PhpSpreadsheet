<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ConvertBinary extends ConvertBase
{
    /**
     * toDecimal.
     *
     * Return a binary value as decimal.
     *
     * Excel Function:
     *        BIN2DEC(x)
     *
     * @param string $value The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2DEC returns the #NUM! error value.
     */
    public static function toDecimal($value): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateBinary($value);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) == 10) {
            //    Two's Complement
            $value = substr($value, -9);

            return '-' . (512 - bindec($value));
        }

        return (string) bindec($value);
    }

    /**
     * toHex.
     *
     * Return a binary value as hex.
     *
     * Excel Function:
     *        BIN2HEX(x[,places])
     *
     * @param string $value The binary number (as a string) that you want to convert. The number
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
     */
    public static function toHex($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateBinary($value);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) == 10) {
            $high2 = substr($value, 0, 2);
            $low8 = substr($value, 2);
            $xarr = ['00' => '00000000', '01' => '00000001', '10' => 'FFFFFFFE', '11' => 'FFFFFFFF'];

            return $xarr[$high2] . strtoupper(substr('0' . dechex((int) bindec($low8)), -2));
        }
        $hexVal = (string) strtoupper(dechex((int) bindec($value)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * toOctal.
     *
     * Return a binary value as octal.
     *
     * Excel Function:
     *        BIN2OCT(x[,places])
     *
     * @param string $value The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2OCT returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2OCT uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2OCT returns the #VALUE! error value.
     *                                If places is negative, BIN2OCT returns the #NUM! error value.
     */
    public static function toOctal($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateBinary($value);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) == 10 && substr($value, 0, 1) === '1') { //    Two's Complement
            return str_repeat('7', 6) . strtoupper(decoct((int) bindec("11$value")));
        }
        $octVal = (string) decoct((int) bindec($value));

        return self::nbrConversionFormat($octVal, $places);
    }

    protected static function validateBinary(string $value): string
    {
        if ((strlen($value) > preg_match_all('/[01]/', $value)) || (strlen($value) > 10)) {
            throw new Exception(Functions::NAN());
        }

        return $value;
    }
}
