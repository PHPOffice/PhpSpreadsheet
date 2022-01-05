<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;

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
     * @var Style $baseStyle
     */
    protected $baseStyle;

    /**
     * @var ?string $referenceCell
     */
    protected $referenceCell;

    /**
     * @var Conditional[]
     */
    protected $conditionalStyles;

    protected $engine;

    public function __construct(Cell $cell, Style $baseStyle)
    {
        $this->cell = $cell;
        $this->baseStyle = $baseStyle;
        $this->engine = Calculation::getInstance();
    }

    protected function setReferenceCellForExpressions(?string $conditionalRange)
    {
        $this->referenceCell = null;
        if ($conditionalRange !== null) {
            $conditionalRange = Coordinate::splitRange(str_replace('$', '', $conditionalRange));
            [$this->referenceCell] = $conditionalRange[0];
        }
    }

    public function matchConditions(?string $conditionalRange, array $conditionalStyles = []): Style
    {
        $this->setReferenceCellForExpressions($conditionalRange);
        $this->conditionalStyles = $conditionalStyles;

        var_dump($this->baseStyle->exportArray());
        foreach ($this->conditionalStyles as $conditional) {
            /** @var Conditional $conditional */
            if ($this->evaluateConditional($conditional) === true) {
                $style = $conditional->getStyle()->exportArray();
                var_dump($style);
                // Merging the conditional style into the base style goes in here
                if ($conditional->getStopIfTrue() === true) {
                    break;
                }
            }
        }

        return $this->baseStyle;
    }

    protected function evaluateConditional(Conditional $conditional): bool
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
            case Conditional::CONDITION_EXPRESSION:
                return $this->processExpression($conditional);
        }

        return false;
    }

    protected function wrappedValue()
    {
        $value = $this->cell->getValue();
        if (!is_numeric($value)) {
            return '"' . $value . '"';
        }

        return $value;
    }

    protected function processOperatorComparison(Conditional $conditional): bool
    {
        if (array_key_exists($conditional->getOperatorType(), self::COMPARISON_RANGE_OPERATORS)) {
            return $this->processRangeOperator($conditional);
        }

        $operator = self::COMPARISON_OPERATORS[$conditional->getOperatorType()];
        $conditions = $conditional->getConditions();
        $expression = sprintf('%s%s%s', $this->wrappedValue(), $operator, array_pop($conditions));

        return $this->evaluateExpression($expression);
    }

    protected function processRangeOperator(Conditional $conditional): bool
    {
        $conditions = $conditional->getConditions();
        sort($conditions);
        $expression = sprintf(
            str_replace('A1', $this->wrappedValue(), self::COMPARISON_RANGE_OPERATORS[$conditional->getOperatorType()]),
            ...$conditions
        );

        return $this->evaluateExpression($expression);
    }

    protected function processExpression(Conditional $conditional): bool
    {
        $conditions = $conditional->getConditions();
        $expression = array_pop($conditions);

        $expression = str_replace($this->referenceCell, $this->wrappedValue(), $expression);

        return $this->evaluateExpression($expression);
    }

    protected function evaluateExpression(string $expression): bool
    {
        $expression = "={$expression}";

        try {
            $result = $this->engine->calculateFormula($expression);
        } catch (Exception $e) {
            return false;
        }

        return $result;
    }
}
