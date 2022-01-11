<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method Blanks not()
 */
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
    }

    protected function inverse(bool $inverse)
    {
        $this->inverse = $inverse;
    }

    protected function getExpression()
    {
        $this->expression = sprintf(
            self::EXPRESSIONS[$this->inverse ? Wizard::BLANKS : Wizard::NOT_BLANKS],
            $this->referenceCell
        );
    }

    public function getConditional()
    {
        $this->getExpression();

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

    /**
     * @param $methodName
     * @param $arguments
     */
    public function __call($methodName, $arguments)
    {
        if ($methodName !== 'not') {
            throw new \Exception('Invalid Operation for Blanks CF Rule Wizard');
        }

        $this->inverse(false);

        return $this;
    }
}
