<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellMatcher;

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

    /** @var string $operator */
    protected $operator;

    /** @var array $operand */
    protected $operand = [];

    /**
     * @var bool[] $isCellReference
     */
    protected $isCellReference = [];

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    protected function operator(string $operator)
    {
        if ((!isset(self::SINGLE_OPERATORS[$operator])) && (!isset(self::RANGE_OPERATORS[$operator]))) {
            throw new \Exception('Invalid Operator for Cell Value CF Rule Wizard');
        }

        $this->operator = $operator;
    }

    protected function operand($index, $operand, bool $isCellReference = false)
    {
        $this->operand[$index] = $operand;
        $this->isCellReference[$index] = $isCellReference;
    }

    protected function wrapValue($value, $isCellReference)
    {
        if (!is_numeric($value) && !is_bool($value) && !is_null($value) && $isCellReference === false) {
            return '"' . $value . '"';
        }

        return $value;
    }

    public function getConditional()
    {
        $values = array_map([$this, 'wrapValue'], $this->operand, $this->isCellReference);

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
            throw new \Exception('Invalid Operator for Cell Value CF Rule Wizard');
        }

        if ($methodName === 'and') {
            if (!isset(self::RANGE_OPERATORS[$this->operator])) {
                throw new \Exception('AND Value is only appropriate for range operators');
            }

            $this->operand(1, ...$arguments);
            return $this;
        }

        $this->operator(self::MAGIC_OPERATIONS[$methodName]);
        $this->operand(0, ...$arguments);

        return $this;
    }
}
