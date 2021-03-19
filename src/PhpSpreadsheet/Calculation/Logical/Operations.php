<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Operations
{
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
    public static function logicalAnd(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }
        $argCount = count($args);

        return ($returnValue > 0) && ($returnValue == $argCount);
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
    public static function logicalOr(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

        return $returnValue > 0;
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
    public static function logicalXor(...$args)
    {
        $args = Functions::flattenArray($args);

        if (count($args) == 0) {
            return Functions::VALUE();
        }

        $args = array_filter($args, function ($value) {
            return $value !== null || (is_string($value) && trim($value) == '');
        });

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

        return $returnValue % 2 == 1;
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
     *
     * @return bool|string the boolean inverse of the argument
     */
    public static function NOT($logical = false)
    {
        $logical = Functions::flattenSingleValue($logical);

        if (is_string($logical)) {
            $logical = mb_strtoupper($logical, 'UTF-8');
            if (($logical == 'TRUE') || ($logical == Calculation::getTRUE())) {
                return false;
            } elseif (($logical == 'FALSE') || ($logical == Calculation::getFALSE())) {
                return true;
            }

            return Functions::VALUE();
        }

        return !$logical;
    }

    /**
     * @return int|string
     */
    private static function countTrueValues(array $args)
    {
        $trueValueCount = 0;

        foreach ($args as $arg) {
            // Is it a boolean value?
            if (is_bool($arg)) {
                $trueValueCount += $arg;
            } elseif ((is_numeric($arg)) && (!is_string($arg))) {
                $trueValueCount += ((int) $arg != 0);
            } elseif (is_string($arg)) {
                $arg = mb_strtoupper($arg, 'UTF-8');
                if (($arg == 'TRUE') || ($arg == Calculation::getTRUE())) {
                    $arg = true;
                } elseif (($arg == 'FALSE') || ($arg == Calculation::getFALSE())) {
                    $arg = false;
                } else {
                    return Functions::VALUE();
                }
                $trueValueCount += ($arg != 0);
            }
        }

        return $trueValueCount;
    }
}
