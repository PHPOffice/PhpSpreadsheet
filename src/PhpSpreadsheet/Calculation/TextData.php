<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TextData
{
    private static $invalidChars;

    private static function unicodeToOrd($character)
    {
        return unpack('V', iconv('UTF-8', 'UCS-4LE', $character))[1];
    }

    /**
     * CHARACTER.
     *
     * @param string $character Value
     *
     * @return string
     */
    public static function CHARACTER($character)
    {
        $character = Functions::flattenSingleValue($character);

        if ((!is_numeric($character)) || ($character < 0)) {
            return Functions::VALUE();
        }

        if (function_exists('iconv')) {
            return iconv('UCS-4LE', 'UTF-8', pack('V', $character));
        }

        return mb_convert_encoding('&#' . (int) $character . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * TRIMNONPRINTABLE.
     *
     * @param mixed $stringValue Value to check
     *
     * @return string
     */
    public static function TRIMNONPRINTABLE($stringValue = '')
    {
        $stringValue = Functions::flattenSingleValue($stringValue);

        if (is_bool($stringValue)) {
            return ($stringValue) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        if (self::$invalidChars === null) {
            self::$invalidChars = range(chr(0), chr(31));
        }

        if (is_string($stringValue) || is_numeric($stringValue)) {
            return str_replace(self::$invalidChars, '', trim($stringValue, "\x00..\x1F"));
        }

        return null;
    }

    /**
     * TRIMSPACES.
     *
     * @param mixed $stringValue Value to check
     *
     * @return string
     */
    public static function TRIMSPACES($stringValue = '')
    {
        $stringValue = Functions::flattenSingleValue($stringValue);
        if (is_bool($stringValue)) {
            return ($stringValue) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        if (is_string($stringValue) || is_numeric($stringValue)) {
            return trim(preg_replace('/ +/', ' ', trim($stringValue, ' ')), ' ');
        }

        return null;
    }

    private static function convertBooleanValue($value)
    {
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
            return (int) $value;
        }

        return ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
    }

    /**
     * ASCIICODE.
     *
     * @param string $characters Value
     *
     * @return int
     */
    public static function ASCIICODE($characters)
    {
        if (($characters === null) || ($characters === '')) {
            return Functions::VALUE();
        }
        $characters = Functions::flattenSingleValue($characters);
        if (is_bool($characters)) {
            $characters = self::convertBooleanValue($characters);
        }

        $character = $characters;
        if (mb_strlen($characters, 'UTF-8') > 1) {
            $character = mb_substr($characters, 0, 1, 'UTF-8');
        }

        return self::unicodeToOrd($character);
    }

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
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @param float $value The value to format
     * @param int $decimals The number of digits to display to the right of the decimal point.
     *                                    If decimals is negative, number is rounded to the left of the decimal point.
     *                                    If you omit decimals, it is assumed to be 2
     *
     * @return string
     */
    public static function DOLLAR($value = 0, $decimals = 2)
    {
        $value = Functions::flattenSingleValue($value);
        $decimals = $decimals === null ? 0 : Functions::flattenSingleValue($decimals);

        // Validate parameters
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return Functions::NAN();
        }
        $decimals = floor($decimals);

        $mask = '$#,##0';
        if ($decimals > 0) {
            $mask .= '.' . str_repeat('0', $decimals);
        } else {
            $round = pow(10, abs($decimals));
            if ($value < 0) {
                $round = 0 - $round;
            }
            $value = MathTrig::MROUND($value, $round);
        }

        return NumberFormat::toFormattedString($value, $mask);
    }

    /**
     * SEARCHSENSITIVE.
     *
     * @param string $needle The string to look for
     * @param string $haystack The string in which to look
     * @param int $offset Offset within $haystack
     *
     * @return string
     */
    public static function SEARCHSENSITIVE($needle, $haystack, $offset = 1)
    {
        $needle = Functions::flattenSingleValue($needle);
        $haystack = Functions::flattenSingleValue($haystack);
        $offset = Functions::flattenSingleValue($offset);

        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = ($haystack) ? Calculation::getTRUE() : Calculation::getFALSE();
            }

            if (($offset > 0) && (StringHelper::countCharacters($haystack) > $offset)) {
                if (StringHelper::countCharacters($needle) === 0) {
                    return $offset;
                }

                $pos = mb_strpos($haystack, $needle, --$offset, 'UTF-8');
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }

        return Functions::VALUE();
    }

    /**
     * SEARCHINSENSITIVE.
     *
     * @param string $needle The string to look for
     * @param string $haystack The string in which to look
     * @param int $offset Offset within $haystack
     *
     * @return string
     */
    public static function SEARCHINSENSITIVE($needle, $haystack, $offset = 1)
    {
        $needle = Functions::flattenSingleValue($needle);
        $haystack = Functions::flattenSingleValue($haystack);
        $offset = Functions::flattenSingleValue($offset);

        if (!is_bool($needle)) {
            if (is_bool($haystack)) {
                $haystack = ($haystack) ? Calculation::getTRUE() : Calculation::getFALSE();
            }

            if (($offset > 0) && (StringHelper::countCharacters($haystack) > $offset)) {
                if (StringHelper::countCharacters($needle) === 0) {
                    return $offset;
                }

                $pos = mb_stripos($haystack, $needle, --$offset, 'UTF-8');
                if ($pos !== false) {
                    return ++$pos;
                }
            }
        }

        return Functions::VALUE();
    }

    /**
     * FIXEDFORMAT.
     *
     * @param mixed $value Value to check
     * @param int $decimals
     * @param bool $no_commas
     *
     * @return string
     */
    public static function FIXEDFORMAT($value, $decimals = 2, $no_commas = false)
    {
        $value = Functions::flattenSingleValue($value);
        $decimals = Functions::flattenSingleValue($decimals);
        $no_commas = Functions::flattenSingleValue($no_commas);

        // Validate parameters
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return Functions::NAN();
        }
        $decimals = (int) floor($decimals);

        $valueResult = round($value, $decimals);
        if ($decimals < 0) {
            $decimals = 0;
        }
        if (!$no_commas) {
            $valueResult = number_format(
                $valueResult,
                $decimals,
                StringHelper::getDecimalSeparator(),
                StringHelper::getThousandsSeparator()
            );
        }

        return (string) $valueResult;
    }

    /**
     * LEFT.
     *
     * @param string $value Value
     * @param int $chars Number of characters
     *
     * @return string
     */
    public static function LEFT($value = '', $chars = 1)
    {
        $value = Functions::flattenSingleValue($value);
        $chars = Functions::flattenSingleValue($chars);

        if ($chars < 0) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_substr($value, 0, $chars, 'UTF-8');
    }

    /**
     * MID.
     *
     * @param string $value Value
     * @param int $start Start character
     * @param int $chars Number of characters
     *
     * @return string
     */
    public static function MID($value = '', $start = 1, $chars = null)
    {
        $value = Functions::flattenSingleValue($value);
        $start = Functions::flattenSingleValue($start);
        $chars = Functions::flattenSingleValue($chars);

        if (($start < 1) || ($chars < 0)) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        if (empty($chars)) {
            return '';
        }

        return mb_substr($value, --$start, $chars, 'UTF-8');
    }

    /**
     * RIGHT.
     *
     * @param string $value Value
     * @param int $chars Number of characters
     *
     * @return string
     */
    public static function RIGHT($value = '', $chars = 1)
    {
        $value = Functions::flattenSingleValue($value);
        $chars = Functions::flattenSingleValue($chars);

        if ($chars < 0) {
            return Functions::VALUE();
        }

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_substr($value, mb_strlen($value, 'UTF-8') - $chars, $chars, 'UTF-8');
    }

    /**
     * STRINGLENGTH.
     *
     * @param string $value Value
     *
     * @return int
     */
    public static function STRINGLENGTH($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        if (is_bool($value)) {
            $value = ($value) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return mb_strlen($value, 'UTF-8');
    }

    /**
     * LOWERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param string $mixedCaseString
     *
     * @return string
     */
    public static function LOWERCASE($mixedCaseString)
    {
        $mixedCaseString = Functions::flattenSingleValue($mixedCaseString);

        if (is_bool($mixedCaseString)) {
            $mixedCaseString = ($mixedCaseString) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToLower($mixedCaseString);
    }

    /**
     * UPPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param string $mixedCaseString
     *
     * @return string
     */
    public static function UPPERCASE($mixedCaseString)
    {
        $mixedCaseString = Functions::flattenSingleValue($mixedCaseString);

        if (is_bool($mixedCaseString)) {
            $mixedCaseString = ($mixedCaseString) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToUpper($mixedCaseString);
    }

    /**
     * PROPERCASE.
     *
     * Converts a string value to upper case.
     *
     * @param string $mixedCaseString
     *
     * @return string
     */
    public static function PROPERCASE($mixedCaseString)
    {
        $mixedCaseString = Functions::flattenSingleValue($mixedCaseString);

        if (is_bool($mixedCaseString)) {
            $mixedCaseString = ($mixedCaseString) ? Calculation::getTRUE() : Calculation::getFALSE();
        }

        return StringHelper::strToTitle($mixedCaseString);
    }

    /**
     * REPLACE.
     *
     * @param string $oldText String to modify
     * @param int $start Start character
     * @param int $chars Number of characters
     * @param string $newText String to replace in defined position
     *
     * @return string
     */
    public static function REPLACE($oldText, $start, $chars, $newText)
    {
        $oldText = Functions::flattenSingleValue($oldText);
        $start = Functions::flattenSingleValue($start);
        $chars = Functions::flattenSingleValue($chars);
        $newText = Functions::flattenSingleValue($newText);

        $left = self::LEFT($oldText, $start - 1);
        $right = self::RIGHT($oldText, self::STRINGLENGTH($oldText) - ($start + $chars) + 1);

        return $left . $newText . $right;
    }

    /**
     * SUBSTITUTE.
     *
     * @param string $text Value
     * @param string $fromText From Value
     * @param string $toText To Value
     * @param int $instance Instance Number
     *
     * @return string
     */
    public static function SUBSTITUTE($text = '', $fromText = '', $toText = '', $instance = 0)
    {
        $text = Functions::flattenSingleValue($text);
        $fromText = Functions::flattenSingleValue($fromText);
        $toText = Functions::flattenSingleValue($toText);
        $instance = floor(Functions::flattenSingleValue($instance));

        if ($instance == 0) {
            return str_replace($fromText, $toText, $text);
        }

        $pos = -1;
        while ($instance > 0) {
            $pos = mb_strpos($text, $fromText, $pos + 1, 'UTF-8');
            if ($pos === false) {
                break;
            }
            --$instance;
        }

        if ($pos !== false) {
            return self::REPLACE($text, ++$pos, mb_strlen($fromText, 'UTF-8'), $toText);
        }

        return $text;
    }

    /**
     * RETURNSTRING.
     *
     * @param mixed $testValue Value to check
     *
     * @return null|string
     */
    public static function RETURNSTRING($testValue = '')
    {
        $testValue = Functions::flattenSingleValue($testValue);

        if (is_string($testValue)) {
            return $testValue;
        }

        return null;
    }

    /**
     * TEXTFORMAT.
     *
     * @param mixed $value Value to check
     * @param string $format Format mask to use
     *
     * @return string
     */
    public static function TEXTFORMAT($value, $format)
    {
        $value = Functions::flattenSingleValue($value);
        $format = Functions::flattenSingleValue($format);

        if ((is_string($value)) && (!is_numeric($value)) && Date::isDateTimeFormatCode($format)) {
            $value = DateTime::DATEVALUE($value);
        }

        return (string) NumberFormat::toFormattedString($value, $format);
    }

    /**
     * VALUE.
     *
     * @param mixed $value Value to check
     *
     * @return bool
     */
    public static function VALUE($value = '')
    {
        $value = Functions::flattenSingleValue($value);

        if (!is_numeric($value)) {
            $numberValue = str_replace(
                StringHelper::getThousandsSeparator(),
                '',
                trim($value, " \t\n\r\0\x0B" . StringHelper::getCurrencyCode())
            );
            if (is_numeric($numberValue)) {
                return (float) $numberValue;
            }

            $dateSetting = Functions::getReturnDateType();
            Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);

            if (strpos($value, ':') !== false) {
                $timeValue = DateTime::TIMEVALUE($value);
                if ($timeValue !== Functions::VALUE()) {
                    Functions::setReturnDateType($dateSetting);

                    return $timeValue;
                }
            }
            $dateValue = DateTime::DATEVALUE($value);
            if ($dateValue !== Functions::VALUE()) {
                Functions::setReturnDateType($dateSetting);

                return $dateValue;
            }
            Functions::setReturnDateType($dateSetting);

            return Functions::VALUE();
        }

        return (float) $value;
    }

    /**
     * NUMBERVALUE.
     *
     * @param mixed $value Value to check
     * @param string $decimalSeparator decimal separator, defaults to locale defined value
     * @param string $groupSeparator group/thosands separator, defaults to locale defined value
     *
     * @return float|string
     */
    public static function NUMBERVALUE($value = '', $decimalSeparator = null, $groupSeparator = null)
    {
        $value = Functions::flattenSingleValue($value);
        $decimalSeparator = Functions::flattenSingleValue($decimalSeparator);
        $groupSeparator = Functions::flattenSingleValue($groupSeparator);

        if (!is_numeric($value)) {
            $decimalSeparator = empty($decimalSeparator) ? StringHelper::getDecimalSeparator() : $decimalSeparator;
            $groupSeparator = empty($groupSeparator) ? StringHelper::getThousandsSeparator() : $groupSeparator;

            $decimalPositions = preg_match_all('/' . preg_quote($decimalSeparator) . '/', $value, $matches, PREG_OFFSET_CAPTURE);
            if ($decimalPositions > 1) {
                return Functions::VALUE();
            }
            $decimalOffset = array_pop($matches[0])[1];
            if (strpos($value, $groupSeparator, $decimalOffset) !== false) {
                return Functions::VALUE();
            }

            $value = str_replace([$groupSeparator, $decimalSeparator], ['', '.'], $value);

            // Handle the special case of trailing % signs
            $percentageString = rtrim($value, '%');
            if (!is_numeric($percentageString)) {
                return Functions::VALUE();
            }

            $percentageAdjustment = strlen($value) - strlen($percentageString);
            if ($percentageAdjustment) {
                $value = (float) $percentageString;
                $value /= pow(10, $percentageAdjustment * 2);
            }
        }

        return (float) $value;
    }

    /**
     * Compares two text strings and returns TRUE if they are exactly the same, FALSE otherwise.
     * EXACT is case-sensitive but ignores formatting differences.
     * Use EXACT to test text being entered into a document.
     *
     * @param $value1
     * @param $value2
     *
     * @return bool
     */
    public static function EXACT($value1, $value2)
    {
        $value1 = Functions::flattenSingleValue($value1);
        $value2 = Functions::flattenSingleValue($value2);

        return (string) $value2 === (string) $value1;
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
}
