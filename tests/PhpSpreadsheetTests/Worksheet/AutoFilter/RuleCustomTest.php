<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RuleCustomTest extends SetupTeardown
{
    protected function initSheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Heading');
        $sheet->getCell('A2')->setValue(2);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('A4')->setValue(4);
        $sheet->getCell('B1')->setValue('Heading2');
        $sheet->getCell('B2')->setValue(1);
        $sheet->getCell('B3')->setValue(2);
        $sheet->getCell('B4')->setValue(3);
        $this->maxRow = $maxRow = 4;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:B$maxRow");

        return $sheet;
    }

    /**
     * @dataProvider providerCondition
     */
    public function testRuleCondition(array $expectedResult, string $condition): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                $condition,
                3
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        self::assertEquals($expectedResult, $this->getVisible());
    }

    public static function providerCondition(): array
    {
        return [
            [[3], Rule::AUTOFILTER_COLUMN_RULE_EQUAL],
            [[2, 4], Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL],
            [[4], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN],
            [[3, 4], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL],
            [[2], Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN],
            [[2, 3], Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL],
        ];
    }
}
