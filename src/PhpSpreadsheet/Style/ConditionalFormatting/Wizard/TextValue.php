<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method TextValue contains(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue doesNotContain(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue doesntContain(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue beginsWith(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue startsWith(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue endsWith(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 */
class TextValue extends WizardAbstract implements WizardInterface
{
    protected const MAGIC_OPERATIONS = [
        'contains' => Conditional::OPERATOR_CONTAINSTEXT,
        'doesntContain' => Conditional::OPERATOR_NOTCONTAINS,
        'doesNotContain' => Conditional::OPERATOR_NOTCONTAINS,
        'beginsWith' => Conditional::OPERATOR_BEGINSWITH,
        'startsWith' => Conditional::OPERATOR_BEGINSWITH,
        'endsWith' => Conditional::OPERATOR_ENDSWITH,
    ];

    protected const OPERATORS = [
        Conditional::OPERATOR_CONTAINSTEXT => Conditional::CONDITION_CONTAINSTEXT,
        Conditional::OPERATOR_NOTCONTAINS => Conditional::CONDITION_NOTCONTAINSTEXT,
        Conditional::OPERATOR_BEGINSWITH => Conditional::CONDITION_BEGINSWITH,
        Conditional::OPERATOR_ENDSWITH => Conditional::CONDITION_ENDSWITH,
    ];

    protected const EXPRESSIONS = [
        Conditional::OPERATOR_CONTAINSTEXT => 'NOT(ISERROR(SEARCH(%s,%s)))',
        Conditional::OPERATOR_NOTCONTAINS => 'ISERROR(SEARCH(%s,%s))',
        Conditional::OPERATOR_BEGINSWITH => 'LEFT(%s,LEN(%s))=%s',
        Conditional::OPERATOR_ENDSWITH => 'RIGHT(%s,LEN(%s))=%s',
    ];

    protected string $operator;

    protected string $operand;

    protected string $operandValueType;

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    protected function operator(string $operator): void
    {
        if (!isset(self::OPERATORS[$operator])) {
            throw new Exception('Invalid Operator for Text Value CF Rule Wizard');
        }

        $this->operator = $operator;
    }

    protected function operand(string $operand, string $operandValueType = Wizard::VALUE_TYPE_LITERAL): void
    {
        $operand = $this->validateOperand($operand, $operandValueType);

        $this->operand = $operand;
        $this->operandValueType = $operandValueType;
    }

    protected function wrapValue(string $value): string
    {
        return '"' . $value . '"';
    }

    protected function setExpression(): void
    {
        $operand = $this->operandValueType === Wizard::VALUE_TYPE_LITERAL
            ? $this->wrapValue(str_replace('"', '""', $this->operand))
            : $this->cellConditionCheck($this->operand);

        if (
            $this->operator === Conditional::OPERATOR_CONTAINSTEXT
            || $this->operator === Conditional::OPERATOR_NOTCONTAINS
        ) {
            $this->expression = sprintf(self::EXPRESSIONS[$this->operator], $operand, $this->referenceCell);
        } else {
            $this->expression = sprintf(self::EXPRESSIONS[$this->operator], $this->referenceCell, $operand, $operand);
        }
    }

    public function getConditional(): Conditional
    {
        $this->setExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(self::OPERATORS[$this->operator]);
        $conditional->setOperatorType($this->operator);
        $conditional->setText(
            $this->operandValueType !== Wizard::VALUE_TYPE_LITERAL
                ? $this->cellConditionCheck($this->operand)
                : $this->operand
        );
        $conditional->setConditions([$this->expression]);
        $conditional->setStyle($this->getStyle());
        $conditional->setStopIfTrue($this->getStopIfTrue());

        return $conditional;
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        if (!in_array($conditional->getConditionType(), self::OPERATORS, true)) {
            throw new Exception('Conditional is not a Text Value CF Rule conditional');
        }

        $wizard = new self($cellRange);
        $wizard->operator = (string) array_search($conditional->getConditionType(), self::OPERATORS, true);
        $wizard->style = $conditional->getStyle();
        $wizard->stopIfTrue = $conditional->getStopIfTrue();

        // Best-guess to try and identify if the text is a string literal, a cell reference or a formula?
        $wizard->operandValueType = Wizard::VALUE_TYPE_LITERAL;
        $condition = $conditional->getText();
        if (preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '$/i', $condition)) {
            $wizard->operandValueType = Wizard::VALUE_TYPE_CELL;
            $condition = self::reverseAdjustCellRef($condition, $cellRange);
        } elseif (
            preg_match('/\(\)/', $condition)
            || preg_match('/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/i', $condition)
        ) {
            $wizard->operandValueType = Wizard::VALUE_TYPE_FORMULA;
        }
        $wizard->operand = $condition;

        return $wizard;
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $methodName, array $arguments): self
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName])) {
            throw new Exception('Invalid Operation for Text Value CF Rule Wizard');
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);
        //$this->operand(...$arguments);
        if (count($arguments) < 2) {
            $this->operand($arguments[0]);
        } else {
            $this->operand($arguments[0], $arguments[1]);
        }

        return $this;
    }
}
