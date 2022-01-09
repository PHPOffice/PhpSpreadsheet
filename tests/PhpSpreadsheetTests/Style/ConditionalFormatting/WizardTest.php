<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class WizardTest extends TestCase
{
    /**
     * @var Wizard $wizardFactory
     */
    protected $wizardFactory;

    public function setUp(): void
    {
        $range = '$C$3:$E$5';
        $this->wizardFactory = new Wizard($range);
    }

    /**
     * @dataProvider basicWizardFactoryProvider
     */
    public function testBasicWizardFactory(string $ruleType, string $expectedWizard)
    {
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf($expectedWizard, $wizard);
    }

    public function basicWizardFactoryProvider(): array
    {
        return [
            [Wizard::CELL_VALUE, Wizard\CellValue::class],
            [Wizard::TEXT_VALUE, Wizard\TextValue::class],
            [Wizard::BLANKS, Wizard\Blanks::class],
            [Wizard::NOT_BLANKS, Wizard\Blanks::class],
            [Wizard::ERRORS, Wizard\Errors::class],
            [Wizard::NOT_ERRORS, Wizard\Errors::class],
        ];
    }

    public function testWizardFactoryException()
    {
        $ruleType = 'Unknown';
        self::expectException(\Exception::class);
        $this->wizardFactory->newRule($ruleType);
    }

    public function testBasicCellValueWizard()
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->equals(5);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame(Conditional::OPERATOR_EQUAL, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame([5], $conditions);
    }

    public function testRangeCellValueWizard()
    {
        $ruleType = Wizard::CELL_VALUE;
        /** @var Wizard\CellValue $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        $wizard->between(5)->and(10);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CELLIS, $conditional->getConditionType());
        self::assertSame(Conditional::OPERATOR_BETWEEN, $conditional->getOperatorType());
        $conditions = $conditional->getConditions();
        self::assertSame([5, 10], $conditions);
    }

    public function testTextContainsWizardWithText()
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

    public function testTextContainsWizardWithCellReference()
    {
        $ruleType = Wizard::TEXT_VALUE;
        /** @var Wizard\TextValue $textWizard */
        $textWizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\TextValue::class, $textWizard);

        $textWizard->contains('$A$1', true);

        $conditional = $textWizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSTEXT, $conditional->getConditionType());
        self:self::assertSame(Conditional::OPERATOR_CONTAINSTEXT, $conditional->getOperatorType());
        self::assertSame('', $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(SEARCH($A$1,C3)))'], $conditions);
    }

    public function testTextNotContainsWizardWithText()
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

    public function testTextBeginsWithWizardWithText()
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

    public function testTextEndsWithWizardWithText()
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

    public function testBlankWizard()
    {
        $ruleType = Wizard::BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Blanks::class, $wizard);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))=0'], $conditions);
    }

    public function testNonBlankWizard()
    {
        $ruleType = Wizard::NOT_BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Blanks::class, $wizard);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))>0'], $conditions);
    }

    public function testErrorWizard()
    {
        $ruleType = Wizard::ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Errors::class, $wizard);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['ISERROR(C3)'], $conditions);
    }

    public function testNonErrorWizard()
    {
        $ruleType = Wizard::NOT_ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Errors::class, $wizard);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(C3))'], $conditions);
    }
}
