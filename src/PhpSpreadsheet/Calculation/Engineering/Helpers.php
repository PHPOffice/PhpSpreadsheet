<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Helpers
{
    /**
     * Convert null, or Boolean if ODS, to int.
     *
     * @param mixed $number
     */
    public static function nullOrOdsBoolToNumber(&$number): void
    {
        if ($number === null) {
            $number = 0;
        } elseif (is_bool($number)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $number = (int) $number;
            }
        }
    }

    /**
     * Convert float to int if Gnumeric.
     *
     * @param mixed $number
     */
    public static function gnumericFloatToInt(&$number): void
    {
        if (is_numeric($number) && Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $number = floor((float) $number);
        }
    }

    /**
     * Formats a number base string value with leading zeroes.
     *
     * @param string $xVal The "number" to pad
     * @param mixed $places The length that we want to pad this value
     */
    public static function nbrConversionFormat($xVal, $places): string
    {
        if ($places === null) {
            return substr($xVal, -10);
        }
        if (!is_numeric($places)) {
            return Functions::VALUE();
        }
        $places = (int) $places;
        if ($places < 0 || $places > 10) {
            return Functions::NAN();
        }
        if (strlen($xVal) <= $places) {
            return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
        }

        return Functions::NAN();
    }
}
