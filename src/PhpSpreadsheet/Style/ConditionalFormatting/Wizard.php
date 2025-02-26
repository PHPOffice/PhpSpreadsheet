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

    protected string $cellRange;

    public function __construct(string $cellRange)
    {
        $this->cellRange = $cellRange;
    }

    public function newRule(string $ruleType): WizardInterface
    {
        return match ($ruleType) {
            self::CELL_VALUE => new Wizard\CellValue($this->cellRange),
            self::TEXT_VALUE => new Wizard\TextValue($this->cellRange),
            self::BLANKS => new Wizard\Blanks($this->cellRange, true),
            self::NOT_BLANKS => new Wizard\Blanks($this->cellRange, false),
            self::ERRORS => new Wizard\Errors($this->cellRange, true),
            self::NOT_ERRORS => new Wizard\Errors($this->cellRange, false),
            self::EXPRESSION, self::FORMULA => new Wizard\Expression($this->cellRange),
            self::DATES_OCCURRING => new Wizard\DateValue($this->cellRange),
            self::DUPLICATES => new Wizard\Duplicates($this->cellRange, false),
            self::UNIQUE => new Wizard\Duplicates($this->cellRange, true),
            default => throw new Exception('No wizard exists for this CF rule type'),
        };
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        $conditionalType = $conditional->getConditionType();

        return match ($conditionalType) {
            Conditional::CONDITION_CELLIS => Wizard\CellValue::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_CONTAINSTEXT, Conditional::CONDITION_NOTCONTAINSTEXT, Conditional::CONDITION_BEGINSWITH, Conditional::CONDITION_ENDSWITH => Wizard\TextValue::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_CONTAINSBLANKS, Conditional::CONDITION_NOTCONTAINSBLANKS => Wizard\Blanks::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_CONTAINSERRORS, Conditional::CONDITION_NOTCONTAINSERRORS => Wizard\Errors::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_TIMEPERIOD => Wizard\DateValue::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_EXPRESSION => Wizard\Expression::fromConditional($conditional, $cellRange),
            Conditional::CONDITION_DUPLICATES, Conditional::CONDITION_UNIQUE => Wizard\Duplicates::fromConditional($conditional, $cellRange),
            default => throw new Exception('No wizard exists for this CF rule type'),
        };
    }
}
