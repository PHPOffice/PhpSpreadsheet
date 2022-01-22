<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class DuplicatesWizardTest extends TestCase
{
    /**
     * @var Style
     */
    protected $style;

    /**
     * @var string
     */
    protected $range = '$C$3:$E$5';

    /**
     * @var Wizard
     */
    protected $wizardFactory;

    protected function setUp(): void
    {
        $this->wizardFactory = new Wizard($this->range);
        $this->style = new Style();
    }

    public function testDuplicateWizard(): void
    {
        $ruleType = Wizard::DUPLICATES;
        /** @var Wizard\Duplicates $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Duplicates::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_DUPLICATES, $conditional->getConditionType());

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testUniqueWizard(): void
    {
        $ruleType = Wizard::UNIQUE;
        /** @var Wizard\Duplicates $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Duplicates::class, $wizard);
        $wizard->setStyle($this->style);

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_UNIQUE, $conditional->getConditionType());

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testDuplicateWizardUnique(): void
    {
        $ruleType = Wizard::DUPLICATES;
        /** @var Wizard\Duplicates $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->unique();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_UNIQUE, $conditional->getConditionType());

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testUniqueWizardDuplicates(): void
    {
        $ruleType = Wizard::UNIQUE;
        /** @var Wizard\Duplicates $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);

        $wizard->setStyle($this->style);
        $wizard->duplicates();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_DUPLICATES, $conditional->getConditionType());

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $wizard, 'fromConditional() Failure');
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not a Duplicates CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\Duplicates::fromConditional($conditional);
    }
}
