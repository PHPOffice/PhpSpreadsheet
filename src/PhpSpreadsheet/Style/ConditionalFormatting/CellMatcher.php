<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Style;

class CellMatcher
{
    const CELL_IS_OPERATORS = [
        Conditional::OPERATOR_EQUAL => '=',
        Conditional::OPERATOR_GREATERTHAN => '>',
        Conditional::OPERATOR_GREATERTHANOREQUAL => '>=',
        Conditional::OPERATOR_LESSTHAN => '<',
        Conditional::OPERATOR_LESSTHANOREQUAL => '<=',
        Conditional::OPERATOR_NOTEQUAL => '<>',
    ];

    /**
     * @var Cell $cell;
     */
    protected $cell;

    /**
     * @var Style $baseStyle
     */
    protected $baseStyle;

    /**
     * @var Conditional[]
     */
    protected $conditionalStyles;

    protected $engine;

    public function __construct(Cell $cell, Style $baseStyle, array $conditionalStyles)
    {
        $this->cell = $cell;
        $this->baseStyle = $baseStyle;
        $this->conditionalStyles = $conditionalStyles;
        $this->engine = Calculation::getInstance();
    }

    public function matchConditions(): Style
    {
        var_dump($this->baseStyle->exportArray());
        foreach ($this->conditionalStyles as $conditional) {
            /** @var Conditional $conditional */
            var_dump($conditional->get, $conditional->getConditionType(), $conditional->getOperatorType(), $conditional->getConditions());
            $result = $this->evaluateConditional($conditional);
            if ($result === true) {
                $style = $conditional->getStyle();
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
                return $this->processCellIs($conditional);
            case Conditional::CONDITION_EXPRESSION:
                return $this->processExpression($conditional);
        }

        return false;
    }

    protected function processCellIs(Conditional $conditional): bool
    {
        $operator = self::CELL_IS_OPERATORS[$conditional->getOperatorType()];
        $conditions = $conditional->getConditions();
        $expression = sprintf('=%s%s%s', $this->cell->getValue(), $operator, array_pop($conditions));
        return $this->evaluateExpression($expression);
    }

    protected function processExpression(Conditional $conditional): bool
    {
        $conditions = $conditional->getConditions();
        $expression = array_pop($conditions);
        $expression = str_replace('A1', $this->cell->getValue(), $expression);
        $expression = '=' . $expression;
        return $this->evaluateExpression($expression);
    }

    protected function evaluateExpression(string $expression)
    {
        var_dump($expression);
        try {
            $result = $this->engine->calculateFormula($expression);
        } catch (Exception $e) {
            var_dump('EXCEPTION', $e->getMessage());
            return false;
        }
        var_dump($result);
        return $result;
    }
}
