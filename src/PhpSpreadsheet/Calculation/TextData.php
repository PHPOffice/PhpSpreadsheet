<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use DateTimeInterface;

/**
 * @deprecated 1.18.0
 */
class TextData
{
    /**
     * CHARACTER.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the character() method in the TextData\CharacterConvert class instead
     *
     * @param string $character Value
     *
     * @return array|string
     */
    public static function CHARACTER($character)
    {
        return TextData\CharacterConvert::character($character);
    }

    /**
     * TRIMNONPRINTABLE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the nonPrintable() method in the TextData\Trim class instead
     *
     * @param mixed $stringValue Value to check
     *
     * @return null|array|string
     */
    public static function TRIMNONPRINTABLE($stringValue = '')
    {
        return TextData\Trim::nonPrintable($stringValue);
    }

    /**
     * TRIMSPACES.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the spaces() method in the TextData\Trim class instead
     *
     * @param mixed $stringValue Value to check
     *
     * @return array|string
     */
    public static function TRIMSPACES($stringValue = '')
    {
        return TextData\Trim::spaces($stringValue);
    }

    /**
     * ASCIICODE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the code() method in the TextData\CharacterConvert class instead
     *
     * @param array|string $characters Value
     *
     * @return array|int|string A string if arguments are invalid
     */
    public static function ASCIICODE($characters)
    {
        return TextData\CharacterConvert::code($characters);
    }

    /**
     * CONCATENATE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the CONCATENATE() method in the TextData\Concatenate class instead
     *
     * @return string
     */
    public static function CONCATENATE(...$args)
    {
        return TextData\Concatenate::CONCATENATE(...$args);
    }

    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @Deprecated 1.18.0
     *
     * @see Use the DOLLAR() method in the TextData\Format class instead
     *
     * @param float $value The value to format
     * @param int $decimals The number of digits to display to the right of the decimal point.
     *                                    If decimals is negative, number is rounded to the left of the decimal point.
     *                                    If you omit decimals, it is assumed to be 2
     *
     * @return array|string
     */
    public static function DOLLAR($value = 0, $decimals = 2)
    {
        return TextData\Format::DOLLAR($value, $decimals);
    }

    /**
     * FIND.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the sensitive() method in the TextData\Search class instead
     *
     * @param array|string $needle The string to look for
     * @param array|string $haystack The string in which to look
     * @param array|int $offset Offset within $haystack
     *
     * @return array|int|string
     */
    public static function SEARCHSENSITIVE($needle, $haystack, $offset = 1)
    {
        return TextData\Search::sensitive($needle, $haystack, $offset);
    }

    /**
     * SEARCH.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the insensitive() method in the TextData\Search class instead
     *
     * @param array|string $needle The string to look for
     * @param array|string $haystack The string in which to look
     * @param array|int $offset Offset within $haystack
     *
     * @return array|int|string
     */
    public static function SEARCHINSENSITIVE($needle, $haystack, $offset = 1)
    {
        return TextData\Search::insensitive($needle, $haystack, $offset);
    }

    /**
     * FIXEDFORMAT.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the FIXEDFORMAT() method in the TextData\Format class instead
     *
     * @param mixed $value Value to check
     * @param int $decimals
     * @param bool $no_commas
     *
     * @return array|string
     */
    public static function FIXEDFORMAT($value, $decimals = 2, $no_commas = false)
    {
        return TextData\Format::FIXEDFORMAT($value, $decimals, $no_commas);
    }

    /**
     * LEFT.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the left() method in the TextData\Extract class instead
     *
     * @param array|string $value Value
     * @param array|int $chars Number of characters
     *
     * @return array|string
     */
    public static function LEFT($value = '', $chars = 1)
    {
        return TextData\Extract::left($value, $chars);
    }

    /**
     * MID.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the mid() method in the TextData\Extract class instead
     *
     * @param array|string $value Value
     * @param array|int $start Start character
     * @param array|int $chars Number of characters
     *
     * @return array|string
     */
    public static function MID($value = '', $start = 1, $chars = null)
    {
        return TextData\Extract::mid($value, $start, $chars);
    }

    /**
     * RIGHT.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the right() method in the TextData\Extract class instead
     *
     * @param array|string $value Value
     * @param array|int $chars Number of characters
     *
     * @return array|string
     */
    public static function RIGHT($value = '', $chars = 1)
    {
        return TextData\Extract::right($value, $chars);
    }

    /**
     * STRINGLENGTH.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the length() method in the TextData\Text class instead
     *
     * @param string $value Value
     *
     * @return array|int
     */
    public static function STRINGLENGTH($value = '')
    {
        return TextData\Text::length($value);
    }

    /**
     * LOWERCASE.
     *
     * Converts a string value to lower case.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the lower() method in the TextData\CaseConvert class instead
     *
     * @param array|string $mixedCaseString
     *
     * @return array|string
     */
    public static function LOWERCASE($mixedCaseString)
    {
        return TextData\CaseConvert::lower($mixedCaseString);
    }

    /**
     * UPPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the upper() method in the TextData\CaseConvert class instead
     *
     * @param string $mixedCaseString
     *
     * @return array|string
     */
    public static function UPPERCASE($mixedCaseString)
    {
        return TextData\CaseConvert::upper($mixedCaseString);
    }

    /**
     * PROPERCASE.
     *
     * Converts a string value to proper/title case.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the proper() method in the TextData\CaseConvert class instead
     *
     * @param array|string $mixedCaseString
     *
     * @return array|string
     */
    public static function PROPERCASE($mixedCaseString)
    {
        return TextData\CaseConvert::proper($mixedCaseString);
    }

    /**
     * REPLACE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the replace() method in the TextData\Replace class instead
     *
     * @param string $oldText String to modify
     * @param int $start Start character
     * @param int $chars Number of characters
     * @param string $newText String to replace in defined position
     *
     * @return array|string
     */
    public static function REPLACE($oldText, $start, $chars, $newText)
    {
        return TextData\Replace::replace($oldText, $start, $chars, $newText);
    }

    /**
     * SUBSTITUTE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the substitute() method in the TextData\Replace class instead
     *
     * @param string $text Value
     * @param string $fromText From Value
     * @param string $toText To Value
     * @param int $instance Instance Number
     *
     * @return array|string
     */
    public static function SUBSTITUTE($text = '', $fromText = '', $toText = '', $instance = 0)
    {
        return TextData\Replace::substitute($text, $fromText, $toText, $instance);
    }

    /**
     * RETURNSTRING.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the test() method in the TextData\Text class instead
     *
     * @param mixed $testValue Value to check
     *
     * @return null|array|string
     */
    public static function RETURNSTRING($testValue = '')
    {
        return TextData\Text::test($testValue);
    }

    /**
     * TEXTFORMAT.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the TEXTFORMAT() method in the TextData\Format class instead
     *
     * @param mixed $value Value to check
     * @param string $format Format mask to use
     *
     * @return array|string
     */
    public static function TEXTFORMAT($value, $format)
    {
        return TextData\Format::TEXTFORMAT($value, $format);
    }

    /**
     * VALUE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the VALUE() method in the TextData\Format class instead
     *
     * @param mixed $value Value to check
     *
     * @return array|DateTimeInterface|float|int|string A string if arguments are invalid
     */
    public static function VALUE($value = '')
    {
        return TextData\Format::VALUE($value);
    }

    /**
     * NUMBERVALUE.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the NUMBERVALUE() method in the TextData\Format class instead
     *
     * @param mixed $value Value to check
     * @param string $decimalSeparator decimal separator, defaults to locale defined value
     * @param string $groupSeparator group/thosands separator, defaults to locale defined value
     *
     * @return array|float|string
     */
    public static function NUMBERVALUE($value = '', $decimalSeparator = null, $groupSeparator = null)
    {
        return TextData\Format::NUMBERVALUE($value, $decimalSeparator, $groupSeparator);
    }

    /**
     * Compares two text strings and returns TRUE if they are exactly the same, FALSE otherwise.
     * EXACT is case-sensitive but ignores formatting differences.
     * Use EXACT to test text being entered into a document.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the exact() method in the TextData\Text class instead
     *
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return array|bool
     */
    public static function EXACT($value1, $value2)
    {
        return TextData\Text::exact($value1, $value2);
    }

    /**
     * TEXTJOIN.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the TEXTJOIN() method in the TextData\Concatenate class instead
     *
     * @param mixed $delimiter
     * @param mixed $ignoreEmpty
     * @param mixed $args
     *
     * @return array|string
     */
    public static function TEXTJOIN($delimiter, $ignoreEmpty, ...$args)
    {
        return TextData\Concatenate::TEXTJOIN($delimiter, $ignoreEmpty, ...$args);
    }

    /**
     * REPT.
     *
     * Returns the result of builtin function repeat after validating args.
     *
     * @Deprecated 1.18.0
     *
     * @see Use the builtinREPT() method in the TextData\Concatenate class instead
     *
     * @param array|string $str Should be numeric
     * @param mixed $number Should be int
     *
     * @return array|string
     */
    public static function builtinREPT($str, $number)
    {
        return TextData\Concatenate::builtinREPT($str, $number);
    }
}
