<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Format
{
    /**
     * DOLLAR.
     *
     * This function converts a number to text using currency format, with the decimals rounded to the specified place.
     * The format used is $#,##0.00_);($#,##0.00)..
     *
     * @param mixed $value The value to format
     * @param mixed $decimals The number of digits to display to the right of the decimal point (as an integer).
     *                            If decimals is negative, number is rounded to the left of the decimal point.
     *                            If you omit decimals, it is assumed to be 2
     */
    public static function DOLLAR($value = 0, $decimals = 2): string
    {
        $value = Functions::flattenSingleValue($value);
        $decimals = $decimals === null ? 2 : Functions::flattenSingleValue($decimals);

        // Validate parameters
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return Functions::VALUE();
        }
        $decimals = (int) $decimals;

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
     * FIXEDFORMAT.
     *
     * @param mixed $value The value to format
     * @param mixed $decimals Integer value for the number of decimal places that should be formatted
     * @param mixed $noCommas Boolean value indicating whether the value should have thousands separators or not
     */
    public static function FIXEDFORMAT($value, $decimals = 2, $noCommas = false): string
    {
        $value = Functions::flattenSingleValue($value);
        $decimals = $decimals === null ? 2 : Functions::flattenSingleValue($decimals);
        $noCommas = Functions::flattenSingleValue($noCommas);

        // Validate parameters
        if (!is_numeric($value) || !is_numeric($decimals)) {
            return Functions::VALUE();
        }
        $decimals = (float) $decimals;
        $value = (float) $value;
        $decimals = (int) floor($decimals);

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
     * TEXTFORMAT.
     *
     * @param mixed $value The value to format
     * @param mixed $format A string with the Format mask that should be used
     */
    public static function TEXTFORMAT($value, $format): string
    {
        $value = Functions::flattenSingleValue($value);
        $format = Functions::flattenSingleValue($format);

        if ((is_string($value)) && (!is_numeric($value)) && Date::isDateTimeFormatCode($format)) {
            $value = DateTimeExcel\DateValue::fromString($value);
        }

        return (string) NumberFormat::toFormattedString($value, $format);
    }

    /**
     * VALUE.
     *
     * @param mixed $value Value to check
     *
     * @return DateTimeInterface|float|int|string A string if arguments are invalid
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
                $timeValue = DateTimeExcel\TimeValue::fromString($value);
                if ($timeValue !== Functions::VALUE()) {
                    Functions::setReturnDateType($dateSetting);

                    return $timeValue;
                }
            }
            $dateValue = DateTimeExcel\DateValue::fromString($value);
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
     * @param mixed $value The value to format
     * @param mixed $decimalSeparator A string with the decimal separator to use, defaults to locale defined value
     * @param mixed $groupSeparator A string with the group/thousands separator to use, defaults to locale defined value
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
                $value /= 10 ** ($percentageAdjustment * 2);
            }
        }

        return (float) $value;
    }
}
