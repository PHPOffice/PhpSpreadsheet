<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Exception as SpException;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RuleDateGroupTest extends SetupTeardown
{
    protected function initSheet(): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Date');
        $sheet->getCell('A2')->setValue('=DATE(2011,1,10)');
        $sheet->getCell('A3')->setValue('=DATE(2012,1,10)');
        $sheet->getCell('A4')->setValue('=DATE(2011,1,10)');
        $sheet->getCell('A5')->setValue('=DATE(2012,2,10)');
        $sheet->getCell('A6')->setValue('=DATE(2012,1,1)');
        $sheet->getCell('A7')->setValue('=DATE(2012,12,31)');
        $sheet->getCell('B1')->setValue('Heading2');
        $sheet->getCell('B2')->setValue(1);
        $sheet->getCell('B3')->setValue(2);
        $sheet->getCell('B4')->setValue(3);
        $sheet->getCell('B5')->setValue(4);
        $sheet->getCell('B6')->setValue(5);
        $sheet->getCell('B7')->setValue(6);
        $this->maxRow = $maxRow = 7;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:B$maxRow");

        return $sheet;
    }

    public function testYearGroup(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => 2012,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([3, 5, 6, 7], $this->getVisible());
    }

    public function testYearGroupWithInvalidIndex(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => 2012,
                    'xyz' => 5,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([3, 5, 6, 7], $this->getVisible());
    }

    public function testYearGroupNoValidIndexes(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('Invalid rule value for column AutoFilter Rule.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'zzyear' => 2012,
                    'xyz' => 5,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([3, 5, 6, 7], $this->getVisible());
    }

    public function testYearGroupBadRuleType(): void
    {
        $this->expectException(SpException::class);
        $this->expectExceptionMessage('Invalid rule type for column AutoFilter Rule.');
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => 2012,
                ]
            )
            ->setRuleType(
                'xyz'
            );
        self::assertEquals([3, 5, 6, 7], $this->getVisible());
    }

    public function testYearMonthGroup(): void
    {
        $sheet = $this->initSheet();
        $columnFilter = $sheet->getAutoFilter()->getColumn('A');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => 2012,
                    'month' => 1,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([3, 6], $this->getVisible());
    }
}
