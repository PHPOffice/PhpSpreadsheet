<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard\WizardInterface;

class Wizard
{
    public const CELL_VALUE = 'cellValue';
    public const TEXT_VALUE = 'textValue';
    public const BLANKS = Conditional::CONDITION_CONTAINSBLANKS;
    public const NOT_BLANKS = Conditional::CONDITION_NOTCONTAINSBLANKS;
    public const ERRORS = Conditional::CONDITION_CONTAINSERRORS;
    public const NOT_ERRORS = Conditional::CONDITION_NOTCONTAINSERRORS;
    public const EXPRESSION = Conditional::CONDITION_EXPRESSION;
    public const FORMULA = Conditional::CONDITION_EXPRESSION;
    public const DATES_OCCURRING = 'DateValue';
    public const DUPLICATES = Conditional::CONDITION_DUPLICATES;
    public const UNIQUE = Conditional::CONDITION_UNIQUE;

    public const VALUE_TYPE_LITERAL = 'value';
    public const VALUE_TYPE_CELL = 'cell';
    public const VALUE_TYPE_FORMULA = 'formula';

    /**
     * @var string
     */
    protected $cellRange;

    public function __construct(string $cellRange)
    {
        $this->cellRange = $cellRange;
    }

    public function newRule(string $ruleType): WizardInterface
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
            case self::FORMULA:
                return new Wizard\Expression($this->cellRange);
            case self::DATES_OCCURRING:
                return new Wizard\DateValue($this->cellRange);
            case self::DUPLICATES:
                return new Wizard\Duplicates($this->cellRange, false);
            case self::UNIQUE:
                return new Wizard\Duplicates($this->cellRange, true);
            default:
                throw new Exception('No wizard exists for this CF rule type');
        }
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        $conditionalType = $conditional->getConditionType();

        switch ($conditionalType) {
            case Conditional::CONDITION_CELLIS:
                return Wizard\CellValue::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_CONTAINSTEXT:
            case Conditional::CONDITION_NOTCONTAINSTEXT:
            case Conditional::CONDITION_BEGINSWITH:
            case Conditional::CONDITION_ENDSWITH:
                return Wizard\TextValue::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_CONTAINSBLANKS:
            case Conditional::CONDITION_NOTCONTAINSBLANKS:
                return Wizard\Blanks::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_CONTAINSERRORS:
            case Conditional::CONDITION_NOTCONTAINSERRORS:
                return Wizard\Errors::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_TIMEPERIOD:
                return Wizard\DateValue::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_EXPRESSION:
                return Wizard\Expression::fromConditional($conditional, $cellRange);
            case Conditional::CONDITION_DUPLICATES:
            case Conditional::CONDITION_UNIQUE:
                return Wizard\Duplicates::fromConditional($conditional, $cellRange);
            default:
                throw new Exception('No wizard exists for this CF rule type');
        }
    }
}
