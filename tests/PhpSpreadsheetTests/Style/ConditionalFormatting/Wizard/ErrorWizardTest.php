<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class ErrorWizardTest extends TestCase
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

    public function testErrorWizardNot()
    {
        $ruleType = Wizard::ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Errors::class, $wizard);

        $wizard->not();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(C3))'], $conditions);
    }
}
