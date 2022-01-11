<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class BlankWizardTest extends TestCase
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

    public function testBlankWizardWithNot()
    {
        $ruleType = Wizard::BLANKS;
        /** @var Wizard\Blanks $wizard */
        $wizard = $this->wizardFactory->newRule($ruleType);
        self::assertInstanceOf(Wizard\Blanks::class, $wizard);

        $wizard->not();

        $conditional = $wizard->getConditional();
        self::assertSame(Conditional::CONDITION_NOTCONTAINSBLANKS, $conditional->getConditionType());
        $conditions = $conditional->getConditions();
        self::assertSame(['LEN(TRIM(C3))>0'], $conditions);
    }

}
