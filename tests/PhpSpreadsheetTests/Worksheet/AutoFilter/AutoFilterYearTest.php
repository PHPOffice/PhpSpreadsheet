<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PHPUnit\Framework\TestCase;

class AutoFilterYearTest extends TestCase
{
    public function providerYear(): array
    {
        return [
            [[5, 6, 7], Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR],
            [[2, 3, 4], Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR],
            [[8, 9, 10], Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR],
            [[], Rule::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2],
            [[2, 5, 8, 11], Rule::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1],
            [[4, 7, 10], Rule::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11],
        ];
    }

    /**
     * @dataProvider providerYear
     */
    public function testYears(array $expectedVisible, string $rule): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same day.
        do {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $dtStart = new DateTimeImmutable();
            $startDay = (int) $dtStart->format('d');
            $sheet->getCell('A1')->setValue('Date');
            $year = (int) $dtStart->format('Y') - 1;
            $row = 1;
            $iteration = 0;
            while ($iteration < 3) {
                for ($month = 3; $month < 13; $month += 4) {
                    ++$row;
                    $sheet->getCell("A$row")->setValue("=DATE($year, $month, 1)");
                }
                ++$year;
                ++$iteration;
            }
            ++$row;
            $sheet->getCell("A$row")->setValue('=DATE(2041, 1, 1)'); // beyond epoch
            $maxRow = $row;
            $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
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
            $autoFilter->showHideRows();
            $dtEnd = new DateTimeImmutable();
            $endDay = (int) $dtEnd->format('d');
        } while ($startDay !== $endDay);
        $actualVisible = [];
        for ($row = 2; $row <= $maxRow; ++$row) {
            if ($sheet->getRowDimension($row)->getVisible()) {
                $actualVisible[] = $row;
            }
        }
        self::assertEquals($expectedVisible, $actualVisible);
    }

    public function testYearToDate(): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same day.
        do {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $dtStart = new DateTimeImmutable();
            $startDay = (int) $dtStart->format('d');
            $startMonth = (int) $dtStart->format('m');
            $sheet->getCell('A1')->setValue('Date');
            $sheet->getCell('A2')->setValue('=TODAY()');
            $sheet->getCell('A3')->setValue('=DATE(YEAR(A2), 12, 31)');
            $sheet->getCell('A4')->setValue('=A3 + 1');
            $sheet->getCell('A5')->setValue('=DATE(YEAR(A2), 1, 1)');
            $sheet->getCell('A6')->setValue('=A5 - 1');

            $maxRow = 6;
            $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
            $autoFilter->setRange("A1:A$maxRow");
            $columnFilter = $autoFilter->getColumn('A');
            $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER);
            $columnFilter->createRule()
                ->setRule(
                    Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
                    '',
                    Rule::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE
                )
                ->setRuleType(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER);
            $autoFilter->showHideRows();
            $dtEnd = new DateTimeImmutable();
            $endDay = (int) $dtEnd->format('d');
        } while ($startDay !== $endDay);
        $actualVisible = [];
        for ($row = 2; $row <= $maxRow; ++$row) {
            if ($sheet->getRowDimension($row)->getVisible()) {
                $actualVisible[] = $row;
            }
        }
        $expected = ($startMonth === 12 && $startDay === 31) ? [2, 3, 5] : [2, 5];
        self::assertEquals($expected, $actualVisible);
    }
}
