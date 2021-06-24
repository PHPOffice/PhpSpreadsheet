<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AutoFilterMonthTest extends SetupTeardown
{
    public function providerMonth(): array
    {
        return [
            [[2, 3], Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH],
            [[4], Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH],
            [[6], Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH],
        ];
    }

    private function setCells(Worksheet $sheet, int $startMonth): void
    {
        $sheet->getCell('A1')->setValue('Date');
        $sheet->getCell('A2')->setValue('=TODAY()');
        $sheet->getCell('A3')->setValue('=DATE(YEAR(A2), MONTH(A2), 1)');
        if ($startMonth === 12) {
            $sheet->getCell('A4')->setValue('=DATE(YEAR(A2) + 1, 1, 1)');
            $sheet->getCell('A5')->setValue('=DATE(YEAR(A2) + 1, 2, 1)');
        } elseif ($startMonth === 11) {
            $sheet->getCell('A4')->setValue('=DATE(YEAR(A2), MONTH(A2) + 1, 1)');
            $sheet->getCell('A5')->setValue('=DATE(YEAR(A2) + 1, 1, 1)');
        } else {
            $sheet->getCell('A4')->setValue('=DATE(YEAR(A2), MONTH(A2) + 1, 1)');
            $sheet->getCell('A5')->setValue('=DATE(YEAR(A2), MONTH(A2) + 2, 1)');
        }
        if ($startMonth === 1) {
            $sheet->getCell('A6')->setValue('=DATE(YEAR(A2) - 1, 12, 1)');
            $sheet->getCell('A7')->setValue('=DATE(YEAR(A2) - 1, 10, 1)');
        } elseif ($startMonth === 2) {
            $sheet->getCell('A6')->setValue('=DATE(YEAR(A2), 1, 1)');
            $sheet->getCell('A7')->setValue('=DATE(YEAR(A2) - 1, 12, 1)');
        } else {
            $sheet->getCell('A6')->setValue('=DATE(YEAR(A2), MONTH(A2) - 1, 1)');
            $sheet->getCell('A7')->setValue('=DATE(YEAR(A2), MONTH(A2) - 2, 1)');
        }
        $sheet->getCell('A8')->setValue('=DATE(YEAR(A2) + 1, MONTH(A2), 1)');
        $sheet->getCell('A9')->setValue('=DATE(YEAR(A2) - 1, MONTH(A2), 1)');
        $this->maxRow = 9;
    }

    /**
     * @dataProvider providerMonth
     */
    public function testMonths(array $expectedVisible, string $rule): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same day.
        do {
            $sheet = $this->getSheet();
            $dtStart = new DateTimeImmutable();
            $startDay = (int) $dtStart->format('d');
            $startMonth = (int) $dtStart->format('m');
            $this->setCells($sheet, $startMonth);

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
            $autoFilter->showHideRows();
            $dtEnd = new DateTimeImmutable();
            $endDay = (int) $dtEnd->format('d');
        } while ($startDay !== $endDay);

        self::assertEquals($expectedVisible, $this->getVisible());
    }
}
