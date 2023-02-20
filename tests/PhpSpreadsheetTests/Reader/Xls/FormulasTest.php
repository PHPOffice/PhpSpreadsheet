<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class FormulasTest extends AbstractFunctional
{
    public function testFormulas(): void
    {
        // This file was created with Excel 365.
        $filename = 'tests/data/Reader/XLS/formulas.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $originalArray = $sheet->toArray(null, false, false, false);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        $newArray = $newWorksheet->toArray(null, false, false, false);
        self::assertEquals($originalArray, $newArray);
        $newSpreadsheet->disconnectWorksheets();
    }

    public function testDatabaseFormulas(): void
    {
        // This file was created with Excel 2003.
        $filename = 'tests/data/Reader/XLS/formulas.database.xls';
        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $originalArray = $sheet->toArray(null, false, false, false);

        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls');
        $spreadsheet->disconnectWorksheets();
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        $newArray = $newWorksheet->toArray(null, false, false, false);
        self::assertEquals($originalArray, $newArray);
        $newSpreadsheet->disconnectWorksheets();
    }

    public function testOtherFormulas(): void
    {
        // This file was created with Excel 2003.
        $filename = 'tests/data/Reader/XLS/formulas.other.xls';
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
