<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilterAverageTop10Test extends SetupTeardown
{
    public function initsheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Header');
        $sheet->getCell('A2')->setValue(1);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('A4')->setValue(5);
        $sheet->getCell('A5')->setValue(7);
        $sheet->getCell('A6')->setValue(9);
        $sheet->getCell('A7')->setValue(2);
        $sheet->getCell('A8')->setValue(4);
        $sheet->getCell('A9')->setValue(6);
        $sheet->getCell('A10')->setValue(8);
        $this->maxRow = 10;

        return $sheet;
    }

    public static function providerAverage(): array
    {
        return [
            [[5, 6, 9, 10], Rule::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE],
            [[2, 3, 7, 8], Rule::AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE],
        ];
    }

    /**
     * @dataProvider providerAverage
     */
    public function testAboveAverage(array $expectedVisible, string $rule): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                '',
                $rule
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);

        self::assertEquals($expectedVisible, $this->getVisible());
    }

    public static function providerTop10(): array
    {
        return [
            [[6, 10], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP, 2],
            [[2, 3, 7], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM, 3],
            [[6], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP, 10],
            [[2, 3, 7], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM, 40],
        ];
    }

    /**
     * @dataProvider providerTop10
     */
    public function testTop10(array $expectedVisible, string $rule, string $ruleType, int $count): void
    {
        $sheet = $this->initSheet();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
        $columnFilter->createRule()
            ->setRule(
                $rule,
                $count,
                $ruleType
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);

        self::assertEquals($expectedVisible, $this->getVisible());
    }

    public function initsheetTies(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Header');
        $sheet->getCell('A2')->setValue(1);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('A4')->setValue(3);
        $sheet->getCell('A5')->setValue(7);
        $sheet->getCell('A6')->setValue(9);
        $sheet->getCell('A7')->setValue(4);
        $sheet->getCell('A8')->setValue(4);
        $sheet->getCell('A9')->setValue(8);
        $sheet->getCell('A10')->setValue(8);
        $this->maxRow = 10;

        return $sheet;
    }

    public static function providerTop10Ties(): array
    {
        return [
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM, 2],
            [[2], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM, 1],
            [[5, 6, 7, 8, 9, 10], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP, 5],
            [[6], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP, 1],
            [[2, 3, 4], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM, 25],
            [[6, 9, 10], Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT, Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP, 25],
        ];
    }

    /**
     * @dataProvider providerTop10Ties
     */
    public function testTop10Ties(array $expectedVisible, string $rule, string $ruleType, int $count): void
    {
        $sheet = $this->initSheetTies();
        $maxRow = $this->maxRow;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
        $columnFilter->createRule()
            ->setRule(
                $rule,
                $count,
                $ruleType
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);

        self::assertEquals($expectedVisible, $this->getVisible());
    }

    public function testTop10Exceeds500(): void
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Heading');
        for ($row = 2; $row < 602; ++$row) {
            $sheet->getCell("A$row")->setValue($row);
        }
        $maxRow = $this->maxRow = 601;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:A$maxRow");
        $columnFilter = $autoFilter->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE,
                550,
                Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP
            )
            ->setRuleType(Rule::AUTOFILTER_RULETYPE_TOPTENFILTER);

        self::assertCount(500, $this->getVisible(), 'Top10 Filter limited to 500 items plus ties');
    }
}
