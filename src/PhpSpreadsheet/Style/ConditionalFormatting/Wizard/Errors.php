<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method Errors notError()
 * @method Errors isError()
 */
class Errors extends WizardAbstract implements WizardInterface
{
    protected const OPERATORS = [
        'notError' => false,
        'isError' => true,
    ];

    protected const EXPRESSIONS = [
        Wizard::NOT_ERRORS => 'NOT(ISERROR(%s))',
        Wizard::ERRORS => 'ISERROR(%s)',
    ];

    protected bool $inverse;

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
        $conditional->setConditions([$this->expression]);
        $conditional->setStyle($this->getStyle());
        $conditional->setStopIfTrue($this->getStopIfTrue());

        return $conditional;
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        if (
            $conditional->getConditionType() !== Conditional::CONDITION_CONTAINSERRORS
            && $conditional->getConditionType() !== Conditional::CONDITION_NOTCONTAINSERRORS
        ) {
            throw new Exception('Conditional is not an Errors CF Rule conditional');
        }

        $wizard = new self($cellRange);
        $wizard->style = $conditional->getStyle();
        $wizard->stopIfTrue = $conditional->getStopIfTrue();
        $wizard->inverse = $conditional->getConditionType() === Conditional::CONDITION_CONTAINSERRORS;

        return $wizard;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if (!array_key_exists($methodName, self::OPERATORS)) {
            throw new Exception('Invalid Operation for Errors CF Rule Wizard');
        }

        $this->inverse(self::OPERATORS[$methodName]);

        return $this;
    }
}
