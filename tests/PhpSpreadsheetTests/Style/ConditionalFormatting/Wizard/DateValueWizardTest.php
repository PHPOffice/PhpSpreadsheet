<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PHPUnit\Framework\TestCase;

class DateValueWizardTest extends TestCase
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
     * @dataProvider dateValueWizardProvider
     */
    public function testDateValueWizard(string $operator, string $expectedReference, string $expectedExpression)
    {
        $ruleType = Wizard::DATES_OCCURING;
        /** @var Wizard\DateValue $wizard */
        $dateWizard = $this->wizardFactory->newRule($ruleType);

        call_user_func([$dateWizard, $operator]);

        $conditional = $dateWizard->getConditional();
        self::assertSame(Conditional::CONDITION_TIMEPERIOD, $conditional->getConditionType());
        self:self::assertSame($expectedReference, $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedExpression], $conditions);
    }

    public function dateValueWizardProvider(): array
    {
        return [
            ['today', 'today', 'FLOOR(C3,1)=TODAY()'],
            ['lastSevenDays', 'last7Days', 'AND(TODAY()-FLOOR(C3,1)<=6,FLOOR(C3,1)<=TODAY())'],
        ];
    }
}
