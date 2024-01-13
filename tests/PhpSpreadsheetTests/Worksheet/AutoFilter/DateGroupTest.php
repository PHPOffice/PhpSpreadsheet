<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DateGroupTest extends SetupTeardown
{
    protected function initSheet(int $year): Worksheet
    {
        $sheet = $this->getSheet();
        $sheet->getCell('A1')->setValue('Date');
        $sheet->getCell('B1')->setValue('Time');
        $sheet->getCell('C1')->setValue('DateTime');
        $sheet->getCell('C1')->setValue('Row*10');
        for ($row = 2; $row < 63; ++$row) {
            $sheet->getCell("A$row")->setValue("=DATE($year,11,30)+$row");
            $hour = $row % 24;
            $minute = $row % 10;
            $second = $row % 20;
            $sheet->getCell("B$row")->setValue("=TIME($hour,$minute,$second)");
            $sheet->getCell("C$row")->setValue("=A$row+B$row");
            $sheet->getCell("D$row")->setValue("=10*$row");
        }
        $this->maxRow = $maxRow = 62;
        $autoFilter = $sheet->getAutoFilter();
        $autoFilter->setRange("A1:C$maxRow");

        return $sheet;
    }

    public function testYearMonthDayGroup(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('C');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => $year,
                    'month' => 12,
                    'day' => 6,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([6], $this->getVisible());
    }

    public function testYearMonthDayHourMinuteSecond1Group(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('C');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => $year,
                    'month' => 12,
                    'day' => 6,
                    'hour' => 6,
                    'minute' => 6,
                    'second' => 6,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        $sheet->getCell('C5')->setValue(''); // make an empty cell in range
        self::assertEquals([6], $this->getVisible());
    }

    public function testYearMonthDayHourMinuteSecond2Group(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('C');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => $year,
                    'month' => 12,
                    'day' => 6,
                    'hour' => 6,
                    'minute' => 6,
                    'second' => 7,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([], $this->getVisible());
    }

    public function testDayGroupEpoch(): void
    {
        $year = 2040;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('C');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'year' => $year,
                    'month' => 12,
                    'day' => 6,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([6], $this->getVisible());
    }

    public function testDayGroupNonArray(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        /** @var int|string */
        $cellA2 = $sheet->getCell('A2')->getCalculatedValue();
        $columnFilter = $sheet->getAutoFilter()->getColumn('C');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                $cellA2
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([], $this->getVisible());
    }

    public function testHourGroup(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('B');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'hour' => 14,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([14, 38, 62], $this->getVisible());
    }

    public function testHourMinuteGroup(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('B');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'hour' => 14,
                    'minute' => 8,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([38], $this->getVisible());
    }

    public function testHourMinuteSecondGroup1(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('B');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'hour' => 14,
                    'minute' => 8,
                    'second' => 18,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([38], $this->getVisible());
    }

    public function testHourMinuteSecondGroup2(): void
    {
        $year = 2011;
        $sheet = $this->initSheet($year);
        $columnFilter = $sheet->getAutoFilter()->getColumn('B');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()
            ->setRule(
                Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                [
                    'hour' => 14,
                    'minute' => 8,
                    'second' => 19,
                ]
            )
            ->setRuleType(
                Rule::AUTOFILTER_RULETYPE_DATEGROUP
            );
        self::assertEquals([], $this->getVisible());
    }
}
