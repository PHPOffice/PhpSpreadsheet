<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\DataProvider;

class RuleCustom2Test extends SetupTeardown
{
    protected function initSheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Heading');
        $sheet->getCell('A2')->setValue(2);
        //$sheet->getCell('A3')->setValue(3);
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

    /** @param int[] $expectedResult */
    #[DataProvider('providerCondition')]
    public function testComparisonToNullString(array $expectedResult, string $condition): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER);
        $columnFilter->createRule()
            ->setRule(
                $condition,
                ''
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
        self::assertEquals($expectedResult, $this->getVisible());
    }

    /** @return array<array{int[], string}> */
    public static function providerCondition(): array
    {
        // Comparing to null-string, equal and notequal work as expected.
        // Other conditions filter everything.
        return [
            [[3], Rule::AUTOFILTER_COLUMN_RULE_EQUAL],
            [[2, 4], Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL],
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN],
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL],
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN],
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL],
        ];
    }
}
