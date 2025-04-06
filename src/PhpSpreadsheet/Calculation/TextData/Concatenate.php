<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class Concatenate
{
    use ArrayEnabled;

    /**
     * This implements the CONCAT function, *not* CONCATENATE.
     *
     * @param array $args
     */
    public static function CONCATENATE(...$args): string
    {
        $returnValue = '';

        // Loop through arguments
        $aArgs = Functions::flattenArray($args);

        foreach ($aArgs as $arg) {
            $value = Helpers::extractString($arg);
            if (ErrorValue::isError($value, true)) {
                $returnValue = $value;

                break;
            }
            $returnValue .= Helpers::extractString($arg);
            if (StringHelper::countCharacters($returnValue) > DataType::MAX_STRING_LENGTH) {
                $returnValue = ExcelError::CALC();

                break;
            }
        }

        return $returnValue;
    }

    /**
     * This implements the CONCATENATE function.
     *
     * @param array $args data to be concatenated
     */
    public static function actualCONCATENATE(...$args): array|string
    {
        if (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_GNUMERIC) {
            return self::CONCATENATE(...$args);
        }
        $result = '';
        foreach ($args as $operand2) {
            $result = self::concatenate2Args($result, $operand2);
            if (ErrorValue::isError($result, true) === true) {
                break;
            }
        }

        return $result;
    }

    private static function concatenate2Args(array|string $operand1, null|array|bool|float|int|string $operand2): array|string
    {
        if (is_array($operand1) || is_array($operand2)) {
            $operand1 = Calculation::boolToString($operand1);
            $operand2 = Calculation::boolToString($operand2);
            [$rows, $columns] = Calculation::checkMatrixOperands($operand1, $operand2, 2);
            $errorFound = false;
            for ($row = 0; $row < $rows && !$errorFound; ++$row) {
                for ($column = 0; $column < $columns; ++$column) {
                    if (ErrorValue::isError($operand2[$row][$column])) {
                        return $operand2[$row][$column];
                    }
                    $operand1[$row][$column]
                        = StringHelper::convertToString($operand1[$row][$column], convertBool: true)
                        . StringHelper::convertToString($operand2[$row][$column], convertBool: true);
                    if (mb_strlen($operand1[$row][$column]) > DataType::MAX_STRING_LENGTH) {
                        $operand1 = ExcelError::CALC();
                        $errorFound = true;

                        break;
                    }
                }
            }
        } elseif (ErrorValue::isError($operand2, true) === true) {
            $operand1 = (string) $operand2;
        } else {
            $operand1 .= StringHelper::convertToString($operand2, convertBool: true);
            if (mb_strlen($operand1) > DataType::MAX_STRING_LENGTH) {
                $operand1 = ExcelError::CALC();
            }
        }

        return $operand1;
    }

    /**
     * TEXTJOIN.
     *
     * @param null|string|string[] $delimiter The delimiter to use between the joined arguments
     *                         Or can be an array of values
     * @param null|bool|bool[] $ignoreEmpty true/false Flag indicating whether empty arguments should be skipped
     *                         Or can be an array of values
     * @param mixed $args The values to join
     *
     * @return array|string The joined string
     *         If an array of values is passed for the $delimiter or $ignoreEmpty arguments, then the returned result
     *            will also be an array with matching dimensions
     */
    public static function TEXTJOIN($delimiter = '', $ignoreEmpty = true, mixed ...$args): array|string
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

        $delimiter ??= '';
        $ignoreEmpty ??= true;
        /** @var array */
        $aArgs = Functions::flattenArray($args);
        $returnValue = self::evaluateTextJoinArray($ignoreEmpty, $aArgs);

        $returnValue ??= implode($delimiter, $aArgs);
        if (StringHelper::countCharacters($returnValue) > DataType::MAX_STRING_LENGTH) {
            $returnValue = ExcelError::CALC();
        }

        return $returnValue;
    }

    private static function evaluateTextJoinArray(bool $ignoreEmpty, array &$aArgs): ?string
    {
        foreach ($aArgs as $key => &$arg) {
            $value = Helpers::extractString($arg);
            if (ErrorValue::isError($value, true)) {
                return $value;
            }

            if ($ignoreEmpty === true && ((is_string($arg) && trim($arg) === '') || $arg === null)) {
                unset($aArgs[$key]);
            } elseif (is_bool($arg)) {
                $arg = Helpers::convertBooleanValue($arg);
            }
        }

        return null;
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
    public static function builtinREPT(mixed $stringValue, mixed $repeatCount): array|string
    {
        if (is_array($stringValue) || is_array($repeatCount)) {
            return self::evaluateArrayArguments([self::class, __FUNCTION__], $stringValue, $repeatCount);
        }

        $stringValue = Helpers::extractString($stringValue);

        if (!is_numeric($repeatCount) || $repeatCount < 0) {
            $returnValue = ExcelError::VALUE();
        } elseif (ErrorValue::isError($stringValue, true)) {
            $returnValue = $stringValue;
        } else {
            $returnValue = str_repeat($stringValue, (int) $repeatCount);
            if (StringHelper::countCharacters($returnValue) > DataType::MAX_STRING_LENGTH) {
                $returnValue = ExcelError::VALUE(); // note VALUE not CALC
            }
        }

        return $returnValue;
    }
}
