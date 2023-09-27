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
     * @deprecated 1.18.0
     *      Use the character() method in the TextData\CharacterConvert class instead
     * @see TextData\CharacterConvert::character()
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
     * @deprecated 1.18.0
     *      Use the nonPrintable() method in the TextData\Trim class instead
     * @see TextData\Trim::nonPrintable()
     *
     * @param mixed $stringValue Value to check
     *
     * @return null|array|string
     */
    public static function TRIMNONPRINTABLE(mixed $stringValue = '')
    {
        return TextData\Trim::nonPrintable($stringValue);
    }

    /**
     * TRIMSPACES.
     *
     * @deprecated 1.18.0
     *      Use the spaces() method in the TextData\Trim class instead
     * @see TextData\Trim::spaces()
     *
     * @param mixed $stringValue Value to check
     */
    public static function TRIMSPACES(mixed $stringValue = ''): string|array
    {
        return TextData\Trim::spaces($stringValue);
    }

    /**
     * ASCIICODE.
     *
     * @deprecated 1.18.0
     *      Use the code() method in the TextData\CharacterConvert class instead
     * @see TextData\CharacterConvert::code()
     *
     * @param array|string $characters Value
     *
     * @return array|int|string A string if arguments are invalid
     */
    public static function ASCIICODE($characters): string|int|array
    {
        return TextData\CharacterConvert::code($characters);
    }

    /**
     * CONCATENATE.
     *
     * @deprecated 1.18.0
     *      Use the CONCATENATE() method in the TextData\Concatenate class instead
     * @see TextData\Concatenate::CONCATENATE()
     *
     * @param array $args
     */
    public static function CONCATENATE(...$args): string
    {
        return TextData\Concatenate::CONCATENATE(...$args);
    }

    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @deprecated 1.18.0
     *      Use the DOLLAR() method in the TextData\Format class instead
     * @see TextData\Format::DOLLAR()
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
     * @deprecated 1.18.0
     *      Use the sensitive() method in the TextData\Search class instead
     * @see TextData\Search::sensitive()
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
     * @deprecated 1.18.0
     *      Use the insensitive() method in the TextData\Search class instead
     * @see TextData\Search::insensitive()
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
     * @deprecated 1.18.0
     *      Use the FIXEDFORMAT() method in the TextData\Format class instead
     * @see TextData\Format::FIXEDFORMAT()
     *
     * @param mixed $value Value to check
     * @param int $decimals
     * @param bool $no_commas
     *
     * @return array|string
     */
    public static function FIXEDFORMAT(mixed $value, $decimals = 2, $no_commas = false)
    {
        return TextData\Format::FIXEDFORMAT($value, $decimals, $no_commas);
    }

    /**
     * LEFT.
     *
     * @deprecated 1.18.0
     *      Use the left() method in the TextData\Extract class instead
     * @see TextData\Extract::left()
     *
     * @param array|string $value Value
     * @param array|int $chars Number of characters
     */
    public static function LEFT($value = '', $chars = 1): string|array
    {
        return TextData\Extract::left($value, $chars);
    }

    /**
     * MID.
     *
     * @deprecated 1.18.0
     *      Use the mid() method in the TextData\Extract class instead
     * @see TextData\Extract::mid()
     *
     * @param array|string $value Value
     * @param array|int $start Start character
     * @param array|int $chars Number of characters
     */
    public static function MID($value = '', $start = 1, $chars = null): string|array
    {
        return TextData\Extract::mid($value, $start, $chars);
    }

    /**
     * RIGHT.
     *
     * @deprecated 1.18.0
     *      Use the right() method in the TextData\Extract class instead
     * @see TextData\Extract::right()
     *
     * @param array|string $value Value
     * @param array|int $chars Number of characters
     */
    public static function RIGHT($value = '', $chars = 1): string|array
    {
        return TextData\Extract::right($value, $chars);
    }

    /**
     * STRINGLENGTH.
     *
     * @deprecated 1.18.0
     *      Use the length() method in the TextData\Text class instead
     * @see TextData\Text::length()
     *
     * @param string $value Value
     */
    public static function STRINGLENGTH($value = ''): int|array
    {
        return TextData\Text::length($value);
    }

    /**
     * LOWERCASE.
     *
     * Converts a string value to lower case.
     *
     * @deprecated 1.18.0
     *      Use the lower() method in the TextData\CaseConvert class instead
     * @see TextData\CaseConvert::lower()
     *
     * @param array|string $mixedCaseString
     */
    public static function LOWERCASE($mixedCaseString): string|array
    {
        return TextData\CaseConvert::lower($mixedCaseString);
    }

    /**
     * UPPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @deprecated 1.18.0
     *      Use the upper() method in the TextData\CaseConvert class instead
     * @see TextData\CaseConvert::upper()
     *
     * @param string $mixedCaseString
     */
    public static function UPPERCASE($mixedCaseString): string|array
    {
        return TextData\CaseConvert::upper($mixedCaseString);
    }

    /**
     * PROPERCASE.
     *
     * Converts a string value to proper/title case.
     *
     * @deprecated 1.18.0
     *      Use the proper() method in the TextData\CaseConvert class instead
     * @see TextData\CaseConvert::proper()
     *
     * @param array|string $mixedCaseString
     */
    public static function PROPERCASE($mixedCaseString): string|array
    {
        return TextData\CaseConvert::proper($mixedCaseString);
    }

    /**
     * REPLACE.
     *
     * @deprecated 1.18.0
     *      Use the replace() method in the TextData\Replace class instead
     * @see TextData\Replace::replace()
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
     * @deprecated 1.18.0
     *      Use the substitute() method in the TextData\Replace class instead
     * @see TextData\Replace::substitute()
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
     * @deprecated 1.18.0
     *      Use the test() method in the TextData\Text class instead
     * @see TextData\Text::test()
     *
     * @param mixed $testValue Value to check
     */
    public static function RETURNSTRING(mixed $testValue = ''): string|array
    {
        return TextData\Text::test($testValue);
    }

    /**
     * TEXTFORMAT.
     *
     * @deprecated 1.18.0
     *      Use the TEXTFORMAT() method in the TextData\Format class instead
     * @see TextData\Format::TEXTFORMAT()
     *
     * @param mixed $value Value to check
     * @param string $format Format mask to use
     *
     * @return array|string
     */
    public static function TEXTFORMAT(mixed $value, $format)
    {
        return TextData\Format::TEXTFORMAT($value, $format);
    }

    /**
     * VALUE.
     *
     * @deprecated 1.18.0
     *      Use the VALUE() method in the TextData\Format class instead
     * @see TextData\Format::VALUE()
     *
     * @param mixed $value Value to check
     *
     * @return array|DateTimeInterface|float|int|string A string if arguments are invalid
     */
    public static function VALUE(mixed $value = '')
    {
        return TextData\Format::VALUE($value);
    }

    /**
     * NUMBERVALUE.
     *
     * @deprecated 1.18.0
     *      Use the NUMBERVALUE() method in the TextData\Format class instead
     * @see TextData\Format::NUMBERVALUE()
     *
     * @param mixed $value Value to check
     * @param string $decimalSeparator decimal separator, defaults to locale defined value
     * @param string $groupSeparator group/thosands separator, defaults to locale defined value
     *
     * @return array|float|string
     */
    public static function NUMBERVALUE(mixed $value = '', $decimalSeparator = null, $groupSeparator = null)
    {
        return TextData\Format::NUMBERVALUE($value, $decimalSeparator, $groupSeparator);
    }

    /**
     * Compares two text strings and returns TRUE if they are exactly the same, FALSE otherwise.
     * EXACT is case-sensitive but ignores formatting differences.
     * Use EXACT to test text being entered into a document.
     *
     * @deprecated 1.18.0
     *      Use the exact() method in the TextData\Text class instead
     * @see TextData\Text::exact()
     */
    public static function EXACT(mixed $value1, mixed $value2): bool|array
    {
        return TextData\Text::exact($value1, $value2);
    }

    /**
     * TEXTJOIN.
     *
     * @deprecated 1.18.0
     *      Use the TEXTJOIN() method in the TextData\Concatenate class instead
     * @see TextData\Concatenate::TEXTJOIN()
     *
     * @return array|string
     */
    public static function TEXTJOIN(mixed $delimiter, mixed $ignoreEmpty, mixed ...$args)
    {
        return TextData\Concatenate::TEXTJOIN($delimiter, $ignoreEmpty, ...$args);
    }

    /**
     * REPT.
     *
     * Returns the result of builtin function repeat after validating args.
     *
     * @deprecated 1.18.0
     *      Use the builtinREPT() method in the TextData\Concatenate class instead
     * @see TextData\Concatenate::builtinREPT()
     *
     * @param array|string $str Should be numeric
     * @param mixed $number Should be int
     *
     * @return array|string
     */
    public static function builtinREPT($str, mixed $number)
    {
        return TextData\Concatenate::builtinREPT($str, $number);
    }
}
