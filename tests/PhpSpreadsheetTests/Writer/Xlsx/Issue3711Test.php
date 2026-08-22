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
        // Issue 3711 - float being used as index in StringTable.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('21.5');
        self::assertSame(21.5, $sheet->getCell('A1')->getValue());
        $sheet->getCell('A1')->setDataType(DataType::TYPE_STRING);
        $sheet->getCell('A2')->setValue('21');
        self::assertSame(21, $sheet->getCell('A2')->getValue());
        $sheet->getCell('A2')->setDataType(DataType::TYPE_STRING);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertSame('21.5', $rsheet->getCell('A1')->getValue());
        self::assertSame('21', $rsheet->getCell('A2')->getValue());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
