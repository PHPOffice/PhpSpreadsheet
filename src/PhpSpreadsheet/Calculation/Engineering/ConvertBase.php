<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class ConvertBase
{
    /**
     * BINTODEC.
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
    public static function BINTODEC($value): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value), true);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[01]/', $value, $out)) {
            return Functions::NAN();
        }
        if (strlen($value) > 10) {
            return Functions::NAN();
        } elseif (strlen($value) == 10) {
            //    Two's Complement
            $value = substr($value, -9);

            return '-' . (512 - bindec($value));
        }

        return bindec($value);
    }

    /**
     * BINTOHEX.
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
    public static function BINTOHEX($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value), true);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[01]/', $value, $out)) {
            return Functions::NAN();
        }
        if (strlen($value) > 10) {
            return Functions::NAN();
        } elseif (strlen($value) == 10) {
            //    Two's Complement
            return str_repeat('F', 8) . substr(strtoupper(dechex((int) bindec(substr($value, -9)))), -2);
        }
        $hexVal = (string) strtoupper(dechex((int) bindec($value)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * BINTOOCT.
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
    public static function BINTOOCT($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value), true);
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[01]/', $value, $out)) {
            return Functions::NAN();
        }
        if (strlen($value) > 10) {
            return Functions::NAN();
        } elseif (strlen($value) == 10) {
            //    Two's Complement
            return str_repeat('7', 7) . substr(strtoupper(decoct((int) bindec(substr($value, -9)))), -3);
        }
        $octVal = (string) decoct((int) bindec($value));

        return self::nbrConversionFormat($octVal, $places);
    }

    /**
     * DECTOBIN.
     *
     * Return a decimal value as binary.
     *
     * Excel Function:
     *        DEC2BIN(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
     *                                valid place values are ignored and DEC2BIN returns a 10-character
     *                                (10-bit) binary number in which the most significant bit is the sign
     *                                bit. The remaining 9 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
     *                                value.
     *                                If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If DEC2BIN requires more than places characters, it returns the #NUM!
     *                                error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2BIN uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2BIN returns the #NUM! error value.
     */
    public static function DECTOBIN($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[-0123456789.]/', $value, $out)) {
            return Functions::VALUE();
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
     * DECTOHEX.
     *
     * Return a decimal value as hex.
     *
     * Excel Function:
     *        DEC2HEX(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2HEX returns a 10-character (40-bit)
     *                                hexadecimal number in which the most significant bit is the sign
     *                                bit. The remaining 39 bits are magnitude bits. Negative numbers
     *                                are represented using two's-complement notation.
     *                                If number < -549,755,813,888 or if number > 549,755,813,887,
     *                                DEC2HEX returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If DEC2HEX requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2HEX uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2HEX returns the #NUM! error value.
     */
    public static function DECTOHEX($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[-0123456789.]/', $value, $out)) {
            return Functions::VALUE();
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
     * DECTOOCT.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @param string $value The decimal integer you want to convert. If number is negative,
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
     */
    public static function DECTOOCT($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[-0123456789.]/', $value, $out)) {
            return Functions::VALUE();
        }
        $value = (int) floor((float) $value);
        $r = decoct($value);
        if (strlen($r) == 11) {
            //    Two's Complement
            $r = substr($r, -10);
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * HEXTOBIN.
     *
     * Return a hex value as binary.
     *
     * Excel Function:
     *        HEX2BIN(x[,places])
     *
     * @param string $value the hexadecimal number you want to convert.
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
     */
    public static function HEXTOBIN($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($value), $out)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::HEXTODEC($value), $places);
    }

    /**
     * HEXTODEC.
     *
     * Return a hex value as decimal.
     *
     * Excel Function:
     *        HEX2DEC(x)
     *
     * @param string $value The hexadecimal number you want to convert. This number cannot
     *                                contain more than 10 characters (40 bits). The most significant
     *                                bit of number is the sign bit. The remaining 39 bits are magnitude
     *                                bits. Negative numbers are represented using two's-complement
     *                                notation.
     *                                If number is not a valid hexadecimal number, HEX2DEC returns the
     *                                #NUM! error value.
     */
    public static function HEXTODEC($value): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($value), $out)) {
            return Functions::NAN();
        }

        if (strlen($value) > 10) {
            return Functions::NAN();
        }

        $binX = '';
        foreach (str_split($value) as $char) {
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

    /**
     * HEXTOOCT.
     *
     * Return a hex value as octal.
     *
     * Excel Function:
     *        HEX2OCT(x[,places])
     *
     * @param string $value The hexadecimal number you want to convert. Number cannot
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
     */
    public static function HEXTOOCT($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (strlen($value) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($value), $out)) {
            return Functions::NAN();
        }

        $decimal = self::HEXTODEC($value);
        if ($decimal < -536870912 || $decimal > 536870911) {
            return Functions::NAN();
        }

        return self::DECTOOCT($decimal, $places);
    }

    /**
     * OCTTOBIN.
     *
     * Return an octal value as binary.
     *
     * Excel Function:
     *        OCT2BIN(x[,places])
     *
     * @param string $value The octal number you want to convert. Number may not
     *                                    contain more than 10 characters. The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits
     *                                    are magnitude bits. Negative numbers are represented
     *                                    using two's-complement notation.
     *                                    If number is negative, OCT2BIN ignores places and returns
     *                                    a 10-character binary number.
     *                                    If number is negative, it cannot be less than 7777777000,
     *                                    and if number is positive, it cannot be greater than 777.
     *                                    If number is not a valid octal number, OCT2BIN returns
     *                                    the #NUM! error value.
     *                                    If OCT2BIN requires more than places characters, it
     *                                    returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    OCT2BIN uses the minimum number of characters necessary.
     *                                    Places is useful for padding the return value with
     *                                    leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2BIN returns the #VALUE!
     *                                    error value.
     *                                    If places is negative, OCT2BIN returns the #NUM! error
     *                                    value.
     */
    public static function OCTTOBIN($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (preg_match_all('/[01234567]/', $value, $out) != strlen($value)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::OCTTODEC($value), $places);
    }

    /**
     * OCTTODEC.
     *
     * Return an octal value as decimal.
     *
     * Excel Function:
     *        OCT2DEC(x)
     *
     * @param string $value The octal number you want to convert. Number may not contain
     *                                more than 10 octal characters (30 bits). The most significant
     *                                bit of number is the sign bit. The remaining 29 bits are
     *                                magnitude bits. Negative numbers are represented using
     *                                two's-complement notation.
     *                                If number is not a valid octal number, OCT2DEC returns the
     *                                #NUM! error value.
     */
    public static function OCTTODEC($value): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (preg_match_all('/[01234567]/', $value, $out) != strlen($value)) {
            return Functions::NAN();
        }
        $binX = '';
        foreach (str_split($value) as $char) {
            $binX .= str_pad(decbin((int) $char), 3, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 30 && $binX[0] == '1') {
            for ($i = 0; $i < 30; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    /**
     * OCTTOHEX.
     *
     * Return an octal value as hex.
     *
     * Excel Function:
     *        OCT2HEX(x[,places])
     *
     * @param string $value The octal number you want to convert. Number may not contain
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
     */
    public static function OCTTOHEX($value, $places = null): string
    {
        try {
            $value = self::validateValue(Functions::flattenSingleValue($value));
            $places = self::validatePlaces(Functions::flattenSingleValue($places));
        } catch (Exception $e) {
            return $e->getMessage();
        }

        if (preg_match_all('/[01234567]/', $value, $out) != strlen($value)) {
            return Functions::NAN();
        }
        $hexVal = strtoupper(dechex((int) self::OCTTODEC((int) $value)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    private static function validateValue($value, bool $gnumericCheck = false): string
    {
        if (is_bool($value)) {
            if (Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_OPENOFFICE) {
                throw new Exception(Functions::VALUE());
            }
            $value = (int) $value;
        }

        if ($gnumericCheck && Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $value = floor((float) $value);
        }

        return (string) $value;
    }

    private static function validatePlaces($places = null): ?int
    {
        if ($places === null) {
            return $places;
        }

        if (is_numeric($places)) {
            if ($places < 0) {
                throw new Exception(Functions::NAN());
            }

            return (int) $places;
        }

        throw new Exception(Functions::VALUE());
    }

    /**
     * Formats a number base string value with leading zeroes.
     *
     * @param string $value The "number" to pad
     * @param ?int $places The length that we want to pad this value
     *
     * @return string The padded "number"
     */
    private static function nbrConversionFormat(string $value, ?int $places): string
    {
        if ($places !== null) {
            if (strlen($value) <= $places) {
                return substr(str_pad($value, $places, '0', STR_PAD_LEFT), -10);
            }

            return Functions::NAN();
        }

        return substr($value, -10);
    }
}
