<?php

namespace PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

use Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;

/**
 * @method Blanks notBlank()
 * @method Blanks isBlank()
 */
class Blanks extends WizardAbstract
{
    protected const OPERATORS = [
        'notBlank' => false,
        'isBlank' => true,
    ];

    private const EXPRESSIONS = [
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
        if (!array_key_exists($methodName, self::OPERATORS)) {
            throw new Exception('Invalid Operation for Blanks CF Rule Wizard');
        }

        $this->inverse(self::OPERATORS[$methodName]);

        return $this;
    }
}
