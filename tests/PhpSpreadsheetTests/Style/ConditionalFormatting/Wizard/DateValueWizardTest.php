<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting\Wizard;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PHPUnit\Framework\TestCase;

class DateValueWizardTest extends TestCase
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

    /**
     * @dataProvider dateValueWizardProvider
     */
    public function testDateValueWizard(string $operator, string $expectedReference, string $expectedExpression): void
    {
        $ruleType = Wizard::DATES_OCCURRING;
        /** @var Wizard\DateValue $dateWizard */
        $dateWizard = $this->wizardFactory->newRule($ruleType);
        $dateWizard->setStyle($this->style);

        $dateWizard->$operator();

        $conditional = $dateWizard->getConditional();
        self::assertSame(Conditional::CONDITION_TIMEPERIOD, $conditional->getConditionType());
        self:self::assertSame($expectedReference, $conditional->getText());
        $conditions = $conditional->getConditions();
        self::assertSame([$expectedExpression], $conditions);

        $newWizard = Wizard::fromConditional($conditional, $this->range);
        $newWizard->getConditional();
        self::assertEquals($newWizard, $dateWizard, 'fromConditional() Failure');
    }

    public static function dateValueWizardProvider(): array
    {
        return [
            ['today', 'today', 'FLOOR(C3,1)=TODAY()'],
            ['yesterday', 'yesterday', 'FLOOR(C3,1)=TODAY()-1'],
            ['tomorrow', 'tomorrow', 'FLOOR(C3,1)=TODAY()+1'],
            ['lastSevenDays', 'last7Days', 'AND(TODAY()-FLOOR(C3,1)<=6,FLOOR(C3,1)<=TODAY())'],
        ];
    }

    public function testInvalidFromConditional(): void
    {
        $ruleType = 'Unknown';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Conditional is not a Date Value CF Rule conditional');

        $conditional = new Conditional();
        $conditional->setConditionType($ruleType);
        Wizard\DateValue::fromConditional($conditional);
    }
}
