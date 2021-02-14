<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ConvertDecimal extends ConvertBase
{
    /**
     * toBinary.
     *
     * Return a decimal value as binary.
     *
     * Excel Function:
     *        DEC2BIN(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
     *                          valid place values are ignored and DEC2BIN returns a 10-character
     *                          (10-bit) binary number in which the most significant bit is the sign
     *                          bit. The remaining 9 bits are magnitude bits. Negative numbers are
     *                          represented using two's-complement notation.
     *                      If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
     *                          value.
     *                      If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                      If DEC2BIN requires more than places characters, it returns the #NUM!
     *                          error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2BIN uses
     *                          the minimum number of characters necessary. Places is useful for
     *                          padding the return value with leading 0s (zeros).
     *                      If places is not an integer, it is truncated.
     *                      If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                      If places is zero or negative, DEC2BIN returns the #NUM! error value.
     */
    public static function toBinary($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateDecimal($value);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $value = (int) floor((float) $value);
        if ($value < -512 || $value > 511) {
            return Functions::NAN();
        }

        $r = decbin($value);
        // Two's Complement
        $r = substr($r, -10);
        if (strlen($r) >= 11) {
            return Functions::NAN();
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * toHex.
     *
     * Return a decimal value as hex.
     *
     * Excel Function:
     *        DEC2HEX(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
     *                          places is ignored and DEC2HEX returns a 10-character (40-bit)
     *                          hexadecimal number in which the most significant bit is the sign
     *                          bit. The remaining 39 bits are magnitude bits. Negative numbers
     *                          are represented using two's-complement notation.
     *                      If number < -549,755,813,888 or if number > 549,755,813,887,
     *                          DEC2HEX returns the #NUM! error value.
     *                      If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                      If DEC2HEX requires more than places characters, it returns the
     *                          #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2HEX uses
     *                          the minimum number of characters necessary. Places is useful for
     *                          padding the return value with leading 0s (zeros).
     *                      If places is not an integer, it is truncated.
     *                      If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                      If places is zero or negative, DEC2HEX returns the #NUM! error value.
     */
    public static function toHex($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateDecimal($value);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $value = (int) floor((float) $value);
        $r = strtoupper(dechex($value));
        if (strlen($r) == 8) {
            //    Two's Complement
            $r = 'FF' . $r;
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * toOctal.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
     *                          places is ignored and DEC2OCT returns a 10-character (30-bit)
     *                          octal number in which the most significant bit is the sign bit.
     *                          The remaining 29 bits are magnitude bits. Negative numbers are
     *                          represented using two's-complement notation.
     *                      If number < -536,870,912 or if number > 536,870,911, DEC2OCT
     *                          returns the #NUM! error value.
     *                      If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                      If DEC2OCT requires more than places characters, it returns the
     *                          #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2OCT uses
     *                          the minimum number of characters necessary. Places is useful for
     *                          padding the return value with leading 0s (zeros).
     *                      If places is not an integer, it is truncated.
     *                      If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                      If places is zero or negative, DEC2OCT returns the #NUM! error value.
     */
    public static function toOctal($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $value = self::validateDecimal($value);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $value = (int) floor((float) $value);
        $r = decoct($value);
        if (strlen($r) == 11) {
            //    Two's Complement
            $r = substr($r, -10);
        }

        return self::nbrConversionFormat($r, $places);
    }

    protected static function validateDecimal(string $value): string
    {
        if (strlen($value) > preg_match_all('/[-0123456789.]/', $value)) {
            throw new Exception(Functions::VALUE());
        }

        return $value;
    }
}
