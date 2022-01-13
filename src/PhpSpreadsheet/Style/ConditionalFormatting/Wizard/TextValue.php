<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method TextValue contains(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue doesNotContain(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue beginsWith(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method TextValue endsWith(string $value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 */
class TextValue extends WizardAbstract
{
    protected const MAGIC_OPERATIONS = [
        'contains' => Conditional::OPERATOR_CONTAINSTEXT,
        'doesNotContain' => Conditional::OPERATOR_NOTCONTAINS,
        'beginsWith' => Conditional::OPERATOR_BEGINSWITH,
        'endsWith' => Conditional::OPERATOR_ENDSWITH,
    ];

    protected const OPERATORS = [
        Conditional::OPERATOR_CONTAINSTEXT => Conditional::CONDITION_CONTAINSTEXT,
        Conditional::OPERATOR_NOTCONTAINS => Conditional::CONDITION_NOTCONTAINSTEXT,
        Conditional::OPERATOR_BEGINSWITH => Conditional::CONDITION_BEGINSWITH,
        Conditional::OPERATOR_ENDSWITH => Conditional::CONDITION_ENDSWITH,
    ];

    private const EXPRESSIONS = [
        Conditional::OPERATOR_CONTAINSTEXT => 'NOT(ISERROR(SEARCH(%s,%s)))',
        Conditional::OPERATOR_NOTCONTAINS => 'ISERROR(SEARCH(%s,%s))',
        Conditional::OPERATOR_BEGINSWITH => 'LEFT(%s,LEN(%s))=%s',
        Conditional::OPERATOR_ENDSWITH => 'RIGHT(%s,LEN(%s))=%s',
    ];

    /** @var string */
    protected $operator;

    /** @var string */
    protected $operand;

    /**
     * @var string
     */
    protected $operandValueType;

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
        $this->operand = $operand;
        $this->operandValueType = $operandValueType;
    }

    protected function wrapValue($value): string
    {
        return '"' . $value . '"';
    }

    protected function setExpression(): void
    {
        $operand = $this->operandValueType === Wizard::VALUE_TYPE_LITERAL
            ? $this->wrapValue($this->operand)
            : $this->operand;

        if ($this->operator === Conditional::OPERATOR_CONTAINSTEXT ||
            $this->operator === Conditional::OPERATOR_NOTCONTAINS
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
        $conditional->setText($this->operandValueType === Wizard::VALUE_TYPE_LITERAL ? $this->operand : '');
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName])) {
            throw new Exception('Invalid Operation for Text Value CF Rule Wizard');
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);
        $this->operand(...$arguments);

        return $this;
    }
}
