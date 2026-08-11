<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class Operations
{
    use ArrayEnabled;

    /**
     * LOGICAL_AND.
     *
     * Returns boolean TRUE if all its arguments are TRUE; returns FALSE if one or more argument is FALSE.
     *
     * Excel Function:
     *        =AND(logical1[,logical2[, ...]])
     *
     *        The arguments must evaluate to logical values such as TRUE or FALSE, or the arguments must be arrays
     *            or references that contain logical values.
     *
     *        Boolean arguments are treated as True or False as appropriate
     *        Integer or floating point arguments are treated as True, except for 0 or 0.0 which are False
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string
     *            holds the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @param mixed ...$args Data values
     *
     * @return bool|string the logical AND of the arguments
     */
    public static function logicalAnd(mixed ...$args)
    {
        return self::countTrueValues($args, fn (int $trueValueCount, int $count): bool => $trueValueCount === $count);
    }

    /**
     * LOGICAL_OR.
     *
     * Returns boolean TRUE if any argument is TRUE; returns FALSE if all arguments are FALSE.
     *
     * Excel Function:
     *        =OR(logical1[,logical2[, ...]])
     *
     *        The arguments must evaluate to logical values such as TRUE or FALSE, or the arguments must be arrays
     *            or references that contain logical values.
     *
     *        Boolean arguments are treated as True or False as appropriate
     *        Integer or floating point arguments are treated as True, except for 0 or 0.0 which are False
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string
     *            holds the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @param mixed $args Data values
     *
     * @return bool|string the logical OR of the arguments
     */
    public static function logicalOr(mixed ...$args)
    {
        return self::countTrueValues($args, fn (int $trueValueCount): bool => $trueValueCount > 0);
    }

    /**
     * LOGICAL_XOR.
     *
     * Returns the Exclusive Or logical operation for one or more supplied conditions.
     * i.e. the Xor function returns TRUE if an odd number of the supplied conditions evaluate to TRUE,
     *      and FALSE otherwise.
     *
     * Excel Function:
     *        =XOR(logical1[,logical2[, ...]])
     *
     *        The arguments must evaluate to logical values such as TRUE or FALSE, or the arguments must be arrays
     *            or references that contain logical values.
     *
     *        Boolean arguments are treated as True or False as appropriate
     *        Integer or floating point arguments are treated as True, except for 0 or 0.0 which are False
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string
     *            holds the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @param mixed $args Data values
     *
     * @return bool|string the logical XOR of the arguments
     */
    public static function logicalXor(mixed ...$args)
    {
        return self::countTrueValues($args, fn (int $trueValueCount): bool => $trueValueCount % 2 === 1);
    }

    /**
     * NOT.
     *
     * Returns the boolean inverse of the argument.
     *
     * Excel Function:
     *        =NOT(logical)
     *
     *        The argument must evaluate to a logical value such as TRUE or FALSE
     *
     *        Boolean arguments are treated as True or False as appropriate
     *        Integer or floating point arguments are treated as True, except for 0 or 0.0 which are False
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string
     *            holds the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @param mixed $logical A value or expression that can be evaluated to TRUE or FALSE
     *                      Or can be an array of values
     *
     * @return array<mixed>|bool|string the boolean inverse of the argument
     *         If an array of values is passed as an argument, then the returned result will also be an array
     *            with the same dimensions
     */
    public static function NOT(mixed $logical = false): array|bool|string
    {
        if (is_array($logical)) {
            return self::evaluateSingleArgumentArray([self::class, __FUNCTION__], $logical);
        }

        if (is_string($logical)) {
            $logical = mb_strtoupper($logical, 'UTF-8');
            if (($logical == 'TRUE') || ($logical == Calculation::getTRUE())) {
                return false;
            } elseif (($logical == 'FALSE') || ($logical == Calculation::getFALSE())) {
                return true;
            }

            return ExcelError::VALUE();
        }

        return !$logical;
    }

    /**
     * @param mixed[] $args
     * @param callable(int, int): bool $func
     */
    private static function countTrueValues(array $args, callable $func): bool|string
    {
        $trueValueCount = 0;
        $count = 0;

        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            ++$count;
            // Is it a boolean value?
            if (is_bool($arg)) {
                $trueValueCount += $arg;
            } elseif (is_string($arg)) {
                $isLiteral = !Functions::isCellValue($k);
                $arg = mb_strtoupper($arg, 'UTF-8');
                if ($isLiteral && ($arg == 'TRUE' || $arg == Calculation::getTRUE())) {
                    ++$trueValueCount;
                } elseif ($isLiteral && ($arg == 'FALSE' || $arg == Calculation::getFALSE())) {
                    //$trueValueCount += 0;
                } else {
                    --$count;
                }
            } elseif (is_int($arg) || is_float($arg)) {
                $trueValueCount += (int) ($arg != 0);
            } else {
                --$count;
            }
        }

        return ($count === 0) ? ExcelError::VALUE() : $func($trueValueCount, $count);
    }
}
