<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\AutoFilter;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class AutoFilterQuarterTest extends TestCase
{
    public function providerQuarter(): array
    {
        return [
            [[2, 3], Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER],
            [[4], Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER],
            [[6], Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER],
        ];
    }

    private static function setCells(Worksheet $sheet, int $startMonth): void
    {
        $sheet->getCell('A1')->setValue('Date');
        $sheet->getCell('A2')->setValue('=TODAY()');
        $sheet->getCell('A3')->setValue('=DATE(YEAR(A2), MONTH(A2), 1)');
        $sheet->getCell('A4')->setValue('=DATE(YEAR(A2), MONTH(A2) + 3, 1)');
        $sheet->getCell('A5')->setValue('=DATE(YEAR(A2), MONTH(A2) + 6, 1)');
        $sheet->getCell('A6')->setValue('=DATE(YEAR(A2), MONTH(A2) - 3, 1)');
        $sheet->getCell('A7')->setValue('=DATE(YEAR(A2), MONTH(A2) - 6, 1)');
        $sheet->getCell('A8')->setValue('=DATE(YEAR(A2) + 1, MONTH(A2), 1)');
        $sheet->getCell('A9')->setValue('=DATE(YEAR(A2) - 1, MONTH(A2), 1)');
    }

    /**
     * @dataProvider providerQuarter
     */
    public function testQuarters(array $expectedVisible, string $rule): void
    {
        // Loop to avoid rare edge case where first calculation
        // and second do not take place in same day.
        do {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $dtStart = new DateTimeImmutable();
            $startDay = (int) $dtStart->format('d');
            $startMonth = (int) $dtStart->format('m');
            self::setCells($sheet, $startMonth);

            $maxRow = 9;
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
}
