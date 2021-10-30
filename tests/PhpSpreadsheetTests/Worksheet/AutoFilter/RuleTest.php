<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as SpException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RuleTest extends SetupTeardown
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

    public function testRule(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $autoFilterRuleObject = new Rule($columnFilter);
        self::assertEquals(Rule::AUTOFILTER_RULETYPE_FILTER, $autoFilterRuleObject->getRuleType());
        self::assertEquals([3], $this->getVisible());
        $ruleParent = $autoFilterRuleObject->getParent();
        if ($ruleParent === null) {
            self::fail('Unexpected null parent');
        } else {
            self::assertEquals('A', $ruleParent->getColumnIndex());
            self::assertSame($columnFilter, $ruleParent);
        }
    }

    public function testSetParent(): void
    {
        $sheet = $this->initSheet();
        $autoFilterRuleObject = new Rule();
        $autoFilterRuleObject->setParent($sheet->getAutoFilter()->getColumn('B'));
        $columnFilter = $autoFilterRuleObject->getParent();
        if ($columnFilter === null) {
            self::fail('Unexpected null parent');
        } else {
            $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
            $columnFilter->createRule()
                ->setRule(
                    Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                    3
                );
            self::assertEquals(Rule::AUTOFILTER_RULETYPE_FILTER, $autoFilterRuleObject->getRuleType());
            self::assertEquals([4], $this->getVisible());
        }
    }

    public function testBadSetRule(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('Invalid operator for column AutoFilter Rule.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                'xyz',
                3
            );
    }

    public function testBadSetGrouping(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('Invalid grouping for column AutoFilter Rule.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                '',
                3
            );
        $autoFilterRuleObject = new Rule($columnFilter);
        $autoFilterRuleObject->setGrouping('xyz');
    }

    public function testClone(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                3
            );
        $autoFilterRuleObject = new Rule($columnFilter);
        $result = clone $autoFilterRuleObject;
        self::assertSame($autoFilterRuleObject->getRuleType(), $result->getRuleType());
        self::assertSame($autoFilterRuleObject->getValue(), $result->getValue());
        self::assertSame($autoFilterRuleObject->getRuleType(), $result->getRuleType());
        self::assertSame($autoFilterRuleObject->getOperator(), $result->getOperator());
        self::assertSame($autoFilterRuleObject->getGrouping(), $result->getGrouping());
        self::assertNotNull($autoFilterRuleObject->getParent());
        self::assertNull($result->getParent());
    }
}
