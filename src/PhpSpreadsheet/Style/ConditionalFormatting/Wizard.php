<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

class Wizard
{
    const CELL_VALUE = 'cellValue';
    const TEXT_VALUE = 'textValue';
    const BLANKS = Conditional::CONDITION_CONTAINSBLANKS;
    const NOT_BLANKS = Conditional::CONDITION_NOTCONTAINSBLANKS;
    const ERRORS = Conditional::CONDITION_CONTAINSERRORS;
    const NOT_ERRORS = Conditional::CONDITION_NOTCONTAINSERRORS;
    const EXPRESSION = Conditional::CONDITION_EXPRESSION;
    const DATES_OCCURRING = 'DateValue';

    const VALUE_TYPE_LITERAL = 'value';
    const VALUE_TYPE_CELL = 'cell';
    const VALUE_TYPE_FORMULA = 'formula';

    /**
     * @var string
     */
    protected $cellRange;

    public function __construct(string $cellRange)
    {
        $this->cellRange = $cellRange;
    }

    public function newRule(string $ruleType)
    {
        switch ($ruleType) {
            case self::CELL_VALUE:
                return new Wizard\CellValue($this->cellRange);
            case self::TEXT_VALUE:
                return new Wizard\TextValue($this->cellRange);
            case self::BLANKS:
                return new Wizard\Blanks($this->cellRange, true);
            case self::NOT_BLANKS:
                return new Wizard\Blanks($this->cellRange, false);
            case self::ERRORS:
                return new Wizard\Errors($this->cellRange, true);
            case self::NOT_ERRORS:
                return new Wizard\Errors($this->cellRange, false);
            case self::EXPRESSION:
                return new Wizard\Expression($this->cellRange);
            case self::DATES_OCCURRING:
                return new Wizard\DateValue($this->cellRange);
            default:
                throw new Exception('No wizard exists for this rule type');
        }
    }
}
