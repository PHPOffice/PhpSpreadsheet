<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PHPUnit\Framework\TestCase;

class AutoFilterTodayTest extends TestCase
{
    public function providerYesterdayTodayTomorrow(): array
    {
        return [
            [[2, 5], Rule::AUTOFILTER_RULETYPE_DYNAMIC_TODAY],
            [[3, 6], Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW],
            [[4, 7], Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY],
        ];
    }

    /**
     * @dataProvider providerYesterdayTodayTomorrow
     */
    public function testYesterdayTodayTomorrow(array $expectedVisible, string $rule): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same day.
        do {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $dtStart = new DateTimeImmutable();
            $startDay = $dtStart->format('d');
            $sheet->getCell('A1')->setValue('Date');
            $sheet->getCell('A2')->setValue('=NOW()');
            $sheet->getCell('A3')->setValue('=A2+1');
            $sheet->getCell('A4')->setValue('=A2-1');
            $sheet->getCell('A5')->setValue('=TODAY()');
            $sheet->getCell('A6')->setValue('=A5+1');
            $sheet->getCell('A7')->setValue('=A5-1');
            $maxRow = 7;
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
            $endDay = $dtEnd->format('d');
        } while ($startDay !== $endDay);
        $actualVisible = [];
        for ($row = 2; $row <= $maxRow; ++$row) {
            if ($sheet->getRowDimension($row)->getVisible()) {
                $actualVisible[] = $row;
            }
        }
        self::assertEquals($expectedVisible, $actualVisible);
    }
}
