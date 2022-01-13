<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method Errors not()
 */
class Errors extends WizardAbstract
{
    protected const OPERATORS = [
    ];

    private const EXPRESSIONS = [
        Wizard::NOT_ERRORS => 'NOT(ISERROR(%s))',
        Wizard::ERRORS => 'ISERROR(%s)',
    ];

    /**
     * @var bool
     */
    protected $inverse;

    public function __construct(string $cellRange, bool $inverse = false)
    {
        parent::__construct($cellRange);
        $this->inverse = $inverse;
    }

    protected function inverse(bool $inverse): void
    {
        $this->inverse = $inverse;
    }

    protected function getExpression(): void
    {
        $this->expression = sprintf(
            self::EXPRESSIONS[$this->inverse ? Wizard::ERRORS : Wizard::NOT_ERRORS],
            $this->referenceCell
        );
    }

    public function getConditional(): Conditional
    {
        $this->getExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(
            $this->inverse ? Conditional::CONDITION_CONTAINSERRORS : Conditional::CONDITION_NOTCONTAINSERRORS
        );
        $conditional->setConditions($this->expression);
        $conditional->setStyle($this->getStyle());

        return $conditional;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if ($methodName !== 'not') {
            throw new Exception('Invalid Operation for Errors CF Rule Wizard');
        }

        $this->inverse(false);

        return $this;
    }
}
