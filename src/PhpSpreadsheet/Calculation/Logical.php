<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

/**
 * @deprecated 1.17.0
 */
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
     * @deprecated 1.17.0
     * Use the TRUE() method in the Logical\Boolean class instead
     * @see Logical\Boolean::TRUE()
     *
     * @return bool True
     */
    public static function true(): bool
    {
        return Boolean::true();
    }

    /**
     * FALSE.
     *
     * Returns the boolean FALSE.
     *
     * Excel Function:
     *        =FALSE()
     *
     * @deprecated 1.17.0
     * Use the FALSE() method in the Logical\Boolean class instead
     * @see Logical\Boolean::FALSE()
     *
     * @return bool False
     */
    public static function false(): bool
    {
        return Boolean::false();
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
     *        If any argument value is a string, or a Null, the function returns a #VALUE! error, unless the string
     *            holds the value TRUE or FALSE, in which case it is evaluated as the corresponding boolean value
     *
     * @deprecated 1.17.0
     * Use the logicalAnd() method in the Logical\Operations class instead
     * @see Logical\Operations::logicalAnd()
     *
     * @param mixed ...$args Data values
     *
     * @return bool|string the logical AND of the arguments
     */
    public static function logicalAnd(mixed ...$args)
    {
        return Logical\Operations::logicalAnd(...$args);
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
     * @deprecated 1.17.0
     * Use the logicalOr() method in the Logical\Operations class instead
     * @see Logical\Operations::logicalOr()
     *
     * @param mixed $args Data values
     *
     * @return bool|string the logical OR of the arguments
     */
    public static function logicalOr(mixed ...$args)
    {
        return Logical\Operations::logicalOr(...$args);
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
     * @deprecated 1.17.0
     * Use the logicalXor() method in the Logical\Operations class instead
     * @see Logical\Operations::logicalXor()
     *
     * @param mixed $args Data values
     *
     * @return bool|string the logical XOR of the arguments
     */
    public static function logicalXor(mixed ...$args)
    {
        return Logical\Operations::logicalXor(...$args);
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
     * @deprecated 1.17.0
     * Use the NOT() method in the Logical\Operations class instead
     * @see Logical\Operations::NOT()
     *
     * @param mixed $logical A value or expression that can be evaluated to TRUE or FALSE
     *
     * @return array|bool|string the boolean inverse of the argument
     */
    public static function NOT(mixed $logical = false): bool|string|array
    {
        return Logical\Operations::NOT($logical);
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
     *            For example, if this argument is the text string "Within budget" and the condition argument
     *                evaluates to TRUE, then the IF function returns the text "Within budget"
     *            If condition is TRUE and ReturnIfTrue is blank, this argument returns 0 (zero).
     *               To display the word TRUE, use the logical value TRUE for this argument.
     *            ReturnIfTrue can be another formula.
     *        ReturnIfFalse is the value that is returned if condition evaluates to FALSE.
     *            For example, if this argument is the text string "Over budget" and the condition argument
     *                evaluates to FALSE, then the IF function returns the text "Over budget".
     *            If condition is FALSE and ReturnIfFalse is omitted, then the logical value FALSE is returned.
     *            If condition is FALSE and ReturnIfFalse is blank, then the value 0 (zero) is returned.
     *            ReturnIfFalse can be another formula.
     *
     * @deprecated 1.17.0
     * Use the statementIf() method in the Logical\Conditional class instead
     * @see Logical\Conditional::statementIf()
     *
     * @param mixed $condition Condition to evaluate
     * @param mixed $returnIfTrue Value to return when condition is true
     * @param mixed $returnIfFalse Optional value to return when condition is false
     *
     * @return mixed The value of returnIfTrue or returnIfFalse determined by condition
     */
    public static function statementIf(mixed $condition = true, mixed $returnIfTrue = 0, mixed $returnIfFalse = false)
    {
        return Logical\Conditional::statementIf($condition, $returnIfTrue, $returnIfFalse);
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
     *              A list of values that are compared to expression.
     *              The SWITCH function is looking for the first value that matches the expression.
     *        result1, result2, ... result_n
     *              A list of results. The SWITCH function returns the corresponding result when a value
     *              matches expression.
     *         default
     *              Optional. It is the default to return if expression does not match any of the values
     *              (value1, value2, ... value_n).
     *
     * @deprecated 1.17.0
     * Use the statementSwitch() method in the Logical\Conditional class instead
     * @see Logical\Conditional::statementSwitch()
     *
     * @param mixed $arguments Statement arguments
     *
     * @return mixed The value of matched expression
     */
    public static function statementSwitch(mixed ...$arguments)
    {
        return Logical\Conditional::statementSwitch(...$arguments);
    }

    /**
     * IFERROR.
     *
     * Excel Function:
     *        =IFERROR(testValue,errorpart)
     *
     * @deprecated 1.17.0
     * Use the IFERROR() method in the Logical\Conditional class instead
     * @see Logical\Conditional::IFERROR()
     *
     * @param mixed $testValue Value to check, is also the value returned when no error
     * @param mixed $errorpart Value to return when testValue is an error condition
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     */
    public static function IFERROR(mixed $testValue = '', mixed $errorpart = '')
    {
        return Logical\Conditional::IFERROR($testValue, $errorpart);
    }

    /**
     * IFNA.
     *
     * Excel Function:
     *        =IFNA(testValue,napart)
     *
     * @deprecated 1.17.0
     * Use the IFNA() method in the Logical\Conditional class instead
     * @see Logical\Conditional::IFNA()
     *
     * @param mixed $testValue Value to check, is also the value returned when not an NA
     * @param mixed $napart Value to return when testValue is an NA condition
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     */
    public static function IFNA(mixed $testValue = '', mixed $napart = '')
    {
        return Logical\Conditional::IFNA($testValue, $napart);
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
     * @deprecated 1.17.0
     * Use the IFS() method in the Logical\Conditional class instead
     * @see Logical\Conditional::IFS()
     *
     * @param mixed ...$arguments Statement arguments
     *
     * @return mixed|string The value of returnIfTrue_n, if testValue_n was true. #N/A if none of testValues was true
     */
    public static function IFS(mixed ...$arguments)
    {
        return Logical\Conditional::IFS(...$arguments);
    }
}
