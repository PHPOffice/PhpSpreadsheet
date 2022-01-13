<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellMatcher;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method CellValue equals($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue notEquals($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue greaterThan($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue greaterThanOrEqual($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue lessThan($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue lessThanOrEqual($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue between($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue notBetween($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 * @method CellValue and($value, string $operandValueType = Wizard::VALUE_TYPE_LITERAL)
 */
class CellValue extends WizardAbstract
{
    public const MAGIC_OPERATIONS = [
        'equals' => Conditional::OPERATOR_EQUAL,
        'notEquals' => Conditional::OPERATOR_NOTEQUAL,
        'greaterThan' => Conditional::OPERATOR_GREATERTHAN,
        'greaterThanOrEqual' => Conditional::OPERATOR_GREATERTHANOREQUAL,
        'lessThan' => Conditional::OPERATOR_LESSTHAN,
        'lessThanOrEqual' => Conditional::OPERATOR_LESSTHANOREQUAL,
        'between' => Conditional::OPERATOR_BETWEEN,
        'notBetween' => Conditional::OPERATOR_NOTBETWEEN,
    ];

    protected const SINGLE_OPERATORS = CellMatcher::COMPARISON_OPERATORS;

    protected const RANGE_OPERATORS = CellMatcher::COMPARISON_RANGE_OPERATORS;

    /** @var string */
    protected $operator = Conditional::OPERATOR_EQUAL;

    /** @var array */
    protected $operand = [0];

    /**
     * @var string[]
     */
    protected $operandValueType = [];

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    protected function operator(string $operator): void
    {
        if ((!isset(self::SINGLE_OPERATORS[$operator])) && (!isset(self::RANGE_OPERATORS[$operator]))) {
            throw new Exception('Invalid Operator for Cell Value CF Rule Wizard');
        }

        $this->operator = $operator;
    }

    /**
     * @param mixed $operand
     */
    protected function operand(int $index, $operand, string $operandValueType = Wizard::VALUE_TYPE_LITERAL): void
    {
        $this->operand[$index] = $operand;
        $this->operandValueType[$index] = $operandValueType;
    }

    /**
     * @param mixed $value
     *
     * @return bool|int|string|null
     */
    protected function wrapValue($value, string $operandValueType)
    {
        if (!is_numeric($value) && !is_bool($value) && null !== $value) {
            if ($operandValueType === Wizard::VALUE_TYPE_LITERAL) {
                return '"' . $value . '"';
            }

            return $this->cellConditionCheck($value);
        }

        if (null === $value) {
            $value = 'NULL';
        } elseif (is_bool($value)) {
            $value = $value ? 'TRUE' : 'FALSE';
        }

        return $value;
    }

    public function getConditional(): Conditional
    {
        if (!isset(self::RANGE_OPERATORS[$this->operator])) {
            unset($this->operand[1], $this->operandValueType[1]);
        }
        $values = array_map([$this, 'wrapValue'], $this->operand, $this->operandValueType);

        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional->setOperatorType($this->operator);
        $conditional->setConditions($values);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if (!isset(self::MAGIC_OPERATIONS[$methodName]) && $methodName !== 'and') {
            throw new Exception('Invalid Operator for Cell Value CF Rule Wizard');
        }

        if ($methodName === 'and') {
            if (!isset(self::RANGE_OPERATORS[$this->operator])) {
                throw new Exception('AND Value is only appropriate for range operators');
            }

            $this->operand(1, ...$arguments);

            return $this;
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);
        $this->operand(0, ...$arguments);

        return $this;
    }
}
