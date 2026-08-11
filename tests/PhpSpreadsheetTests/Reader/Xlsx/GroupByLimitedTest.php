<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class GroupByLimitedTest extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/XLSX/excel-groupby-one.xlsx';

    public function testRowBreaks(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(['t' => 'array', 'ref' => 'E3:F7'], $reloadedSheet->getCell('E3')->getFormulaAttributes());
        $expected = [
            ['Design', '$505,000 '],
            ['Development', '$346,000 '],
            ['Marketing', '$491,000 '],
            ['Research', '$573,000 '],
            ['Total', '$1,915,000 '],
            [null, null],
        ];
        self::assertSame($expected, $reloadedSheet->rangeToArray('E3:F8'));
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
