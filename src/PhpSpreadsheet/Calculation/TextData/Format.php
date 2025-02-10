<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use Composer\Pcre\Preg;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Format
{
    use ArrayEnabled;

    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @param mixed $value The value to format
     *                         Or can be an array of values
     * @param mixed $decimals The number of digits to display to the right of the decimal point (as an integer).
     *                            If decimals is negative, number is rounded to the left of the decimal point.
     *                            If you omit decimals, it is assumed to be 2
     *                         Or can be an array of values
     *
     * @return array|string
     *         If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function DOLLAR($value = 0, $decimals = 2)
    {
        if (is_array($value) || is_array($decimals)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $decimals);
        }

        try {
            $value = Helpers::extractFloat($value);
            $decimals = Helpers::extractInt($decimals, -100, 0, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        $mask = '$#,##0';
        if ($decimals > 0) {
            $mask .= '.' . str_repeat('0', $decimals);
        } else {
            $round = 10 ** abs($decimals);
            if ($value < 0) {
                $round = 0 - $round;
            }
            $value = MathTrig\Round::multiple($value, $round);
        }
        $mask = "{$mask};-{$mask}";

        return NumberFormat::toFormattedString($value, $mask);
    }

    /**
     * FIXED.
     *
     * @param mixed $value The value to format
     *                         Or can be an array of values
     * @param mixed $decimals Integer value for the number of decimal places that should be formatted
     *                         Or can be an array of values
     * @param mixed $noCommas Boolean value indicating whether the value should have thousands separators or not
     *                         Or can be an array of values
     *
     * @return array|string
     *         If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function FIXEDFORMAT($value, $decimals = 2, $noCommas = false)
    {
        if (is_array($value) || is_array($decimals) || is_array($noCommas)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $decimals, $noCommas);
        }

        try {
            $value = Helpers::extractFloat($value);
            $decimals = Helpers::extractInt($decimals, -100, 0, true);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        $valueResult = round($value, $decimals);
        if ($decimals < 0) {
            $decimals = 0;
        }
        if ($noCommas === false) {
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
     * TEXT.
     *
     * @param mixed $value The value to format
     *                         Or can be an array of values
     * @param mixed $format A string with the Format mask that should be used
     *                         Or can be an array of values
     *
     * @return array|string
     *         If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function TEXTFORMAT($value, $format)
    {
        if (is_array($value) || is_array($format)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $format);
        }

        $value = Helpers::extractString($value);
        $format = Helpers::extractString($format);

        if (!is_numeric($value) && Date::isDateTimeFormatCode($format) && !Preg::isMatch('/^\s*\d+(\s+\d+)+\s*$/', $value)) {
            $value1 = DateTimeExcel\DateValue::fromString($value);
            $value2 = DateTimeExcel\TimeValue::fromString($value);
            /** @var float|int|string */
            $value = (is_numeric($value1) && is_numeric($value2)) ? ($value1 + $value2) : (is_numeric($value1) ? $value1 : (is_numeric($value2) ? $value2 : $value));
        }

        return (string) NumberFormat::toFormattedString($value, $format);
    }

    /**
     * @param mixed $value Value to check
     *
     * @return mixed
     */
    private static function convertValue($value, bool $spacesMeanZero = false)
    {
        $value = $value ?? 0;
        if (is_bool($value)) {
            if (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE) {
                $value = (int) $value;
            } else {
                throw new CalcExp(ExcelError::VALUE());
            }
        }
        if (is_string($value)) {
            $value = trim($value);
            if ($spacesMeanZero && $value === '') {
                $value = 0;
            }
        }

        return $value;
    }

    /**
     * VALUE.
     *
     * @param mixed $value Value to check
     *                         Or can be an array of values
     *
     * @return array|DateTimeInterface|float|int|string A string if arguments are invalid
     *         If an array of values is passed for the argument, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function VALUE($value = '')
    {
        if (is_array($value)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $value);
        }

        try {
            $value = self::convertValue($value);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }
        if (!is_numeric($value)) {
            $numberValue = str_replace(
                StringHelper::getThousandsSeparator(),
                '',
                trim($value, " \t\n\r\0\x0B" . StringHelper::getCurrencyCode())
            );
            if ($numberValue === '') {
                return ExcelError::VALUE();
            }
            if (is_numeric($numberValue)) {
                return (float) $numberValue;
            }

            $dateSetting = Functions::getReturnDateType();
            Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);

            if (strpos($value, ':') !== false) {
                $timeValue = Functions::scalar(DateTimeExcel\TimeValue::fromString($value));
                if ($timeValue !== ExcelError::VALUE()) {
                    Functions::setReturnDateType($dateSetting);

                    return $timeValue;
                }
            }
            $dateValue = Functions::scalar(DateTimeExcel\DateValue::fromString($value));
            if ($dateValue !== ExcelError::VALUE()) {
                Functions::setReturnDateType($dateSetting);

                return $dateValue;
            }
            Functions::setReturnDateType($dateSetting);

            return ExcelError::VALUE();
        }

        return (float) $value;
    }

    /**
     * TEXT.
     *
     * @param mixed $value The value to format
     *                         Or can be an array of values
     * @param mixed $format
     *
     * @return array|string
     *         If an array of values is passed for either of the arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function valueToText($value, $format = false)
    {
        if (is_array($value) || is_array($format)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $format);
        }

        $format = (bool) $format;

        if (is_object($value) && $value instanceof RichText) {
            $value = $value->getPlainText();
        }
        if (is_string($value)) {
            $value = ($format === true) ? Calculation::wrapResult($value) : $value;
            $value = str_replace("\n", '', $value);
        } elseif (is_bool($value)) {
            $value = Calculation::getLocaleBoolean($value ? 'TRUE' : 'FALSE');
        }

        return (string) $value;
    }

    /**
     * @param mixed $decimalSeparator
     */
    private static function getDecimalSeparator($decimalSeparator): string
    {
        return empty($decimalSeparator) ? StringHelper::getDecimalSeparator() : (string) $decimalSeparator;
    }

    /**
     * @param mixed $groupSeparator
     */
    private static function getGroupSeparator($groupSeparator): string
    {
        return empty($groupSeparator) ? StringHelper::getThousandsSeparator() : (string) $groupSeparator;
    }

    /**
     * NUMBERVALUE.
     *
     * @param mixed $value The value to format
     *                         Or can be an array of values
     * @param mixed $decimalSeparator A string with the decimal separator to use, defaults to locale defined value
     *                         Or can be an array of values
     * @param mixed $groupSeparator A string with the group/thousands separator to use, defaults to locale defined value
     *                         Or can be an array of values
     *
     * @return array|float|string
     */
    public static function NUMBERVALUE($value = '', $decimalSeparator = null, $groupSeparator = null)
    {
        if (is_array($value) || is_array($decimalSeparator) || is_array($groupSeparator)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $value, $decimalSeparator, $groupSeparator);
        }

        try {
            $value = self::convertValue($value, true);
            $decimalSeparator = self::getDecimalSeparator($decimalSeparator);
            $groupSeparator = self::getGroupSeparator($groupSeparator);
        } catch (CalcExp $e) {
            return $e->getMessage();
        }

        if (!is_numeric($value)) {
            $decimalPositions = preg_match_all('/' . preg_quote($decimalSeparator, '/') . '/', $value, $matches, PREG_OFFSET_CAPTURE);
            if ($decimalPositions > 1) {
                return ExcelError::VALUE();
            }
            $decimalOffset = array_pop($matches[0])[1] ?? null;
            if ($decimalOffset === null || strpos($value, $groupSeparator, $decimalOffset) !== false) {
                return ExcelError::VALUE();
            }

            $value = str_replace([$groupSeparator, $decimalSeparator], ['', '.'], $value);

            // Handle the special case of trailing % signs
            $percentageString = rtrim($value, '%');
            if (!is_numeric($percentageString)) {
                return ExcelError::VALUE();
            }

            $percentageAdjustment = strlen($value) - strlen($percentageString);
            if ($percentageAdjustment) {
                $value = (float) $percentageString;
                $value /= 10 ** ($percentageAdjustment * 2);
            }
        }

        return is_array($value) ? ExcelError::VALUE() : (float) $value;
    }
}
