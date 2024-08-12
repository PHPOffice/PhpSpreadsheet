<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class ErrorWizardTest extends TestCase
{
    protected Style $style;

    protected string $range = '$C$3:$E$5';

    protected Wizard $wizardFactory;

    protected function setUp(): void
    {
        $this->wizardFactory = new Wizard($this->range);
        $this->style = new Style();
    }

    public function testErrorWizard(): void
    {
        $ruleType = Wizard::ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Errors::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['ISERROR(C3)'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testNonErrorWizard(): void
    {
        $ruleType = Wizard::NOT_ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Errors::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(C3))'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testErrorWizardNotError(): void
    {
        $ruleType = Wizard::ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->notError();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['NOT(ISERROR(C3))'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testErrorWizardIsError(): void
    {
        $ruleType = Wizard::NOT_ERRORS;
        /** @var Wizard\Errors $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->isError();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSERRORS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['ISERROR(C3)'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not an Errors CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\Errors::fromConditional($conditional);
    }
}
