<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class ErrorWizardTest extends TestCase
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

    public function testErrorWizard(): void
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

    public function testNonErrorWizard(): void
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

    public function testErrorWizardNotError(): void
    {
        $ruleType = Wizard::ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->notError();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(C3))'], $conditions);
    }

    public function testErrorWizardIsError(): void
    {
        $ruleType = Wizard::NOT_ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->isError();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['ISERROR(C3)'], $conditions);
    }
}
