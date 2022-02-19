<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Concatenate
{
    use ArrayEnabled;

    /**
     * CONCATENATE.
     *
     * @param array $args
     */
    public static function CONCATENATE(...$args): string
    {
        $returnValue = '';

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);

        foreach ($aArgs as $arg) {
            $returnValue .= Helpers::extractString($arg);
        }

        return $returnValue;
    }

    /**
     * TEXTJOIN.
     *
     * @param mixed $delimiter The delimter to use between the joined arguments
     *                         Or can be an array of values
     * @param mixed $ignoreEmpty true/false Flag indicating whether empty arguments should be skipped
     *                         Or can be an array of values
     * @param mixed $args The values to join
     *
     * @return array|string The joined string
     *         If an array of values is passed for the $delimiter or $ignoreEmpty arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function TEXTJOIN($delimiter, $ignoreEmpty, ...$args)
    {
        if (is_array($delimiter) || is_array($ignoreEmpty)) {
            return self::evaluateArrayArgumentsSubset(
                [self::class, __FUNCTION__],
                2,
                $delimiter,
                $ignoreEmpty,
                ...$args
            );
        }

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $key => &$arg) {
            if ($ignoreEmpty === true && is_string($arg) && trim($arg) === '') {
                unset($aArgs[$key]);
            } elseif (is_bool($arg)) {
                $arg = Helpers::convertBooleanValue($arg);
            }
        }

        return implode($delimiter, $aArgs);
    }

    /**
     * REPT.
     *
     * Returns the result of builtin function round after validating args.
     *
     * @param mixed $stringValue The value to repeat
     *                         Or can be an array of values
     * @param mixed $repeatCount The number of times the string value should be repeated
     *                         Or can be an array of values
     *
     * @return array|string The repeated string
     *         If an array of values is passed for the $stringValue or $repeatCount arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function builtinREPT($stringValue, $repeatCount)
    {
        if (is_array($stringValue) || is_array($repeatCount)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $stringValue, $repeatCount);
        }

        $stringValue = Helpers::extractString($stringValue);

        if (!is_numeric($repeatCount) || $repeatCount < 0) {
            return ExcelError::VALUE();
        }

        return str_repeat($stringValue, (int) $repeatCount);
    }
}
