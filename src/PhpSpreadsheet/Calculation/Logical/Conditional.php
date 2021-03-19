<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class Conditional
{
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
     *            For example, if this argument is the text string "Within budget" and
     *                the condition argument evaluates to TRUE, then the IF function returns the text "Within budget"
     *            If condition is TRUE and ReturnIfTrue is blank, this argument returns 0 (zero).
     *            To display the word TRUE, use the logical value TRUE for this argument.
     *            ReturnIfTrue can be another formula.
     *        ReturnIfFalse is the value that is returned if condition evaluates to FALSE.
     *            For example, if this argument is the text string "Over budget" and the condition argument evaluates
     *                to FALSE, then the IF function returns the text "Over budget".
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
     *              A list of values that are compared to expression.
     *              The SWITCH function is looking for the first value that matches the expression.
     *        result1, result2, ... result_n
     *              A list of results. The SWITCH function returns the corresponding result when a value
     *              matches expression.
     *         default
     *              Optional. It is the default to return if expression does not match any of the values
     *              (value1, value2, ... value_n).
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
