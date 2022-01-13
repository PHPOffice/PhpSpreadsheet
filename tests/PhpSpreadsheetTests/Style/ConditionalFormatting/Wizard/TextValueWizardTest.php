<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class TextValueWizardTest extends TestCase
{
    /**
     * @var Wizard
     */
    protected $wizardFactory;

    protected function setUp(): void
    {
        $range = '$C$3:$E$5';
        $this->wizardFactory = new Wizard($range);
    }

    public function testTextContainsWizardWithText(): void
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->contains('LL');

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSTEXT, $conditional->getConditionType());
        self::assertSame(Conditional::OPERATOR_CONTAINSTEXT, $conditional->getOperatorType());
        self::assertSame('LL', $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(SEARCH("LL",C3)))'], $conditions);
    }

    public function testTextContainsWizardWithCellReference(): void
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->contains('$A1', Wizard::VALUE_TYPE_CELL);

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSTEXT, $conditional->getConditionType());
        self:self::assertSame(Conditional::OPERATOR_CONTAINSTEXT, $conditional->getOperatorType());
        self::assertSame('$A3', $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(SEARCH($A3,C3)))'], $conditions);
    }

    public function testTextNotContainsWizardWithText(): void
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->doesNotContain('LL');

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSTEXT, $conditional->getConditionType());
        self:self::assertSame(Conditional::OPERATOR_NOTCONTAINS, $conditional->getOperatorType());
        self::assertSame('LL', $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame(['ISERROR(SEARCH("LL",C3))'], $conditions);
    }

    public function testTextBeginsWithWizardWithText(): void
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->beginsWith('LL');

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_BEGINSWITH, $conditional->getConditionType());
        self:self::assertSame(Conditional::OPERATOR_BEGINSWITH, $conditional->getOperatorType());
        self::assertSame('LL', $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEFT(C3,LEN("LL"))="LL"'], $conditions);
    }

    public function testTextEndsWithWizardWithText(): void
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->endsWith('LL');

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_ENDSWITH, $conditional->getConditionType());
        self:self::assertSame(Conditional::OPERATOR_ENDSWITH, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame(['RIGHT(C3,LEN("LL"))="LL"'], $conditions);
    }
}
