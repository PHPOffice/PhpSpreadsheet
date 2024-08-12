<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3552Test extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3552.xlsx';

    public function testRowBreaks(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $sheet = $spreadsheet->getActiveSheet();
        self::assertSame(['A92', 'A184', 'A276', 'A368', 'A417', 'A511', 'A554'], array_keys($sheet->getRowBreaks()));
        $sheet->insertNewRowBefore(397, 1);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $reloadedSheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame(['A92', 'A184', 'A276', 'A368', 'A418', 'A512', 'A555'], array_keys($reloadedSheet->getRowBreaks()));
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testColumnBreaks(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setBreak('Z1', Worksheet::BREAK_COLUMN);
        $sheet->setBreak('H1', Worksheet::BREAK_COLUMN);
        $sheet->setBreak('P1', Worksheet::BREAK_COLUMN);
        self::assertSame(['H1', 'P1', 'Z1'], array_keys($sheet->getColumnBreaks()));
        $sheet->insertNewColumnBefore('N', 2);
        self::assertSame(['H1', 'R1', 'AB1'], array_keys($sheet->getColumnBreaks()));
        $spreadsheet->disconnectWorksheets();
    }
}
