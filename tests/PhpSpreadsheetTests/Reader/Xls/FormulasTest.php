<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class FormulasTest extends AbstractFunctional
{
    public function testFormulas(): void
    {
        $filename = 'tests/data/Reader/XLS/formulas.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $originalArray = $sheet->toArray(null, false, false, false);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        $newArray = $newWorksheet->toArray(null, false, false, false);
        self::assertSame($originalArray, $newArray);
        $newSpreadsheet->disconnectWorksheets();
    }

    public function testDatabaseFormulas(): void
    {
        $filename = 'tests/data/Reader/XLS/formulas.database.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $originalArray = $sheet->toArray(null, false, false, false);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        $newArray = $newWorksheet->toArray(null, false, false, false);
        self::assertSame($originalArray, $newArray);
        $newSpreadsheet->disconnectWorksheets();
    }
}
