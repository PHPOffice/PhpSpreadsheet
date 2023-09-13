<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3613Test extends AbstractFunctional
{
    private static string $testbook = 'tests/data/Reader/XLSX/issue.3613.xlsx';

    // Partial fix only. We will no longer throw exception on save.
    // But calculation for cell value is 0, which is incorrect.
    public function testIssue3613(): void
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load(self::$testbook);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('=ROUND(MAX((O4-P4)*{0.03;0.1;0.2;0.25;0.3;0.35;0.45}-{0;2520;16920;31920;52920;85920;181920},0)-Q4,2)', $sheet->getCell('N4')->getValue());

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
