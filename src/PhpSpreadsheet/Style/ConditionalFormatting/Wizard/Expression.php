<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;

class Expression extends WizardAbstract
{
    /**
     * @var string
     */
    protected $expression;

    public function __construct(string $cellRange)
    {
        parent::__construct($cellRange);
    }

    public function expression(string $expression): void
    {
        $this->expression = $expression;
    }

    public function getConditional()
    {
        $expression = $this->adjustConditionsForCellReferences([$this->expression]);

        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_EXPRESSION);
        $conditional->setConditions($expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }
}
