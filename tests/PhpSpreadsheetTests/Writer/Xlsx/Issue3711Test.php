<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class Issue3711Test extends AbstractFunctional
{
    public function testIssue3711(): void
    {
        // Issue 3443 - float being used as index in StringTable.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('21.8');
        $sheet->getCell('A1')->setDataType(DataType::TYPE_STRING);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('21.8', $rsheet->getCell('A1')->getValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
