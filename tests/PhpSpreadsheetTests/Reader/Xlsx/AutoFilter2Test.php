<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class AutoFilter2Test extends TestCase
{
    private const TESTBOOK = 'tests/data/Reader/XLSX/autofilter2.xlsx';

    public function getVisibleSheet(?Worksheet $sheet, int $maxRow): array
    {
        $actualVisible = [];
        if ($sheet !== null) {
            for ($row = 2; $row <= $maxRow; ++$row) {
                if ($sheet->getRowDimension($row)->getVisible()) {
                    $actualVisible[] = $row;
                }
            }
        }

        return $actualVisible;
    }

    public function testReadDateRange(): void
    {
        $spreadsheet = IOFactory::load(self::TESTBOOK);
        $sheet = $spreadsheet->getSheetByNameOrThrow('daterange');
        $filter = $sheet->getAutoFilter();
        $maxRow = 30;
        self::assertSame("A1:A$maxRow", $filter->getRange());
        $columns = $filter->getColumns();
        self::assertCount(1, $columns);
        $column = $columns['A'] ?? null;
        self::assertNotNull($column);
        $ruleset = $column->getRules();
        self::assertCount(1, $ruleset);
        $rule = $ruleset[0];
        self::assertSame(Rule::AUTOFILTER_RULETYPE_DATEGROUP, $rule->getRuleType());
        $value = $rule->getValue();
        self::assertIsArray($value);
        self::assertCount(6, $value);
        self::assertSame('2002', $value['year']);
        self::assertSame('', $value['month']);
        self::assertSame('', $value['day']);
        self::assertSame('', $value['hour']);
        self::assertSame('', $value['minute']);
        self::assertSame('', $value['second']);
        self::assertSame(
            [25, 26, 27, 28, 29, 30],
            $this->getVisibleSheet($sheet, $maxRow)
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadTopTen(): void
    {
        $spreadsheet = IOFactory::load(self::TESTBOOK);
        $sheet = $spreadsheet->getSheetByNameOrThrow('top10');
        $filter = $sheet->getAutoFilter();
        $maxRow = 65;
        self::assertSame("A1:A$maxRow", $filter->getRange());
        $columns = $filter->getColumns();
        self::assertCount(1, $columns);
        $column = $columns['A'] ?? null;
        self::assertNotNull($column);
        $ruleset = $column->getRules();
        self::assertCount(1, $ruleset);
        $rule = $ruleset[0];
        self::assertSame(Rule::AUTOFILTER_RULETYPE_TOPTENFILTER, $rule->getRuleType());
        $value = $rule->getValue();
        self::assertSame('10', $value);
        self::assertSame(
            [56, 57, 58, 59, 60, 61, 62, 63, 64, 65],
            $this->getVisibleSheet($sheet, $maxRow)
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testReadDynamic(): void
    {
        $spreadsheet = IOFactory::load(self::TESTBOOK);
        $sheet = $spreadsheet->getSheetByNameOrThrow('dynamic');
        $filter = $sheet->getAutoFilter();
        $maxRow = 30;
        self::assertSame("A1:A$maxRow", $filter->getRange());
        $columns = $filter->getColumns();
        self::assertCount(1, $columns);
        $column = $columns['A'] ?? null;
        self::assertNotNull($column);
        $ruleset = $column->getRules();
        self::assertCount(1, $ruleset);
        $rule = $ruleset[0];
        self::assertSame(Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER, $rule->getRuleType());
        self::assertSame('M4', $rule->getGrouping());
        self::assertSame(
            [5, 17, 28],
            $this->getVisibleSheet($sheet, $maxRow)
        );
        $spreadsheet->disconnectWorksheets();
    }
}
