<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

class Errors extends WizardAbstract
{
    protected const OPERATORS = [
    ];

    private const EXPRESSIONS = [
        Wizard::NOT_ERRORS => 'NOT(ISERROR(%s))',
        Wizard::ERRORS => 'ISERROR(%s)'
    ];

    /**
     * @var bool $inverse
     */
    protected $inverse;

    public function __construct(string $cellRange, bool $inverse = false)
    {
        parent::__construct($cellRange);
        $this->inverse = $inverse;

        $this->expression = sprintf(
            self::EXPRESSIONS[$inverse ? Wizard::ERRORS : Wizard::NOT_ERRORS],
            $this->referenceCell
        );
    }

    public function getConditional()
    {
        $conditional = new Conditional();
        $conditional->setConditionType(
            $this->inverse
                ? Conditional::CONDITION_CONTAINSERRORS
                : Conditional::CONDITION_NOTCONTAINSERRORS
        );
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }
}
