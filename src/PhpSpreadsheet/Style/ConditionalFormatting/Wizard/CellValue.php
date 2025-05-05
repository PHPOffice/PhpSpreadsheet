<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Exception;
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
class CellValue extends WizardAbstract implements WizardInterface
{
    protected const MAGIC_OPERATIONS = [
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

    protected string $operator = Conditional::OPERATOR_EQUAL;

    /** @var array<int|string> */
    protected array $operand = [0];

    /**
     * @var string[]
     */
    protected array $operandValueType = [];

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

    protected function operand(int $index, mixed $operand, string $operandValueType = Wizard::VALUE_TYPE_LITERAL): void
    {
        if (is_string($operand)) {
            $operand = $this->validateOperand($operand, $operandValueType);
        }

        $this->operand[$index] = $operand; //* @phpstan-ignore-line
        $this->operandValueType[$index] = $operandValueType;
    }

    /** @param null|bool|float|int|string $value value to be wrapped */
    protected function wrapValue(mixed $value, string $operandValueType): float|int|string
    {
        if (!is_numeric($value) && !is_bool($value) && null !== $value) {
            if ($operandValueType === Wizard::VALUE_TYPE_LITERAL) {
                return '"' . str_replace('"', '""', $value) . '"';
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
        $conditional->setStopIfTrue($this->getStopIfTrue());

        return $conditional;
    }

    protected static function unwrapString(string $condition): string
    {
        if ((str_starts_with($condition, '"')) && (str_starts_with(strrev($condition), '"'))) {
            $condition = substr($condition, 1, -1);
        }

        return str_replace('""', '"', $condition);
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        if ($conditional->getConditionType() !== Conditional::CONDITION_CELLIS) {
            throw new Exception('Conditional is not a Cell Value CF Rule conditional');
        }

        $wizard = new self($cellRange);
        $wizard->style = $conditional->getStyle();
        $wizard->stopIfTrue = $conditional->getStopIfTrue();

        $wizard->operator = $conditional->getOperatorType();
        $conditions = $conditional->getConditions();
        foreach ($conditions as $index => $condition) {
            // Best-guess to try and identify if the text is a string literal, a cell reference or a formula?
            $operandValueType = Wizard::VALUE_TYPE_LITERAL;
            if (is_string($condition)) {
                if (Calculation::keyInExcelConstants($condition)) {
                    $condition = Calculation::getExcelConstants($condition);
                } elseif (preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '$/i', $condition)) {
                    $operandValueType = Wizard::VALUE_TYPE_CELL;
                    $condition = self::reverseAdjustCellRef($condition, $cellRange);
                } elseif (
                    preg_match('/\(\)/', $condition)
                    || preg_match('/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/i', $condition)
                ) {
                    $operandValueType = Wizard::VALUE_TYPE_FORMULA;
                    $condition = self::reverseAdjustCellRef($condition, $cellRange);
                } else {
                    $condition = self::unwrapString($condition);
                }
            }
            $wizard->operand($index, $condition, $operandValueType);
        }

        return $wizard;
    }

    /**
     * @param mixed[] $arguments
     */
    public function __call(string $methodName, array $arguments): self
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
        //$this->operand(0, ...$arguments);
        if (count($arguments) < 2) {
            $this->operand(0, $arguments[0]);
        } else {
            /** @var string */
            $arg1 = $arguments[1];
            $this->operand(0, $arguments[0], $arg1);
        }

        return $this;
    }
}
