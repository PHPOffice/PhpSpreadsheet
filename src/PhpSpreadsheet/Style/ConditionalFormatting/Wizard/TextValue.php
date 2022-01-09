<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;

/**
 * @method TextValue contains(string $value, bool $isCellReference = false)
 * @method TextValue doesNotContain(string $value, bool $isCellReference = false)
 * @method TextValue beginsWith(string $value, bool $isCellReference = false)
 * @method TextValue endsWith(string $value, bool $isCellReference = false)
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

    /** @var string $operator */
    protected $operator;

    /** @var string $operand */
    protected $operand;

    /**
     * @var bool $isCellReference
     */
    protected $isCellReference;

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    protected function operator(string $operator)
    {
        if (!isset(self::OPERATORS[$operator])) {
            throw new \Exception('Invalid Operator for Text Value CF Rule Wizard');
        }

        $this->operator = $operator;
    }

    protected function operand(string $operand, bool $isCellReference = false)
    {
        $this->operand = $operand;
        $this->isCellReference = $isCellReference;
    }

    protected function wrapValue($value)
    {
            return '"' . $value . '"';
    }

    protected function setExpression()
    {
        $operand = $this->isCellReference ? $this->operand : $this->wrapValue($this->operand);

        if ($this->operator === Conditional::OPERATOR_CONTAINSTEXT ||
            $this->operator === Conditional::OPERATOR_NOTCONTAINS
        ) {
            $this->expression = sprintf(self::EXPRESSIONS[$this->operator], $operand, $this->referenceCell);
        } else {
            $this->expression = sprintf(self::EXPRESSIONS[$this->operator], $this->referenceCell, $operand, $operand);
        }
    }

    public function getConditional()
    {
        $this->setExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(self::OPERATORS[$this->operator]);
        $conditional->setOperatorType($this->operator);
        $conditional->setText($this->isCellReference ? '' : $this->operand);
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }

    /**
     * @param $methodName
     * @param $arguments
     */
    public function __call($methodName, $arguments)
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName])) {
            throw new \Exception('Invalid Operation for Text Value CF Rule Wizard');
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);
        $this->operand(...$arguments);
    }
}
