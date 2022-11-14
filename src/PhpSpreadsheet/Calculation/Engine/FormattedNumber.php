<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class FormattedNumber
{
    /**    Constants                */
    /**    Regular Expressions        */
    //    Fraction
    private const STRING_REGEXP_FRACTION = '~^\s*(-?)((\d*)\s+)?(\d+\/\d+)\s*$~';

    private const STRING_REGEXP_PERCENT = '~^(?:(?: *(?<PrefixedSign>[-+])? *\% *(?<PrefixedSign2>[-+])? *(?<PrefixedValue>[0-9]+\.?[0-9*]*(?:E[-+]?[0-9]*)?) *)|(?: *(?<PostfixedSign>[-+])? *(?<PostfixedValue>[0-9]+\.?[0-9]*(?:E[-+]?[0-9]*)?) *\% *))$~i';

    private const STRING_CONVERSION_LIST = [
        [self::class, 'convertToNumberIfNumeric'],
        [self::class, 'convertToNumberIfFraction'],
        [self::class, 'convertToNumberIfPercent'],
    ];

    /**
     * Identify whether a string contains a formatted numeric value,
     * and convert it to a numeric if it is.
     *
     * @param string $operand string value to test
     */
    public static function convertToNumberIfFormatted(string &$operand): bool
    {
        foreach (self::STRING_CONVERSION_LIST as $conversionMethod) {
            if ($conversionMethod($operand) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Identify whether a string contains a numeric value,
     * and convert it to a numeric if it is.
     *
     * @param string $operand string value to test
     */
    public static function convertToNumberIfNumeric(string &$operand): bool
    {
        if (is_numeric($operand)) {
            $operand = (float) $operand;

            return true;
        }

        return false;
    }

    /**
     * Identify whether a string contains a fractional numeric value,
     * and convert it to a numeric if it is.
     *
     * @param string $operand string value to test
     */
    public static function convertToNumberIfFraction(string &$operand): bool
    {
        if (preg_match(self::STRING_REGEXP_FRACTION, $operand, $match)) {
            $sign = ($match[1] === '-') ? '-' : '+';
            $wholePart = ($match[3] === '') ? '' : ($sign . $match[3]);
            $fractionFormula = '=' . $wholePart . $sign . $match[4];
            $operand = Calculation::getInstance()->_calculateFormulaValue($fractionFormula);

            return true;
        }

        return false;
    }

    /**
     * Identify whether a string contains a percentage, and if so,
     * convert it to a numeric.
     *
     * @param string $operand string value to test
     */
    public static function convertToNumberIfPercent(string &$operand): bool
    {
        $match = [];
        if (preg_match(self::STRING_REGEXP_PERCENT, $operand, $match, PREG_UNMATCHED_AS_NULL)) {
            //Calculate the percentage
            $sign = ($match['PrefixedSign'] ?? $match['PrefixedSign2'] ?? $match['PostfixedSign']) ?? '';
            $operand = (float) ($sign . ($match['PostfixedValue'] ?? $match['PrefixedValue'])) / 100;

            return true;
        }

        return false;
    }
}
