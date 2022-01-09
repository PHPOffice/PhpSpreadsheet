<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

class Blanks extends WizardAbstract
{
    protected const OPERATORS = [
    ];

    private const EXPRESSIONS = [
        Wizard::NOT_BLANKS => 'LEN(TRIM(%s))>0',
        Wizard::BLANKS => 'LEN(TRIM(%s))=0'
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
            self::EXPRESSIONS[$inverse ? Wizard::BLANKS : Wizard::NOT_BLANKS],
            $this->referenceCell
        );
    }

    public function getConditional()
    {
        $conditional = new Conditional();
        $conditional->setConditionType(
            $this->inverse
                ? Conditional::CONDITION_CONTAINSBLANKS
                : Conditional::CONDITION_NOTCONTAINSBLANKS
        );
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }
}
