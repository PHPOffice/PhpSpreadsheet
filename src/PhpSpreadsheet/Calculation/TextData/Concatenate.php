<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Concatenate
{
    /**
     * CONCATENATE.
     *
     * @return string
     */
    public static function CONCATENATE(...$args)
    {
        $returnValue = '';

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                $arg = self::convertBooleanValue($arg);
            }
            $returnValue .= $arg;
        }

        return $returnValue;
    }

    /**
     * TEXTJOIN.
     *
     * @param mixed $delimiter
     * @param mixed $ignoreEmpty
     * @param mixed $args
     *
     * @return string
     */
    public static function TEXTJOIN($delimiter, $ignoreEmpty, ...$args)
    {
        // Loop through arguments
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $key => &$arg) {
            if ($ignoreEmpty && trim($arg) == '') {
                unset($aArgs[$key]);
            } elseif (is_bool($arg)) {
                $arg = self::convertBooleanValue($arg);
            }
        }

        return implode($delimiter, $aArgs);
    }

    /**
     * REPT.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param string $str Should be numeric
     * @param mixed $number Should be int
     *
     * @return string
     */
    public static function builtinREPT($str, $number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number) || $number < 0) {
            return Functions::VALUE();
        }

        return str_repeat($str, $number);
    }

    private static function convertBooleanValue($value)
    {
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
            return (int) $value;
        }

        return ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
    }
}
