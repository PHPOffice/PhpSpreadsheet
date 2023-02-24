<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as WriterXls;
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

    public static function customizeWriter(WriterXls $writer): void
    {
        $writer->setPreCalculateFormulas(false);
    }

    public function testCaveatEmptor(): void
    {
        // This test confirms only that the 5 problematic functions
        //   in it are parsed correctly.
        // When these are written to an Xls spreadsheet:
        //   Excel is buggy regarding their support; only BAHTTEXT
        //     will work as expected.
        //   LibreOffice handles them without problem.
        //   So does Gnumeric, except it doesn't implement BAHTTEXT.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $originalArray = [
            [1],
            [2],
            ['=INDEX(TRANSPOSE(A1:A2),1,2)'],
            ['=BAHTTEXT(2)'],
            ['=CELL("ADDRESS",A3)'],
            ['=OFFSET(A3,-2,0)'],
            ['=GETPIVOTDATA("Sales",A3)'],
        ];
        $sheet->fromArray($originalArray);

        /** @var callable */
        $writerCustomizer = [self::class, 'customizeWriter'];
        $newSpreadsheet = $this->writeAndReload($spreadsheet, 'Xls', null, $writerCustomizer);
        $spreadsheet->disconnectWorksheets();
        $newWorksheet = $newSpreadsheet->getActiveSheet();
        $newArray = $newWorksheet->toArray(null, false, false, false);
        self::assertSame($originalArray, $newArray);
        $newSpreadsheet->disconnectWorksheets();
    }
}
