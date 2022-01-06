<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

class CellMatcher
{
    const COMPARISON_OPERATORS = [
        Conditional::OPERATOR_EQUAL => '=',
        Conditional::OPERATOR_GREATERTHAN => '>',
        Conditional::OPERATOR_GREATERTHANOREQUAL => '>=',
        Conditional::OPERATOR_LESSTHAN => '<',
        Conditional::OPERATOR_LESSTHANOREQUAL => '<=',
        Conditional::OPERATOR_NOTEQUAL => '<>',
    ];

    const COMPARISON_RANGE_OPERATORS = [
        Conditional::OPERATOR_BETWEEN => 'IF(AND(A1>=%s,A1<=%s),TRUE,FALSE)',
        Conditional::OPERATOR_NOTBETWEEN => 'IF(AND(A1>=%s,A1<=%s),FALSE,TRUE)',
    ];

    /**
     * @var Cell $cell
     */
    protected $cell;

    /**
     * @var int $cellRow
     */
    protected $cellRow;

    /**
     * @var int $cellColumn
     */
    protected $cellColumn;

    /**
     * @var string $referenceCell
     */
    protected $referenceCell;

    /**
     * @var int $referenceRow
     */
    protected $referenceRow;

    /**
     * @var int $referenceColumn
     */
    protected $referenceColumn;

    /**
     * @var Calculation $engine
     */
    protected $engine;

    public function __construct(Cell $cell, string $conditionalRange)
    {
        $this->cell = $cell;
        [$this->cellColumn, $this->cellRow] = Coordinate::indexesFromString($this->cell->getCoordinate());
        $this->setReferenceCellForExpressions($conditionalRange);

        $this->engine = Calculation::getInstance($cell->getWorksheet()->getParent());
    }

    protected function setReferenceCellForExpressions(string $conditionalRange)
    {
        $conditionalRange = Coordinate::splitRange(str_replace('$', '', $conditionalRange));
        [$this->referenceCell] = $conditionalRange[0];

        [$this->referenceColumn, $this->referenceRow] = Coordinate::indexesFromString($this->referenceCell);
    }

    public function evaluateConditional(Conditional $conditional): bool
    {
        switch ($conditional->getConditionType()) {
            case Conditional::CONDITION_CELLIS:
                return $this->processOperatorComparison($conditional);
            case Conditional::CONDITION_CONTAINSTEXT:
                // Expression is NOT(ISERROR(SEARCH("<TEXT>",<Cell Reference>)))
            case Conditional::CONDITION_NOTCONTAINSTEXT:
                // Expression is ISERROR(SEARCH("<TEXT>",<Cell Reference>))
            case Conditional::CONDITION_BEGINSWITH:
                // Expression is LEFT(<Cell Reference>,LEN("<TEXT>"))="<TEXT>"
            case Conditional::CONDITION_ENDSWITH:
                // Expression is RIGHT(<Cell Reference>,LEN("<TEXT>"))="<TEXT>"
            case Conditional::CONDITION_CONTAINSBLANKS:
                // Expression is LEN(TRIM(<Cell Reference>))=0
            case Conditional::CONDITION_NOTCONTAINSBLANKS:
                // Expression is LEN(TRIM(<Cell Reference>))>0
            case Conditional::CONDITION_CONTAINSERRORS:
                // Expression is ISERROR(<Cell Reference>)
            case Conditional::CONDITION_NOTCONTAINSERRORS:
                // Expression is NOT(ISERROR(<Cell Reference>))
            case Conditional::CONDITION_EXPRESSION:
                return $this->processExpression($conditional);
        }

        return false;
    }

    protected function wrapValue($value)
    {
        if (!is_numeric($value)) {
            if (is_bool($value)) {
                return $value ? 'TRUE' : 'FALSE';
            }

            return '"' . $value . '"';
        }

        return $value;
    }

    protected function wrapCellValue()
    {
        return $this->wrapValue($this->cell->getCalculatedValue());
    }

    protected function conditionCellAdjustment(array $matches): string
    {
var_dump($matches);
        $column = $matches[6];
        $row = $matches[7];

        if (strpos($column, '$') === false) {
            $column = Coordinate::columnIndexFromString($column);
            $column += $this->cellColumn - $this->referenceColumn;
            $column = Coordinate::stringFromColumnIndex($column);
        }

        if (strpos($row, '$') === false) {
            $row += $this->cellRow - $this->referenceRow;
        }
var_dump("{$column}{$row}");

        return $this->wrapValue($this->cell->getWorksheet()
            ->getCell(str_replace('$', '', "{$column}{$row}"))
            ->getCalculatedValue());
    }

    protected function cellConditionCheck($condition)
    {
        $splitCondition = explode(Calculation::FORMULA_STRING_QUOTE, $condition);
        $i = false;
        foreach ($splitCondition as &$value) {
            //    Only count/replace in alternating array entries (ie. not in quoted strings)
            if ($i = !$i) {
                $value = preg_replace_callback(
                    '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/i',
                    [$this, 'conditionCellAdjustment'],
                    $value
                );
            }
        }
        unset($value);
        //    Then rebuild the condition string to return it
        return implode(Calculation::FORMULA_STRING_QUOTE, $splitCondition);
    }

    protected function adjustConditionsForCellReferences(array $conditions)
    {
        return array_map(
            [$this, 'cellConditionCheck'],
            $conditions
        );
    }

    protected function processOperatorComparison(Conditional $conditional): bool
    {
        if (array_key_exists($conditional->getOperatorType(), self::COMPARISON_RANGE_OPERATORS)) {
            return $this->processRangeOperator($conditional);
        }

        $operator = self::COMPARISON_OPERATORS[$conditional->getOperatorType()];
        $conditions = $this->adjustConditionsForCellReferences($conditional->getConditions());
        $expression = sprintf('%s%s%s', $this->wrapCellValue(), $operator, array_pop($conditions));

        return $this->evaluateExpression($expression);
    }

    protected function processRangeOperator(Conditional $conditional): bool
    {
        $conditions = $this->adjustConditionsForCellReferences($conditional->getConditions());
        $expression = sprintf(
            str_replace('A1', $this->wrapCellValue(), self::COMPARISON_RANGE_OPERATORS[$conditional->getOperatorType()]),
            ...$conditions
        );

        return $this->evaluateExpression($expression);
    }

    protected function processExpression(Conditional $conditional): bool
    {
        $conditions = $this->adjustConditionsForCellReferences($conditional->getConditions());
        var_dump($conditions);
        $expression = array_pop($conditions);

        $expression = str_replace($this->referenceCell, $this->wrapCellValue(), $expression);

        return $this->evaluateExpression($expression);
    }

    protected function evaluateExpression(string $expression): bool
    {
        $expression = "={$expression}";
var_dump($expression);
        try {
            $this->engine->flushInstance();
            $result = $this->engine->calculateFormula($expression);
        } catch (Exception $e) {
            return false;
        }
var_dump($result);
        return $result;
    }
}
