<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class Logical
{
    /**
     * TRUE.
     *
     * Returns the boolean TRUE.
     *
     * Excel Function:
     *        =TRUE()
     *
     * @return bool True
     */
    public static function true()
    {
        return true;
    }

    /**
     * FALSE.
     *
     * Returns the boolean FALSE.
     *
     * Excel Function:
     *        =FALSE()
     *
     * @return bool False
     */
    public static function false()
    {
        return false;
    }

    private static function countTrueValues(array $args)
    {
        $returnValue = 0;

        foreach ($args as $arg) {
            // Is it a boolean value?
            if (is_bool($arg)) {
                $returnValue += $arg;
            } elseif ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ((int) $arg != 0);
            } elseif (is_string($arg)) {
                $arg = strtoupper($arg);
                if (($arg == 'TRUE') || ($arg == Calculation::getTRUE())) {
                    $arg = true;
                } elseif (($arg == 'FALSE') || ($arg == Calculation::getFALSE())) {
                    $arg = false;
                } else {
                    return Functions::VALUE();
                }
                $returnValue += ($arg != 0);
            }
        }

        return $returnValue;
    }

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
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string holds
     *            the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
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
        $argCount = count($args);

        $returnValue = self::countTrueValues($args);
        if (is_string($returnValue)) {
            return $returnValue;
        }

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
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string holds
     *            the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
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
     * i.e. the Xor function returns TRUE if an odd number of the supplied conditions evaluate to TRUE, and FALSE otherwise.
     *
     * Excel Function:
     *        =XOR(logical1[,logical2[, ...]])
     *
     *        The arguments must evaluate to logical values such as TRUE or FALSE, or the arguments must be arrays
     *            or references that contain logical values.
     *
     *        Boolean arguments are treated as True or False as appropriate
     *        Integer or floating point arguments are treated as True, except for 0 or 0.0 which are False
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string holds
     *            the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
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
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string holds
     *            the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @param mixed $logical A value or expression that can be evaluated to TRUE or FALSE
     *
     * @return bool|string the boolean inverse of the argument
     */
    public static function NOT($logical = false)
    {
        $logical = Functions::flattenSingleValue($logical);

        if (is_string($logical)) {
            $logical = strtoupper($logical);
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
     * STATEMENT_IF.
     *
     * Returns one value if a condition you specify evaluates to TRUE and another value if it evaluates to FALSE.
     *
     * Excel Function:
     *        =IF(condition[,returnIfTrue[,returnIfFalse]])
     *
     *        Condition is any value or expression that can be evaluated to TRUE or FALSE.
     *            For example, A10=100 is a logical expression; if the value in cell A10 is equal to 100,
     *            the expression evaluates to TRUE. Otherwise, the expression evaluates to FALSE.
     *            This argument can use any comparison calculation operator.
     *        ReturnIfTrue is the value that is returned if condition evaluates to TRUE.
     *            For example, if this argument is the text string "Within budget" and the condition argument evaluates to TRUE,
     *            then the IF function returns the text "Within budget"
     *            If condition is TRUE and ReturnIfTrue is blank, this argument returns 0 (zero). To display the word TRUE, use
     *            the logical value TRUE for this argument.
     *            ReturnIfTrue can be another formula.
     *        ReturnIfFalse is the value that is returned if condition evaluates to FALSE.
     *            For example, if this argument is the text string "Over budget" and the condition argument evaluates to FALSE,
     *            then the IF function returns the text "Over budget".
     *            If condition is FALSE and ReturnIfFalse is omitted, then the logical value FALSE is returned.
     *            If condition is FALSE and ReturnIfFalse is blank, then the value 0 (zero) is returned.
     *            ReturnIfFalse can be another formula.
     *
     * @param mixed $condition Condition to evaluate
     * @param mixed $returnIfTrue Value to return when condition is true
     * @param mixed $returnIfFalse Optional value to return when condition is false
     *
     * @return mixed The value of returnIfTrue or returnIfFalse determined by condition
     */
    public static function statementIf($condition = true, $returnIfTrue = 0, $returnIfFalse = false)
    {
        if (Functions::isError($condition)) {
            return $condition;
        }

        $condition = ($condition === null) ? true : (bool) Functions::flattenSingleValue($condition);
        $returnIfTrue = ($returnIfTrue === null) ? 0 : Functions::flattenSingleValue($returnIfTrue);
        $returnIfFalse = ($returnIfFalse === null) ? false : Functions::flattenSingleValue($returnIfFalse);

        return ($condition) ? $returnIfTrue : $returnIfFalse;
    }

    /**
     * STATEMENT_SWITCH.
     *
     * Returns corresponding with first match (any data type such as a string, numeric, date, etc).
     *
     * Excel Function:
     *        =SWITCH (expression, value1, result1, value2, result2, ... value_n, result_n [, default])
     *
     *        Expression
     *              The expression to compare to a list of values.
     *        value1, value2, ... value_n
     *              A list of values that are compared to expression. The SWITCH function is looking for the first value that matches the expression.
     *        result1, result2, ... result_n
     *              A list of results. The SWITCH function returns the corresponding result when a value matches expression.
     *         default
     *              Optional. It is the default to return if expression does not match any of the values (value1, value2, ... value_n).
     *
     * @param mixed $arguments Statement arguments
     *
     * @return mixed The value of matched expression
     */
    public static function statementSwitch(...$arguments)
    {
        $result = Functions::VALUE();

        if (count($arguments) > 0) {
            $targetValue = Functions::flattenSingleValue($arguments[0]);
            $argc = count($arguments) - 1;
            $switchCount = floor($argc / 2);
            $switchSatisfied = false;
            $hasDefaultClause = $argc % 2 !== 0;
            $defaultClause = $argc % 2 === 0 ? null : $arguments[count($arguments) - 1];

            if ($switchCount) {
                for ($index = 0; $index < $switchCount; ++$index) {
                    if ($targetValue == $arguments[$index * 2 + 1]) {
                        $result = $arguments[$index * 2 + 2];
                        $switchSatisfied = true;

                        break;
                    }
                }
            }

            if (!$switchSatisfied) {
                $result = $hasDefaultClause ? $defaultClause : Functions::NA();
            }
        }

        return $result;
    }

    /**
     * IFERROR.
     *
     * Excel Function:
     *        =IFERROR(testValue,errorpart)
     *
     * @param mixed $testValue Value to check, is also the value returned when no error
     * @param mixed $errorpart Value to return when testValue is an error condition
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     */
    public static function IFERROR($testValue = '', $errorpart = '')
    {
        $testValue = ($testValue === null) ? '' : Functions::flattenSingleValue($testValue);
        $errorpart = ($errorpart === null) ? '' : Functions::flattenSingleValue($errorpart);

        return self::statementIf(Functions::isError($testValue), $errorpart, $testValue);
    }

    /**
     * IFNA.
     *
     * Excel Function:
     *        =IFNA(testValue,napart)
     *
     * @param mixed $testValue Value to check, is also the value returned when not an NA
     * @param mixed $napart Value to return when testValue is an NA condition
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     */
    public static function IFNA($testValue = '', $napart = '')
    {
        $testValue = ($testValue === null) ? '' : Functions::flattenSingleValue($testValue);
        $napart = ($napart === null) ? '' : Functions::flattenSingleValue($napart);

        return self::statementIf(Functions::isNa($testValue), $napart, $testValue);
    }

    /**
     * IFS.
     *
     * Excel Function:
     *         =IFS(testValue1;returnIfTrue1;testValue2;returnIfTrue2;...;testValue_n;returnIfTrue_n)
     *
     *         testValue1 ... testValue_n
     *             Conditions to Evaluate
     *         returnIfTrue1 ... returnIfTrue_n
     *             Value returned if corresponding testValue (nth) was true
     *
     * @param mixed ...$arguments Statement arguments
     *
     * @return mixed|string The value of returnIfTrue_n, if testValue_n was true. #N/A if none of testValues was true
     */
    public static function IFS(...$arguments)
    {
        if (count($arguments) % 2 != 0) {
            return Functions::NA();
        }
        // We use instance of Exception as a falseValue in order to prevent string collision with value in cell
        $falseValueException = new Exception();
        for ($i = 0; $i < count($arguments); $i += 2) {
            $testValue = ($arguments[$i] === null) ? '' : Functions::flattenSingleValue($arguments[$i]);
            $returnIfTrue = ($arguments[$i + 1] === null) ? '' : Functions::flattenSingleValue($arguments[$i + 1]);
            $result = self::statementIf($testValue, $returnIfTrue, $falseValueException);

            if ($result !== $falseValueException) {
                return $result;
            }
        }

        return Functions::NA();
    }
}
