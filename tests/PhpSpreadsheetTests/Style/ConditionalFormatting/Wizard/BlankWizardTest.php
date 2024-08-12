<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class BlankWizardTest extends TestCase
{
    protected Style $style;

    protected string $range = '$C$3:$E$5';

    protected Wizard $wizardFactory;

    protected function setUp(): void
    {
        $this->wizardFactory = new Wizard($this->range);
        $this->style = new Style();
    }

    public function testBlankWizard(): void
    {
        $ruleType = Wizard::BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Blanks::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))=0'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testNonBlankWizard(): void
    {
        $ruleType = Wizard::NOT_BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Blanks::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))>0'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testBlankWizardWithNotBlank(): void
    {
        $ruleType = Wizard::BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->notBlank();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))>0'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testNonBlankWizardWithIsBlank(): void
    {
        $ruleType = Wizard::NOT_BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->isBlank();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_CONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))=0'], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not a Blanks CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\Blanks::fromConditional($conditional);
    }
}
