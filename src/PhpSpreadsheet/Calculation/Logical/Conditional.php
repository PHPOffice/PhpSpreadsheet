<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Logical;

use PhpOffice\PhpSpreadsheet\Calculation\ArrayEnabled;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Information\Value;

class Conditional
{
    use ArrayEnabled;

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
     *              Note that this can be an array value
     * @param mixed $returnIfFalse Optional value to return when condition is false
     *              Note that this can be an array value
     *
     * @return mixed The value of returnIfTrue or returnIfFalse determined by condition
     */
    public static function statementIf($condition = true, $returnIfTrue = 0, $returnIfFalse = false)
    {
        $condition = ($condition === null) ? true : Functions::flattenSingleValue($condition);

        if (ErrorValue::isError($condition)) {
            return $condition;
        }

        $returnIfTrue = $returnIfTrue ?? 0;
        $returnIfFalse = $returnIfFalse ?? false;

        return ((bool) $condition) ? $returnIfTrue : $returnIfFalse;
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
     *              Note that these can be array values to be returned
     *         default
     *              Optional. It is the default to return if expression does not match any of the values
     *              (value1, value2, ... value_n).
     *              Note that this can be an array value to be returned
     *
     * @param mixed $arguments Statement arguments
     *
     * @return mixed The value of matched expression
     */
    public static function statementSwitch(...$arguments)
    {
        $result = ExcelError::VALUE();

        if (count($arguments) > 0) {
            $targetValue = Functions::flattenSingleValue($arguments[0]);
            $argc = count($arguments) - 1;
            $switchCount = floor($argc / 2);
            $hasDefaultClause = $argc % 2 !== 0;
            $defaultClause = $argc % 2 === 0 ? null : $arguments[$argc];

            $switchSatisfied = false;
            if ($switchCount > 0) {
                for ($index = 0; $index < $switchCount; ++$index) {
                    if ($targetValue == $arguments[$index * 2 + 1]) {
                        $result = $arguments[$index * 2 + 2];
                        $switchSatisfied = true;

                        break;
                    }
                }
            }

            if ($switchSatisfied !== true) {
                $result = $hasDefaultClause ? $defaultClause : ExcelError::NA();
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
     *                      Or can be an array of values
     * @param mixed $errorpart Value to return when testValue is an error condition
     *              Note that this can be an array value to be returned
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     *         If an array of values is passed as the $testValue argument, then the returned result will also be
     *            an array with the same dimensions
     */
    public static function IFERROR($testValue = '', $errorpart = '')
    {
        if (is_array($testValue)) {
            return self::evaluateArrayArgumentsSubset([self::class, __FUNCTION__], 1, $testValue, $errorpart);
        }

        $errorpart = $errorpart ?? '';

        return self::statementIf(ErrorValue::isError($testValue), $errorpart, $testValue);
    }

    /**
     * IFNA.
     *
     * Excel Function:
     *        =IFNA(testValue,napart)
     *
     * @param mixed $testValue Value to check, is also the value returned when not an NA
     *                      Or can be an array of values
     * @param mixed $napart Value to return when testValue is an NA condition
     *              Note that this can be an array value to be returned
     *
     * @return mixed The value of errorpart or testValue determined by error condition
     *         If an array of values is passed as the $testValue argument, then the returned result will also be
     *            an array with the same dimensions
     */
    public static function IFNA($testValue = '', $napart = '')
    {
        if (is_array($testValue)) {
            return self::evaluateArrayArgumentsSubset([self::class, __FUNCTION__], 1, $testValue, $napart);
        }

        $napart = $napart ?? '';

        return self::statementIf(ErrorValue::isNa($testValue), $napart, $testValue);
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
     *              Note that this can be an array value to be returned
     *
     * @return mixed|string The value of returnIfTrue_n, if testValue_n was true. #N/A if none of testValues was true
     */
    public static function IFS(...$arguments)
    {
        $argumentCount = count($arguments);

        if ($argumentCount % 2 != 0) {
            return ExcelError::NA();
        }
        // We use instance of Exception as a falseValue in order to prevent string collision with value in cell
        $falseValueException = new Exception();
        for ($i = 0; $i < $argumentCount; $i += 2) {
            $testValue = ($arguments[$i] === null) ? '' : Functions::flattenSingleValue($arguments[$i]);
            $returnIfTrue = ($arguments[$i + 1] === null) ? '' : $arguments[$i + 1];
            $result = self::statementIf($testValue, $returnIfTrue, $falseValueException);

            if ($result !== $falseValueException) {
                return $result;
            }
        }

        return ExcelError::NA();
    }
}
