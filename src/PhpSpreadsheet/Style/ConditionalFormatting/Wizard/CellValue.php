<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellMatcher;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method CellValue equals($value, bool $isCellReference = false)
 * @method CellValue notEquals($value, bool $isCellReference = false)
 * @method CellValue greaterThan($value, bool $isCellReference = false)
 * @method CellValue greaterThanOrEqual($value, bool $isCellReference = false)
 * @method CellValue lessThan($value, bool $isCellReference = false)
 * @method CellValue lessThanOrEqual($value, bool $isCellReference = false)
 * @method CellValue between($value, bool $isCellReference = false)
 * @method CellValue notBetween($value, bool $isCellReference = false)
 * @method CellValue and($value, bool $isCellReference = false)
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

    protected function operand($index, $operand, string $operandValueType = Wizard::VALUE_TYPE_LITERAL): void
    {
        $this->operand[$index] = $operand;
        $this->operandValueType[$index] = $operandValueType;
    }

    protected function wrapValue($value, $operandValueType)
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

    public function getConditional()
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
     * @param $methodName
     * @param $arguments
     */
    public function __call($methodName, $arguments)
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
