<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method Blanks notBlank()
 * @method Blanks notEmpty()
 * @method Blanks isBlank()
 * @method Blanks isEmpty()
 */
class Blanks extends WizardAbstract implements WizardInterface
{
    protected const OPERATORS = [
        'notBlank' => false,
        'isBlank' => true,
        'notEmpty' => false,
        'empty' => true,
    ];

    protected const EXPRESSIONS = [
        Wizard::NOT_BLANKS => 'LEN(TRIM(%s))>0',
        Wizard::BLANKS => 'LEN(TRIM(%s))=0',
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
            self::EXPRESSIONS[$this->inverse ? Wizard::BLANKS : Wizard::NOT_BLANKS],
            $this->referenceCell
        );
    }

    public function getConditional(): Conditional
    {
        $this->getExpression();

        $conditional = new Conditional();
        $conditional->setConditionType(
            $this->inverse ? Conditional::CONDITION_CONTAINSBLANKS : Conditional::CONDITION_NOTCONTAINSBLANKS
        );
        $conditional->setConditions([$this->expression]);
        $conditional->setStyle($this->getStyle());
        $conditional->setStopIfTrue($this->getStopIfTrue());

        return $conditional;
    }

    public static function fromConditional(Conditional $conditional, string $cellRange = 'A1'): WizardInterface
    {
        if (
            $conditional->getConditionType() !== Conditional::CONDITION_CONTAINSBLANKS &&
            $conditional->getConditionType() !== Conditional::CONDITION_NOTCONTAINSBLANKS
        ) {
            throw new Exception('Conditional is not a Blanks CF Rule conditional');
        }

        $wizard = new self($cellRange);
        $wizard->style = $conditional->getStyle();
        $wizard->stopIfTrue = $conditional->getStopIfTrue();
        $wizard->inverse = $conditional->getConditionType() === Conditional::CONDITION_CONTAINSBLANKS;

        return $wizard;
    }

    /**
     * @param string $methodName
     * @param mixed[] $arguments
     */
    public function __call($methodName, $arguments): self
    {
        if (!array_key_exists($methodName, self::OPERATORS)) {
            throw new Exception('Invalid Operation for Blanks CF Rule Wizard');
        }

        $this->inverse(self::OPERATORS[$methodName]);

        return $this;
    }
}
