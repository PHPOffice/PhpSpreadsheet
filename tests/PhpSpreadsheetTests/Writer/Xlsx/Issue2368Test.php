<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PHPUnit\Framework\TestCase;

class Issue2368Test extends TestCase
{
    public function testBoolWrite(): void
    {
        // DataValidations were incorrectly written twice.
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $validation = $sheet->getDataValidation('A1:A10');
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setShowDropDown(true);
        $validation->setFormula1('"Option 1, Option 2"');

        $outputFilename = File::temporaryFilename();
        $writer = new Writer($spreadsheet);
        $writer->save($outputFilename);
        $zipfile = "zip://$outputFilename#xl/worksheets/sheet1.xml";
        $contents = file_get_contents($zipfile);
        unlink($outputFilename);
        $spreadsheet->disconnectWorksheets();
        if ($contents === false) {
            self::fail('Unable to open file');
        } else {
            self::assertSame(0, substr_count($contents, '<extLst>'));
            self::assertSame(2, substr_count($contents, 'dataValidations')); // start and end tags
        }
    }
}
