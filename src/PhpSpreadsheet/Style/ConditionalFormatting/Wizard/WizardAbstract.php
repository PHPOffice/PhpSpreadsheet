<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;

abstract class WizardAbstract
{
    /**
     * @var ?Style
     */
    protected $style;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $cellRange;

    /**
     * @var string
     */
    protected $referenceCell;

    /**
     * @var int
     */
    protected $referenceRow;

    /**
     * @var bool
     */
    protected $stopIfTrue = false;

    /**
     * @var int
     */
    protected $referenceColumn;

    public function __construct(string $cellRange)
    {
        $this->setCellRange($cellRange);
    }

    public function getCellRange(): string
    {
        return $this->cellRange;
    }

    public function setCellRange(string $cellRange): void
    {
        $this->cellRange = $cellRange;
        $this->setReferenceCellForExpressions($cellRange);
    }

    protected function setReferenceCellForExpressions(string $conditionalRange): void
    {
        $conditionalRange = Coordinate::splitRange(str_replace('$', '', strtoupper($conditionalRange)));
        [$this->referenceCell] = $conditionalRange[0];

        [$this->referenceColumn, $this->referenceRow] = Coordinate::indexesFromString($this->referenceCell);
    }

    public function getStopIfTrue(): bool
    {
        return $this->stopIfTrue;
    }

    public function setStopIfTrue(bool $stopIfTrue): void
    {
        $this->stopIfTrue = $stopIfTrue;
    }

    public function getStyle(): Style
    {
        return $this->style ?? new Style(false, true);
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }

    protected function validateOperand(string $operand, string $operandValueType = Wizard::VALUE_TYPE_LITERAL): string
    {
        if (
            $operandValueType === Wizard::VALUE_TYPE_LITERAL &&
            substr($operand, 0, 1) === '"' &&
            substr($operand, -1) === '"'
        ) {
            $operand = str_replace('""', '"', substr($operand, 1, -1));
        } elseif ($operandValueType === Wizard::VALUE_TYPE_FORMULA && substr($operand, 0, 1) === '=') {
            $operand = substr($operand, 1);
        }

        return $operand;
    }

    protected static function reverseCellAdjustment(array $matches, int $referenceColumn, int $referenceRow): string
    {
        $worksheet = $matches[1];
        $column = $matches[6];
        $row = $matches[7];

        if (strpos($column, '$') === false) {
            $column = Coordinate::columnIndexFromString($column);
            $column -= $referenceColumn - 1;
            $column = Coordinate::stringFromColumnIndex($column);
        }

        if (strpos($row, '$') === false) {
            $row -= $referenceRow - 1;
        }

        return "{$worksheet}{$column}{$row}";
    }

    public static function reverseAdjustCellRef(string $condition, string $cellRange): string
    {
        $conditionalRange = Coordinate::splitRange(str_replace('$', '', strtoupper($cellRange)));
        [$referenceCell] = $conditionalRange[0];
        [$referenceColumnIndex, $referenceRow] = Coordinate::indexesFromString($referenceCell);

        $splitCondition = explode(Calculation::FORMULA_STRING_QUOTE, $condition);
        $i = false;
        foreach ($splitCondition as &$value) {
            //    Only count/replace in alternating array entries (ie. not in quoted strings)
            if ($i = !$i) {
                $value = (string) preg_replace_callback(
                    '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/i',
                    function ($matches) use ($referenceColumnIndex, $referenceRow) {
                        return self::reverseCellAdjustment($matches, $referenceColumnIndex, $referenceRow);
                    },
                    $value
                );
            }
        }
        unset($value);

        //    Then rebuild the condition string to return it
        return implode(Calculation::FORMULA_STRING_QUOTE, $splitCondition);
    }

    protected function conditionCellAdjustment(array $matches): string
    {
        $worksheet = $matches[1];
        $column = $matches[6];
        $row = $matches[7];

        if (strpos($column, '$') === false) {
            $column = Coordinate::columnIndexFromString($column);
            $column += $this->referenceColumn - 1;
            $column = Coordinate::stringFromColumnIndex($column);
        }

        if (strpos($row, '$') === false) {
            $row += $this->referenceRow - 1;
        }

        return "{$worksheet}{$column}{$row}";
    }

    protected function cellConditionCheck(string $condition): string
    {
        $splitCondition = explode(Calculation::FORMULA_STRING_QUOTE, $condition);
        $i = false;
        foreach ($splitCondition as &$value) {
            //    Only count/replace in alternating array entries (ie. not in quoted strings)
            if ($i = !$i) {
                $value = (string) preg_replace_callback(
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

    protected function adjustConditionsForCellReferences(array $conditions): array
    {
        return array_map(
            [$this, 'cellConditionCheck'],
            $conditions
        );
    }
}
