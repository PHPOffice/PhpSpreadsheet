<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Style;

abstract class WizardAbstract
{
    /**
     * @var Style
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
     * @var int
     */
    protected $referenceColumn;

    public function __construct(string $cellRange)
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

    protected function conditionCellAdjustment(array $matches): string
    {
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

        return "{$column}{$row}";
    }

    protected function cellConditionCheck($condition)
    {
        $splitCondition = explode(Calculation::FORMULA_STRING_QUOTE, $condition);
        $i = false;
        foreach ($splitCondition as &$value) {
            //    Only count/replace in alternating array entries (ie. not in quoted strings)
            if ($i = !$i) {
                $value = preg_replace_callback(
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

    protected function adjustConditionsForCellReferences(array $conditions)
    {
        return array_map(
            [$this, 'cellConditionCheck'],
            $conditions
        );
    }

    public function getStyle(): Style
    {
        return $this->style ?? new Style(false, true);
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }

    public function getCellRange(): string
    {
        return $this->cellRange;
    }
}
